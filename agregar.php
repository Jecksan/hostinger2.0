<?php
session_start();
require 'conexion.php';

// Redirigir si no es administrador (asumo que solo el admin puede agregar)
if(!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin"){
    header("Location: login.php");
    exit();
}

$mensaje = "";

// =======================================================
// LÓGICA DE PROCESAMIENTO DEL FORMULARIO
// Esta parte SOLO se ejecuta cuando el formulario es enviado
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Sanitizar y capturar datos
    // Usa un operador ternario para asegurar que el dato existe, aunque el formulario está en esta misma página
    $nombre      = $conexion->real_escape_string($_POST['nombre'] ?? '');
    $rol         = $conexion->real_escape_string($_POST['rol'] ?? '');
    $fecha       = $conexion->real_escape_string($_POST['fecha'] ?? '');
    $semestre    = $conexion->real_escape_string($_POST['semestre'] ?? '');
    // floatval() convierte el valor a número, protegiendo contra caracteres no deseados
    $aportacion  = floatval($_POST['aportacion_v'] ?? 0);
    $paraescolar = floatval($_POST['paraescolar'] ?? 0);
    $credencial  = floatval($_POST['credencial'] ?? 0);
    $otro        = floatval($_POST['otro'] ?? 0);
    $firma_al    = $conexion->real_escape_string($_POST['firma_alumno'] ?? '');
    $firma_en    = $conexion->real_escape_string($_POST['firma_entrega'] ?? '');

    // 2. Calcular total
    $total = $aportacion + $paraescolar + $credencial + $otro;

    // 3. SENTENCIA PREPARADA (MUY IMPORTANTE PARA LA SEGURIDAD)
    $sql = "INSERT INTO recibos 
    (nombre, rol, fecha, semestre, aportacion_v, paraescolar, credencial, otro, total_pagar, firma_alumno, firma_entrega)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    
    // Vincular parámetros:
    // s: string, d: double/float (usamos 5 's' y 4 'd' + 2 's' = 11 variables)
    $stmt->bind_param("ssssdddddss", 
        $nombre, $rol, $fecha, $semestre, $aportacion, $paraescolar, $credencial, $otro, $total, $firma_al, $firma_en
    );

    // 4. Ejecutar y verificar
    if ($stmt->execute()) {
        $stmt->close();
        $conexion->close();
        // Redireccionar al index tras la inserción exitosa
        header("Location: index.php"); 
        exit();
    } else {
        $mensaje = "<p style='color:red;'>Error al insertar el recibo: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Recibo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef3ff; padding: 20px; }
        .form-container { background: #fff; padding: 30px; border-radius: 10px; max-width: 600px; margin: 40px auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        input[type="text"], input[type="date"], select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn-submit { background: #4c6ef5; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #364fc7; }
        .back-btn { background: #6c757d; color: white; padding: 12px 20px; border: none; border-radius: 5px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>➕ Agregar Nuevo Recibo</h2>

    <?= $mensaje ?>

    <form method="POST" action="agregar.php">
        
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        
        <label for="rol">Rol:</label>
        <input type="text" id="rol" name="rol" required>
        
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
        
        <label for="semestre">Semestre:</label>
        <input type="text" id="semestre" name="semestre" required>
        
        <hr>
        <h3>Conceptos de Pago</h3>

        <label for="aportacion_v">Aportación:</label>
        <input type="text" id="aportacion_v" name="aportacion_v" value="0.00" pattern="[0-9]\.?[0-9]" required>
        
        <label for="paraescolar">Paraescolar:</label>
        <input type="text" id="paraescolar" name="paraescolar" value="0.00" pattern="[0-9]\.?[0-9]" required>
        
        <label for="credencial">Credencial:</label>
        <input type="text" id="credencial" name="credencial" value="0.00" pattern="[0-9]\.?[0-9]" required>
        
        <label for="otro">Otro:</label>
        <input type="text" id="otro" name="otro" value="0.00" pattern="[0-9]\.?[0-9]" required>
        
        <hr>
        <h3>Firmas</h3>

        <label for="firma_alumno">Firma Alumno:</label>
        <input type="text" id="firma_alumno" name="firma_alumno" required>
        
        <label for="firma_entrega">Firma Entrega:</label>
        <input type="text" id="firma_entrega" name="firma_entrega" required>

        <br><br>
        <button type="submit" class="btn-submit">Guardar Recibo</button>
        <a href="index.php" class="back-btn">Cancelar y Volver</a>
    </form>
</div>

</body>
</html>
