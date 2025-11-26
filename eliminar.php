<?php
session_start();
require "conexion.php";

// ------------------------------------------
// 1. Verificación de seguridad y rol
// ------------------------------------------
// Asegurar que el usuario está logueado y tiene rol de administrador
// NOTA: Es común que uses $_SESSION["admin"] en lugar de $_SESSION["rol"] === "admin". 
// Si la variable de sesión que usas es 'rol', cámbiala a: 
// if(!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin"){ ...
if(!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin"){
    header("Location: login.php");
    exit();
}

// ------------------------------------------
// 2. Capturar y Limpiar el ID
// ------------------------------------------

// Aseguramos que el ID es un número entero. intval() es la forma más segura aquí.
$id_a_eliminar = intval($_GET['id'] ?? 0);

if ($id_a_eliminar === 0) {
    // Si no hay ID o es inválido, redirigir
    header("Location: index.php");
    exit();
}

// ------------------------------------------
// 3. SENTENCIA PREPARADA para la Eliminación (Seguro)
// ------------------------------------------

$sql = "DELETE FROM recibos WHERE id = ?";
$stmt = $conexion->prepare($sql);

// Vincular el parámetro: 'i' indica que el ID es un entero
$stmt->bind_param("i", $id_a_eliminar);

// ------------------------------------------
// 4. Ejecutar y Redirigir
// ------------------------------------------

if ($stmt->execute()) {
    // Éxito
    $stmt->close();
    $conexion->close();
    header("Location: index.php");
    exit();
} else {
    // Manejo de error (muestra un mensaje simple si falla)
    // En un entorno real, solo registrarías el error y redirigirías.
    die("Error al eliminar el recibo: " . $stmt->error);
}
?>
