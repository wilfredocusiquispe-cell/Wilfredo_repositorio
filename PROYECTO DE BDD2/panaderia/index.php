<?php
require_once 'conexion.php';
function countSafe($c,$sql){ $r=q($c,$sql); if(!$r) return 0; $row=$r->fetch_assoc(); return $row?array_values($row)[0]:0; }
$kpi_panes = countSafe($conexion, "SELECT COUNT(*) FROM pan");
$kpi_stock_panes = countSafe($conexion, "SELECT COALESCE(SUM(stock),0) FROM pan");
$kpi_ing = countSafe($conexion, "SELECT COUNT(*) FROM ingrediente");
$kpi_ventas = countSafe($conexion, "SELECT COALESCE(SUM(total),0) FROM venta");
$kpi_compras = countSafe($conexion, "SELECT COALESCE(SUM(total),0) FROM compra");
$kpi_prod = countSafe($conexion, "SELECT COALESCE(SUM(cantidad),0) FROM produccion");
$ult_ventas = q($conexion, "SELECT v.id_venta, v.fecha, v.total, v.metodo_pago, COUNT(d.id_detalle_venta) items FROM venta v LEFT JOIN detalle_venta d ON v.id_venta=d.id_venta GROUP BY v.id_venta ORDER BY v.fecha DESC, v.id_venta DESC LIMIT 6");
$top_panes = q($conexion, "SELECT p.nombre, COALESCE(SUM(dv.cantidad),0) vendidos, COALESCE(SUM(dv.cantidad*dv.precio_unitario),0) ingreso FROM pan p LEFT JOIN detalle_venta dv ON p.id_pan=dv.id_pan GROUP BY p.id_pan ORDER BY vendidos DESC LIMIT 5");
$stock_bajo = q($conexion, "SELECT nombre, stock, precio_venta FROM pan WHERE stock <= 20 ORDER BY stock ASC LIMIT 5");
$valor_inv = q($conexion, "SELECT (SELECT COALESCE(SUM(stock*precio_venta),0) FROM pan) + (SELECT COALESCE(SUM(stock*precio_unitario),0) FROM ingrediente) AS valor");
$valor = $valor_inv && ($row=$valor_inv->fetch_assoc()) ? $row['valor'] : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panadería - Panel</title>
  <style>
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
  </style>
</head>
<body>
<header><div class="topbar">
  <div class="brand"><div class="logo">🍞</div><div><h1>Panadería Delicias</h1><span>Sistema de gestión · PHP + MySQL</span></div></div>
  <nav>
    <a class="activo" href="index.php">Panel</a>
    <a href="productos.php">Panes</a>
    <a href="ingredientes.php">Ingredientes</a>
    <a href="recetas.php">Recetas</a>
    <a href="produccion.php">Producción</a>
    <a href="ventas.php">Ventas</a>
    <a href="compras.php">Compras</a>
    <a href="proveedores.php">Proveedores</a>
    <a href="auditoria.php">Bitácora</a>
  </nav>
</div></header>

<section class="hero"><div class="hero-card">
  <div>
    <h2>El horno en marcha, el negocio bajo control</h2>
    <p>Panel integral para administrar panes, inventario de ingredientes, recetas, producción diaria, ventas y compras de tu panadería.</p>
    <div class="chips">
      <span class="chip">BD: panaderia</span>
      <span class="chip">9 tablas relacionales</span>
      <span class="chip">Triggers y procedimientos</span>
    </div>
  </div>
  <div class="card" style="background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.25);color:#fff">
    <div class="label" style="color:#fdebd3">Valor estimado de inventario</div>
    <div class="value" style="color:#fff;font-size:2.2rem">Bs <?= number_format((float)$valor,2) ?></div>
    <small style="color:#fdebd3">Panes + ingredientes (stock × costo/precio)</small>
  </div>
</div></section>

<div class="contenedor">
  <div class="grid kpi">
    <div class="card kpi"><div class="label">Productos (panes)</div><div class="value"><?= h($kpi_panes) ?></div><small><?= h($kpi_stock_panes) ?> unidades en stock</small></div>
    <div class="card kpi"><div class="label">Ingredientes</div><div class="value"><?= h($kpi_ing) ?></div><small>insumos registrados</small></div>
    <div class="card kpi"><div class="label">Ventas acumuladas</div><div class="value">Bs <?= number_format((float)$kpi_ventas,2) ?></div><small>histórico</small></div>
    <div class="card kpi"><div class="label">Compras acumuladas</div><div class="value">Bs <?= number_format((float)$kpi_compras,2) ?></div><small>a proveedores</small></div>
    <div class="card kpi"><div class="label">Producción total</div><div class="value"><?= h($kpi_prod) ?></div><small>unidades horneadas</small></div>
  </div>

  <div class="layout">
    <div class="card">
      <div class="section-title"><h3>Últimas ventas</h3><span class="badge">tabla venta + detalle_venta</span></div>
      <table><thead><tr><th>#</th><th>Fecha</th><th>Método</th><th>Ítems</th><th>Total</th></tr></thead><tbody>
      <?php if($ult_ventas && $ult_ventas->num_rows): while($r=$ult_ventas->fetch_assoc()): ?>
        <tr><td>#<?= h($r['id_venta']) ?></td><td><?= h($r['fecha']) ?></td><td><?= h($r['metodo_pago']) ?></td><td><?= h($r['items']) ?></td><td><strong>Bs <?= number_format((float)$r['total'],2) ?></strong></td></tr>
      <?php endwhile; else: ?><tr><td colspan="5" class="muted">Sin ventas registradas.</td></tr><?php endif; ?>
      </tbody></table>
    </div>
    <div class="grid" style="gap:16px">
      <div class="card">
        <div class="section-title"><h3>Más vendidos</h3><span class="badge">ranking</span></div>
        <table><thead><tr><th>Pan</th><th>Vendidos</th><th>Ingreso</th></tr></thead><tbody>
        <?php if($top_panes && $top_panes->num_rows): while($r=$top_panes->fetch_assoc()): ?>
          <tr><td><?= h($r['nombre']) ?></td><td><?= h($r['vendidos']) ?></td><td>Bs <?= number_format((float)$r['ingreso'],2) ?></td></tr>
        <?php endwhile; else: ?><tr><td colspan="3" class="muted">Sin datos.</td></tr><?php endif; ?>
        </tbody></table>
      </div>
      <div class="card">
        <div class="section-title"><h3>Alerta de stock</h3><span class="badge">panes ≤ 20</span></div>
        <table><thead><tr><th>Pan</th><th>Stock</th><th>Estado</th></tr></thead><tbody>
        <?php if($stock_bajo && $stock_bajo->num_rows): while($r=$stock_bajo->fetch_assoc()): $s=(int)$r['stock']; $cls=$s==0?'bad':($s<=10?'warn':'ok'); $txt=$s==0?'SIN STOCK':($s<=10?'BAJO':'OK'); ?>
          <tr><td><?= h($r['nombre']) ?></td><td><?= h($s) ?></td><td><span class="pill <?= $cls ?>"><?= $txt ?></span></td></tr>
        <?php endwhile; else: ?><tr><td colspan="3" class="muted">Todo en orden.</td></tr><?php endif; ?>
        </tbody></table>
      </div>
    </div>
  </div>
</div>
<footer>Proyecto BDD2 · Panadería · UNANDES Ing. de Sistemas</footer>
</body></html>
<?php $conexion->close(); ?>

