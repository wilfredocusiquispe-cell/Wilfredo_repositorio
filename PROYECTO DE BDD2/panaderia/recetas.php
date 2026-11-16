<?php
require_once 'conexion.php';
$rows=q($conexion,"SELECT p.nombre pan, i.nombre ingrediente, i.unidad_medida, dr.cantidad
  FROM detalle_receta dr JOIN pan p ON dr.id_pan=p.id_pan JOIN ingrediente i ON dr.id_ingrediente=i.id_ingrediente
  ORDER BY p.nombre, i.nombre");
$resumen=q($conexion,"SELECT p.id_pan, p.nombre, p.precio_venta, COUNT(dr.id_detalle_receta) insumos,
  COALESCE(SUM(dr.cantidad*i.precio_unitario),0) costo_estimado
  FROM pan p LEFT JOIN detalle_receta dr ON p.id_pan=dr.id_pan LEFT JOIN ingrediente i ON dr.id_ingrediente=i.id_ingrediente
  GROUP BY p.id_pan ORDER BY p.nombre");
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Recetas - Panadería</title><style>
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
<header><div class="topbar"><div class="brand"><div class="logo">📋</div><div><h1>Panadería Delicias</h1><span>Recetas y costos</span></div></div>
<nav><a href="index.php">Panel</a><a href="productos.php">Panes</a><a href="ingredientes.php">Ingredientes</a><a class="activo" href="recetas.php">Recetas</a><a href="produccion.php">Producción</a><a href="ventas.php">Ventas</a><a href="compras.php">Compras</a><a href="proveedores.php">Proveedores</a><a href="auditoria.php">Bitácora</a></nav></div></header>
<div class="contenedor">
  <div class="card" style="margin-bottom:16px">
    <div class="section-title"><h3>Costo estimado por receta</h3><span class="badge">detalle_receta + ingrediente</span></div>
    <table><thead><tr><th>Pan</th><th>Insumos</th><th>Costo insumos (est.)</th><th>Precio venta</th><th>Margen est.</th></tr></thead><tbody>
      <?php if($resumen&&$resumen->num_rows): while($r=$resumen->fetch_assoc()): $m=(float)$r['precio_venta']-(float)$r['costo_estimado']; ?>
      <tr><td><strong><?= h($r['nombre']) ?></strong></td><td><?= h($r['insumos']) ?></td><td>Bs <?= number_format((float)$r['costo_estimado'],2) ?></td><td>Bs <?= number_format((float)$r['precio_venta'],2) ?></td><td><span class="pill <?= $m>=0?'ok':'bad' ?>">Bs <?= number_format($m,2) ?></span></td></tr>
      <?php endwhile; else: ?><tr><td colspan="5" class="muted">Sin recetas.</td></tr><?php endif; ?>
    </tbody></table>
  </div>
  <div class="card">
    <div class="section-title"><h3>Detalle de recetas</h3><span class="badge">ingredientes por pan</span></div>
    <table><thead><tr><th>Pan</th><th>Ingrediente</th><th>Cantidad</th><th>Unidad</th></tr></thead><tbody>
      <?php if($rows&&$rows->num_rows): while($r=$rows->fetch_assoc()): ?>
      <tr><td><strong><?= h($r['pan']) ?></strong></td><td><?= h($r['ingrediente']) ?></td><td><?= number_format((float)$r['cantidad'],2) ?></td><td><?= h($r['unidad_medida']) ?></td></tr>
      <?php endwhile; else: ?><tr><td colspan="4" class="muted">No hay detalle de recetas.</td></tr><?php endif; ?>
    </tbody></table>
  </div>
</div>
<footer>Proyecto BDD2 · Panadería</footer></body></html>
<?php $conexion->close(); ?>

