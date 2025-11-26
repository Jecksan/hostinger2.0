<?php
$conexion = new mysqli(
    "localhost",
    "u905897753_jecksan_user",
    "SUPERCOOL11a",
    "u905897753_recibo_bd"
);

if ($conexion->connect_errno) {
    echo "Error al conectar: " . $conexion->connect_error;
    exit();
}
?>
