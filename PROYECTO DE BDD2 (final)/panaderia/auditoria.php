<?php
require_once 'auth.php';
requerir_admin();

// Consulta adaptada exactamente a los atributos de tu tabla
$resultado = q($conexion, "SELECT id_bitacora, evento, tabla_afectada, detalles, usuario, fecha FROM bitacora_sistema ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bitácora del Sistema - Panadería</title>
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

    .contenedor {
      max-width: 1200px;
      margin: 22px auto;
      padding: 0 20px 30px;
    }

    .card {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 18px;
      box-shadow: var(--shadow);
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

    code {
      background: #fff3e6;
      padding: 2px 6px;
      border-radius: 6px;
    }

    .muted {
      color: var(--muted);
    }

    footer {
      text-align: center;
      color: var(--muted);
      padding: 20px;
      font-size: .9rem;
    }
  </style>
</head>
<body>

<header>
  <div class="topbar">
    <div class="brand">
      <div class="logo">🍞</div>
      <div>
        <h1>Panadería Delicias</h1>
        <span>Auditoría y Bitácora</span>
      </div>
    </div>
    <nav>
      <a href="index.php">Panel</a>
      <a href="productos.php">Panes</a>
      <a href="ingredientes.php">Ingredientes</a>
      <a href="recetas.php">Recetas</a>
      <a href="produccion.php">Producción</a>
      <a href="ventas.php">Ventas</a>
      <a href="compras.php">Compras</a>
      <a href="proveedores.php">Proveedores</a>
      <a class="activo" href="auditoria.php">Bitácora</a>
      <a href="logout.php">Cerrar sesión</a>
    </nav>
  </div>
</header>

<div class="contenedor">
  <div class="card">
    <div class="section-title">
      <h3>Registro de Eventos del Sistema</h3>
      <span class="badge">tabla: bitacora_sistema</span>
    </div>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Evento</th>
          <th>Tabla Afectada</th>
          <th>Detalles</th>
          <th>Usuario</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($resultado && $resultado->num_rows): ?>
          <?php while ($r = $resultado->fetch_assoc()): ?>
            <tr>
              <td>#<?= h($r['id_bitacora']) ?></td>
              <td><strong><?= h($r['evento']) ?></strong></td>
              <td><code><?= h($r['tabla_afectada']) ?></code></td>
              <td class="muted"><?= h($r['detalles']) ?></td>
              <td><?= h($r['usuario']) ?></td>
              <td><?= h($r['fecha']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="muted" style="text-align:center; padding:20px;">
              No hay registros en la bitácora todavía.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<footer>Proyecto BDD2 · Panadería · UNANDES Ing. de Sistemas</footer>

</body>
</html>
<?php $conexion->close(); ?>