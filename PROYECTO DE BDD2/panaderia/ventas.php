<?php
require_once 'conexion.php';
$msg=''; $err='';
$panes=q($conexion,"SELECT id_pan,nombre,precio_venta,stock FROM pan ORDER BY nombre");
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['accion']??'')==='vender'){
  $id_pan=(int)($_POST['id_pan']??0); $cant=(int)($_POST['cantidad']??0); $metodo=trim($_POST['metodo_pago']??'Efectivo');
  if($id_pan<=0||$cant<=0){ $err='Datos de venta inválidos.'; }
  else {
    // Intentar usar el procedimiento almacenado si existe
    $st=$conexion->prepare("CALL sp_registrar_venta_segura(?,?,?)");
    if($st){
      $st->bind_param('iis',$id_pan,$cant,$metodo);
      if($st->execute()){
        $res=$st->get_result(); $row=$res?$res->fetch_assoc():null; $msg=$row?($row['resultado']??'Venta registrada.'):'Venta registrada.';
        while($conexion->more_results() && $conexion->next_result()){ if($extra=$conexion->store_result()) $extra->free(); }
      } else { $err='Error al vender: '.$st->error; }
      $st->close();
    } else {
      // Fallback transaccional manual
      $conexion->begin_transaction();
      try{
        $rp=$conexion->prepare("SELECT stock,precio_venta FROM pan WHERE id_pan=? FOR UPDATE"); $rp->bind_param('i',$id_pan); $rp->execute();
        $p=$rp->get_result()->fetch_assoc(); $rp->close();
        if(!$p || (int)$p['stock'] < $cant) throw new Exception('Stock insuficiente.');
        $precio=(float)$p['precio_venta']; $total=$precio*$cant;
        $up=$conexion->prepare("UPDATE pan SET stock=stock-? WHERE id_pan=?"); $up->bind_param('ii',$cant,$id_pan); $up->execute(); $up->close();
        $iv=$conexion->prepare("INSERT INTO venta(fecha,total,metodo_pago) VALUES (CURDATE(),?,?)"); $iv->bind_param('ds',$total,$metodo); $iv->execute(); $idv=$conexion->insert_id; $iv->close();
        $id=$conexion->prepare("INSERT INTO detalle_venta(id_venta,id_pan,cantidad,precio_unitario) VALUES (?,?,?,?)"); $id->bind_param('iiid',$idv,$id_pan,$cant,$precio); $id->execute(); $id->close();
        $conexion->commit(); $msg='Venta registrada correctamente.';
      }catch(Exception $e){ $conexion->rollback(); $err=$e->getMessage(); }
    }
  }
}
$ventas=q($conexion,"SELECT v.id_venta,v.fecha,v.metodo_pago,v.total,COUNT(d.id_detalle_venta) items FROM venta v LEFT JOIN detalle_venta d ON v.id_venta=d.id_venta GROUP BY v.id_venta ORDER BY v.fecha DESC,v.id_venta DESC");
$detalle=q($conexion,"SELECT v.id_venta,v.fecha,p.nombre pan,d.cantidad,d.precio_unitario,(d.cantidad*d.precio_unitario) subtotal,v.metodo_pago FROM venta v JOIN detalle_venta d ON v.id_venta=d.id_venta JOIN pan p ON d.id_pan=p.id_pan ORDER BY v.id_venta DESC,d.id_detalle_venta DESC LIMIT 20");
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Ventas - Panadería</title><style>
:root {
  --bg: #fff8f1;
  --card: #ffffff;
  --ink: #2b2118;
  --muted: #7a6756;
  --brand: #b45309;
  --brand2: #d97706;
  --accent: #16a34a;
  --danger: #dc2626;
  --line: #eadfce;
  --shadow: 0 10px 30px rgba(92, 55, 20, .12);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: "Segoe UI", Tahoma, sans-serif;
  color: var(--ink);
  background:
    radial-gradient(circle at 10% 10%, #fde7c7 0, transparent 25%),
    radial-gradient(circle at 90% 0%, #f7d9b5 0, transparent 22%),
    var(--bg);
  min-height: 100vh;
}

header {
  position: sticky;
  top: 0;
  z-index: 10;
  backdrop-filter: blur(8px);
  background: rgba(255, 248, 241, .9);
  border-bottom: 1px solid var(--line);
}

.topbar {
  max-width: 1200px;
  margin: 0 auto;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--brand), var(--brand2));
  color: #fff;
  display: grid;
  place-items: center;
  font-size: 22px;
  box-shadow: var(--shadow);
}

