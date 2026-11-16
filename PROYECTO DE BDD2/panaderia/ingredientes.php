<?php
require_once 'conexion.php';
$msg=''; $err='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['accion']??'')==='agregar'){
  $nombre=trim($_POST['nombre']??''); $unidad=trim($_POST['unidad_medida']??''); $stock=(float)($_POST['stock']??0); $precio=(float)($_POST['precio_unitario']??0);
  if($nombre===''||$stock<0||$precio<0){ $err='Datos inválidos.'; }
  else { $st=$conexion->prepare("INSERT INTO ingrediente(nombre,unidad_medida,stock,precio_unitario) VALUES (?,?,?,?)"); $st->bind_param('ssdd',$nombre,$unidad,$stock,$precio);
    if($st->execute()) $msg='Ingrediente registrado.'; else $err='Error: '.$conexion->error; $st->close(); }
}
$rows=q($conexion,"SELECT * FROM ingrediente ORDER BY nombre ASC");
$total=q($conexion,"SELECT COALESCE(SUM(stock*precio_unitario),0) v FROM ingrediente"); $val=$total?$total->fetch_assoc()['v']:0;
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Ingredientes - Panadería</title><style>
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
<header><div class="topbar"><div class="brand"><div class="logo">🌾</div><div><h1>Panadería Delicias</h1><span>Ingredientes e insumos</span></div></div>
<nav><a href="index.php">Panel</a><a href="productos.php">Panes</a><a class="activo" href="ingredientes.php">Ingredientes</a><a href="recetas.php">Recetas</a><a href="produccion.php">Producción</a><a href="ventas.php">Ventas</a><a href="compras.php">Compras</a><a href="proveedores.php">Proveedores</a><a href="auditoria.php">Bitácora</a></nav></div></header>
<div class="contenedor">
  <div class="section-title"><h3>Inventario de ingredientes</h3><span class="badge">Valor: Bs <?= number_format((float)$val,2) ?></span></div>
  <?php if($msg): ?><div class="alert ok"><?= h($msg) ?></div><?php endif; ?><?php if($err): ?><div class="alert err"><?= h($err) ?></div><?php endif; ?>
  <div class="layout">
    <div class="card"><table><thead><tr><th>ID</th><th>Ingrediente</th><th>Unidad</th><th>Stock</th><th>Costo unit.</th><th>Valor</th></tr></thead><tbody>
      <?php if($rows&&$rows->num_rows): while($r=$rows->fetch_assoc()): $v=(float)$r['stock']*(float)$r['precio_unitario']; ?>
      <tr><td>#<?= h($r['id_ingrediente']) ?></td><td><strong><?= h($r['nombre']) ?></strong></td><td><?= h($r['unidad_medida']) ?></td><td><?= number_format((float)$r['stock'],2) ?></td><td>Bs <?= number_format((float)$r['precio_unitario'],2) ?></td><td>Bs <?= number_format($v,2) ?></td></tr>
      <?php endwhile; else: ?><tr><td colspan="6" class="muted">Sin ingredientes.</td></tr><?php endif; ?>
    </tbody></table></div>
    <div class="card"><h3 style="margin-bottom:8px">Nuevo ingrediente</h3>
      <form method="post"><input type="hidden" name="accion" value="agregar">
        <label>Nombre</label><input name="nombre" required>
        <label>Unidad de medida</label><input name="unidad_medida" placeholder="Kg, Litros, Unidades..." required>
        <div class="row"><div><label>Stock</label><input type="number" step="0.01" min="0" name="stock" required></div>
        <div><label>Precio unitario (Bs)</label><input type="number" step="0.01" min="0" name="precio_unitario" required></div></div>
        <div style="margin-top:12px"><button type="submit">Guardar ingrediente</button></div>
      </form>
    </div>
  </div>
</div>
<footer>Proyecto BDD2 · Panadería</footer></body></html>
<?php $conexion->close(); ?>

