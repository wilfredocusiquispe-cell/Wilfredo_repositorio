<?php
require_once 'auth.php';

if (!empty($_SESSION['id_usuario']) && ($_SESSION['rol'] ?? '') === 'administrador') {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['password'] ?? '';

    if ($usuario === '' || $clave === '') {
        $error = 'Debes ingresar tu usuario y contraseña.';
    } else {
        $stmt = $conexion->prepare("SELECT id_usuario, nombre, usuario, password, rol, activo FROM usuario WHERE usuario = ? LIMIT 1");
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();

        if (!$fila) {
            $error = 'El usuario no existe. Revisa tus datos.';
        } elseif (!hash_equals($fila['password'], hash('sha256', $clave))) {
            $error = 'Contraseña incorrecta. Inténtalo de nuevo.';
        } elseif ((int)$fila['activo'] !== 1) {
            $error = 'Este usuario está inactivo. Contacta con soporte.';
        } elseif ($fila['rol'] !== 'administrador') {
            $error = 'Acceso denegado: solo el encargado (administrador) puede ingresar.';
        } else {
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = (int)$fila['id_usuario'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['rol'] = $fila['rol'];
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión - Panadería Delicias</title>
  <style>
  :root {
    --bg: #fff8f1;
    --card: #ffffff;
    --ink: #2b2118;
    --muted: #7a6756;
    --brand: #b45309;
    --brand2: #d97706;
    --line: #eadfce;
    --shadow: 0 10px 30px rgba(92, 55, 20, .12);
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: "Segoe UI", Tahoma, sans-serif;
    color: var(--ink);
    min-height: 100vh;
    display: grid;
    place-items: center;
    padding: 20px;
    background:
      radial-gradient(circle at 10% 10%, #fde7c7 0, transparent 25%),
      radial-gradient(circle at 90% 0%, #f7d9b5 0, transparent 22%),
      var(--bg);
  }
  .login {
    width: 100%;
    max-width: 420px;
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 20px;
    box-shadow: var(--shadow);
    overflow: hidden;
  }
  .login-head {
    background: linear-gradient(120deg, #7c2d12, #b45309 55%, #d97706);
    color: #fff;
    padding: 28px 24px;
    text-align: center;
  }
  .login-head .logo { font-size: 38px; }
  .login-head h1 { font-size: 1.5rem; margin: 8px 0 4px; }
  .login-head p { color: #fdebd3; font-size: .9rem; }
  .login-body { padding: 24px; }
  label {
    display: block;
    font-size: .85rem;
    color: var(--muted);
    margin: 12px 0 5px;
    font-weight: 700;
  }
  input {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid #dccdb8;
    border-radius: 10px;
    font: inherit;
    background: #fffdf9;
  }
  input:focus { outline: 2px solid #f3d3a8; border-color: var(--brand); }
  button {
    width: 100%;
    margin-top: 20px;
    background: linear-gradient(135deg, var(--brand), var(--brand2));
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 10px;
    font: inherit;
    font-weight: 700;
    cursor: pointer;
  }
  button:hover { filter: brightness(1.05); }
  .alert {
    padding: 11px 13px;
    border-radius: 10px;
    margin-bottom: 14px;
    font-weight: 600;
    font-size: .9rem;
  }
  .alert.err {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
  }
  .hint {
    margin-top: 18px;
    padding: 10px 12px;
    background: #fff7ed;
    border: 1px solid #f3d3a8;
    border-radius: 10px;
    color: #7c2d12;
    font-size: .82rem;
    text-align: center;
  }
  footer {
    text-align: center;
    color: var(--muted);
    padding: 14px;
    font-size: .8rem;
  }
  </style>
</head>
<body>
  <div class="login">
    <div class="login-head">
      <div class="logo">🍞</div>
      <h1>Panadería Delicias</h1>
      <p>Acceso exclusivo para el encargado</p>
    </div>
    <div class="login-body">
      <?php if ($error !== ''): ?>
        <div class="alert err"><?= h($error) ?></div>
      <?php endif; ?>
      <form method="post" action="login.php">
        <label for="usuario">Usuario</label>
        <input id="usuario" name="usuario" type="text" autocomplete="username" required
               value="<?= h($_POST['usuario'] ?? '') ?>">

        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <button type="submit">Ingresar</button>
      </form>
      <div class="hint">Acceso demo: usuario <strong>admin</strong> · contraseña <strong>PanAdmin2026</strong></div>
    </div>
    <footer>Proyecto BDD2 · Panadería · UNANDES Ing. de Sistemas</footer>
  </div>
</body>
</html>