.brand h1 {
  font-size: 1.2rem;
  line-height: 1.1;
}

.brand span {
  color: var(--muted);
  font-size: .85rem;
}

nav {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

nav a {
  text-decoration: none;
  color: var(--ink);
  padding: 8px 12px;
  border-radius: 999px;
  border: 1px solid transparent;
  font-weight: 600;
  font-size: .92rem;
}

nav a:hover,
nav a.activo {
  background: #fff;
  border-color: var(--line);
  color: var(--brand);
}

.hero {
  max-width: 1200px;
  margin: 22px auto 0;
  padding: 0 20px;
}

.hero-card {
  background: linear-gradient(120deg, #7c2d12, #b45309 55%, #d97706);
  color: #fff;
  border-radius: 22px;
  padding: 28px;
  box-shadow: var(--shadow);
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 20px;
  align-items: center;
}

.hero h2 {
  font-size: 2rem;
  margin-bottom: 8px;
}

.hero p {
  color: #fdebd3;
}

.hero .chips {
  margin-top: 14px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.chip {
  background: rgba(255, 255, 255, .15);
  border: 1px solid rgba(255, 255, 255, .25);
  padding: 6px 10px;
  border-radius: 999px;
  font-size: .85rem;
}

.contenedor {
  max-width: 1200px;
  margin: 22px auto;
  padding: 0 20px 30px;
}

.grid {
  display: grid;
  gap: 16px;
}

.grid.kpi {
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  margin-bottom: 18px;
}

.card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 16px;
  padding: 18px;
  box-shadow: var(--shadow);
}

.kpi .label {
  color: var(--muted);
  font-size: .85rem;
}

.kpi .value {
  font-size: 1.8rem;
  font-weight: 800;
  margin-top: 4px;
  color: var(--brand);
}

.kpi small {
  color: var(--muted);
}

.section-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 8px 0 12px;
  gap: 10px;
  flex-wrap: wrap;
}

.section-title h3 {
  font-size: 1.2rem;
}

.badge {
  background: #fff3e6;
  color: var(--brand);
  border: 1px solid #f3d3a8;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: .8rem;
  font-weight: 600;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 11px 10px;
  border-bottom: 1px solid var(--line);
  text-align: left;
  font-size: .93rem;
}

th {
  background: #fff7ed;
  color: #7c2d12;
  font-size: .85rem;
  text-transform: uppercase;
  letter-spacing: .03em;
}

tr:hover td {
  background: #fffaf4;
}

.pill {
  padding: 4px 9px;
  border-radius: 999px;
  font-size: .78rem;
  font-weight: 700;
  display: inline-block;
}

.ok {
  background: #dcfce7;
  color: #166534;
}

.warn {
  background: #fef3c7;
  color: #92400e;
}

.bad {
  background: #fee2e2;
  color: #991b1b;
}

.layout {
  display: grid;
  grid-template-columns: 1.2fr .8fr;
  gap: 16px;
}

form .row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

label {
  display: block;
  font-size: .85rem;
  color: var(--muted);
  margin: 8px 0 4px;
  font-weight: 600;
}

input,
select,
textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #dccdb8;
  border-radius: 10px;
  font: inherit;
  background: #fffdf9;
}

textarea {
  min-height: 70px;
  resize: vertical;
}

button,
.btn {
  background: linear-gradient(135deg, var(--brand), var(--brand2));
  color: #fff;
  border: none;
  padding: 10px 14px;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
}

button:hover,
.btn:hover {
  filter: brightness(1.05);
}

.alert {
  padding: 12px 14px;
  border-radius: 10px;
  margin-bottom: 12px;
  font-weight: 600;
}

.alert.ok {
  background: #dcfce7;
  color: #166534;
  border: 1px solid #86efac;
}

