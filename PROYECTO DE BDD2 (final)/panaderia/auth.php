<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

$conexion->query("CREATE TABLE IF NOT EXISTS usuario (
    id_usuario int auto_increment primary key,
    nombre varchar(100) not null,
    usuario varchar(50) not null unique,
    password varchar(64) not null,
    rol varchar(20) not null default 'administrador',
    activo tinyint(1) not null default 1
)");

$conexion->query("INSERT INTO usuario (nombre, usuario, password, rol)
    SELECT 'Encargado', 'admin', SHA2('PanAdmin2026', 256), 'administrador'
    WHERE NOT EXISTS (SELECT 1 FROM usuario WHERE usuario = 'admin')");

function requerir_admin() {
    if (empty($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'administrador') {
        header('Location: login.php');
        exit;
    }
}
