<?php
$host = 'localhost';
$user = 'asistente_web';
$password = 'PanSecure2026';
$db   = 'panaderia';

$conexion = new mysqli($host, $user, $password, $db);
if ($conexion->connect_error) {
    // Fallback a root para facilitar pruebas locales
    $conexion = new mysqli('localhost', 'root', '', $db);
}
if ($conexion->connect_error) {
    die("<div style='background:#fee2e2;color:#991b1b;padding:16px;border-radius:10px;margin:20px;font-family:Segoe UI,sans-serif;'><strong>Error de conexión:</strong> "
        . htmlspecialchars($conexion->connect_error) . "</div>");
}
$conexion->set_charset('utf8mb4');

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function q($conexion, $sql) { return $conexion->query($sql); }
function esc($conexion, $v) { return $conexion->real_escape_string($v); }
