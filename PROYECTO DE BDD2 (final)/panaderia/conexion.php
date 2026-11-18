<?php
$host = 'localhost';
$user = 'asistente_web';
$password = 'PanSecure2026';
$db = 'panaderia';

$conexion = new mysqli($host, $user, $password, $db);
$conexion->set_charset("utf8mb4");

if ($conexion->connect_error) {
    die("<div style='background:#fee2e2; color:#991b1b; padding:15px; border-radius:8px; margin:20px; font-family:sans-serif;'><strong>ERROR CRITICO DE CONEXION:</strong> " . htmlspecialchars($conexion->connect_error) . "</div>");
}

// Funciones auxiliares completas y sin cortes
function q($conexion, $sql) {
    return $conexion->query($sql);
}

function h($v) { 
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); 
}

function esc($conexion, $v) { 
    return $conexion->real_escape_string($v); 
}
?>
