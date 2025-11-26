<?php
session_start();
require "conexion.php";

// ------------------------------------------
// 1. Verificación de seguridad y rol
// ------------------------------------------
// Asegurar que el usuario está logueado y tiene rol de administrador
if(!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin"){
    header("Location: login.php");
    exit();
}

// ------------------------------------------
// 2. Capturar y Limpiar datos
// ------------------------------------------

// Limpiar y asegurar el ID: Debe ser un entero
$id = intval($_GET["id"] ?? 0);

if ($id === 0) {
    // Si no hay ID o el ID es cero, redirigir
    header("Location: index.php");
    exit();
}

// Capturar valores del POST y usar real_escape_string (aunque usaremos prepared statements, es buena práctica)
// Los campos numéricos se convierten a float
$nombre     = $_POST["nombre"] ?? '';
$rol        = $_POST["rol"] ?? '';
$fecha      = $_POST["fecha"] ?? '';
$semestre   = $_POST["semestre"] ?? '';
$ap         = floatval($_POST["aportacion_v"] ?? 0);
$pa         = floatval($_POST["paraescolar"] ?? 0);
$cr         = floatval($_POST["credencial"] ?? 0);
$ot         = floatval($_POST["otro"] ?? 0);
$fa         = $_POST["firma_alumno"] ?? '';
$fe         = $_POST["firma_entrega"] ?? '';

// Calcular total nuevo
$total = $ap + $pa + $cr + $ot;

// ------------------------------------------
// 3. SENTENCIA PREPARADA para la Actualización (Seguro)
// ------------------------------------------

$sql = "UPDATE recibos SET 
        nombre=?,
        rol=?,
        fecha=?,
        semestre=?,
        aportacion_v=?,
        paraescolar=?,
        credencial=?,
        otro=?,
        total_pagar=?,
        firma_alumno=?,
        firma_entrega=?
        WHERE id=?";

$stmt = $conexion->prepare($sql);

// Vincular los parámetros: 11 variables de datos + 1 variable ID
// s=string, d=double/float, i=integer
$stmt->bind_param(
    "ssssdddddssi", // 4s, 5d, 2s, 1i
    $nombre, 
    $rol, 
    $fecha, 
    $semestre, 
    $ap, 
    $pa, 
    $cr, 
    $ot, 
    $total, 
    $fa, 
    $fe,
    $id // El ID va al final
);

// ------------------------------------------
// 4. Ejecutar y Redirigir
// ------------------------------------------
if ($stmt->execute()) {
    // Éxito: Redirigir al índice
    $stmt->close();
    $conexion->close();
    header("Location: index.php");
    exit();
} else {
    // Error: Mostrar un mensaje si la actualización falla
    // En un entorno de producción, es mejor redirigir a una página de error genérica.
    die("Error al actualizar el recibo: " . $stmt->error); 
}
?>