.alert.err {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

footer {
  text-align: center;
  color: var(--muted);
  padding: 20px;
  font-size: .9rem;
}

.muted {
  color: var(--muted);
}

code {
  background: #fff3e6;
  padding: 2px 6px;
  border-radius: 6px;
}

@media (max-width: 900px) {
  .hero-card,
  .layout {
    grid-template-columns: 1fr;
  }

  .hero h2 {
    font-size: 1.6rem;
  }

  form .row {
    grid-template-columns: 1fr;
  }
}
  </style></head><body>
<header><div class="topbar"><div class="brand"><div class="logo">💰</div><div><h1>Panadería Delicias</h1><span>Ventas</span></div></div>
<nav><a href="index.php">Panel</a><a href="productos.php">Panes</a><a href="ingredientes.php">Ingredientes</a><a href="recetas.php">Recetas</a><a href="produccion.php">Producción</a><a class="activo" href="ventas.php">Ventas</a><a href="compras.php">Compras</a><a href="proveedores.php">Proveedores</a><a href="auditoria.php">Bitácora</a></nav></div></header>
<div class="contenedor">
  <div class="section-title"><h3>Registro de ventas</h3><span class="badge">venta + detalle_venta + sp_registrar_venta_segura</span></div>
  <?php if($msg): ?><div class="alert ok"><?= h($msg) ?></div><?php endif; ?><?php if($err): ?><div class="alert err"><?= h($err) ?></div><?php endif; ?>
  <div class="layout">
    <div class="card"><h3 style="margin-bottom:8px">Historial de ventas</h3>
      <table><thead><tr><th>#</th><th>Fecha</th><th>Método</th><th>Ítems</th><th>Total</th></tr></thead><tbody>
      <?php if($ventas&&$ventas->num_rows): while($r=$ventas->fetch_assoc()): ?>
        <tr><td>#<?= h($r['id_venta']) ?></td><td><?= h($r['fecha']) ?></td><td><?= h($r['metodo_pago']) ?></td><td><?= h($r['items']) ?></td><td><strong>Bs <?= number_format((float)$r['total'],2) ?></strong></td></tr>
      <?php endwhile; else: ?><tr><td colspan="5" class="muted">Sin ventas.</td></tr><?php endif; ?>
      </tbody></table>
    </div>
    <div class="card"><h3 style="margin-bottom:8px">Nueva venta</h3>
      <form method="post"><input type="hidden" name="accion" value="vender">
        <label>Pan</label><select name="id_pan" required><option value="">-- Seleccione --</option>
          <?php if($panes) while($p=$panes->fetch_assoc()): ?>
            <option value="<?= h($p['id_pan']) ?>"><?= h($p['nombre']) ?> — Bs <?= number_format((float)$p['precio_venta'],2) ?> (stock: <?= h($p['stock']) ?>)</option>
          <?php endwhile; ?>
        </select>
        <div class="row"><div><label>Cantidad</label><input type="number" min="1" name="cantidad" required></div>
        <div><label>Método de pago</label><select name="metodo_pago"><option>Efectivo</option><option>Tarjeta</option><option>QR / Transferencia</option></select></div></div>
        <div style="margin-top:12px"><button type="submit">Registrar venta</button></div>
      </form>
      <p class="muted" style="margin-top:10px;font-size:.85rem">Descuenta stock automáticamente (procedimiento o transacción segura).</p>
    </div>
  </div>
  <div class="card" style="margin-top:16px">
    <div class="section-title"><h3>Detalle reciente</h3><span class="badge">vw_ventas_detalle</span></div>
    <table><thead><tr><th>Venta</th><th>Fecha</th><th>Pan</th><th>Cant.</th><th>P. Unit.</th><th>Subtotal</th><th>Método</th></tr></thead><tbody>
      <?php if($detalle&&$detalle->num_rows): while($r=$detalle->fetch_assoc()): ?>
      <tr><td>#<?= h($r['id_venta']) ?></td><td><?= h($r['fecha']) ?></td><td><?= h($r['pan']) ?></td><td><?= h($r['cantidad']) ?></td><td>Bs <?= number_format((float)$r['precio_unitario'],2) ?></td><td>Bs <?= number_format((float)$r['subtotal'],2) ?></td><td><?= h($r['metodo_pago']) ?></td></tr>
      <?php endwhile; else: ?><tr><td colspan="7" class="muted">Sin detalle.</td></tr><?php endif; ?>
    </tbody></table>
  </div>
</div>
<footer>Proyecto BDD2 · Panadería</footer></body></html>
<?php $conexion->close(); ?>

