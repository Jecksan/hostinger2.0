<?php
session_start();
require "conexion.php";

// Redirigir si no es administrador (asumo que solo el admin puede editar)
if(!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin"){
    header("Location: login.php");
    exit();
}

// 1. Obtener y limpiar el ID de la URL
// Usamos intval() para asegurar que el ID es un número entero
$id = intval($_GET["id"] ?? 0); 

if ($id === 0) {
    header("Location: index.php");
    exit();
}

// 2. SENTENCIA PREPARADA para obtener datos (Seguro)
$stmt = $conexion->prepare("SELECT * FROM recibos WHERE id = ?");
$stmt->bind_param("i", $id); // 'i' indica que el parámetro es un entero
$stmt->execute();
$resultado = $stmt->get_result();

// 3. Verificar si se encontró el recibo
if ($resultado->num_rows === 0) {
    echo "Recibo no encontrado.";
    $stmt->close();
    exit();
}

$fila = $resultado->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Recibo - ID: <?= $id ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef3ff; padding: 20px; }
        form { background: #fff; padding: 30px; border-radius: 10px; max-width: 600px; margin: 40px auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        input[type="text"], input[type="date"], input[type="number"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        input[type="submit"] { background: #22b8cf; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; transition: 0.3s; }
        input[type="submit"]:hover { background: #0d8fa1; }
    </style>
</head>

<body>

    <form method="POST" action="update.php?id=<?= $id ?>">

        <h2>✏ Editar Recibo (ID: <?= $id ?>)</h2>
        <hr>
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($fila['nombre']) ?>" required><br>

        <label>Rol:</label>
        <input type="text" name="rol" value="<?= htmlspecialchars($fila['rol']) ?>" required><br>

        <label>Fecha:</label>
        <input type="date" name="fecha" value="<?= htmlspecialchars($fila['fecha']) ?>" required><br>

        <label>Semestre:</label>
        <input type="text" name="semestre" value="<?= htmlspecialchars($fila['semestre']) ?>" required><br>
        
        <hr>
        <h3>Conceptos de Pago</h3>

        <label>Aportación:</label>
        <input type="number" name="aportacion_v" value="<?= htmlspecialchars($fila['aportacion_v']) ?>" step="0.01" required><br>

        <label>Paraescolar:</label>
        <input type="number" name="paraescolar" value="<?= htmlspecialchars($fila['paraescolar']) ?>" step="0.01" required><br>

        <label>Credencial:</label>
        <input type="number" name="credencial" value="<?= htmlspecialchars($fila['credencial']) ?>" step="0.01" required><br>

        <label>Otro:</label>
        <input type="number" name="otro" value="<?= htmlspecialchars($fila['otro']) ?>" step="0.01" required><br>
        
        <hr>
        <h3>Firmas</h3>

        <label>Firma alumno:</label>
        <input type="text" name="firma_alumno" value="<?= htmlspecialchars($fila['firma_alumno']) ?>" required><br>

        <label>Firma entrega:</label>
        <input type="text" name="firma_entrega" value="<?= htmlspecialchars($fila['firma_entrega']) ?>" required><br><br>

        <input type="submit" value="Guardar cambios">
        <a href="index.php">Cancelar</a>

    </form>

</body>
</html>
