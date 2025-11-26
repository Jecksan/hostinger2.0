<?php
session_start();
require 'conexion.php';

// Si ya está logueado, enviar al index
if(isset($_SESSION["usuario"])){
    header("Location: index.php");
    exit();
}

$mensaje = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $usuario  = $_POST["usuario"];
    $password = $_POST["password"];
    $turno    = $_POST["turno"];

    // Buscar usuario EXACTO
    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND turno = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $usuario, $turno);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si existe
    if($result->num_rows === 1){

        $row = $result->fetch_assoc();

        // Comparar contraseña
        if($password === $row['password']){

            // GUARDAR SESIÓN COMPLETA
            $_SESSION["usuario"] = $row["usuario"];
            $_SESSION["rol"]     = $row["rol"];        // <- IMPORTANTE
            $_SESSION["turno"]   = $row["turno"];

            header("Location: index.php");
            exit();
        } 
        else {
            $mensaje = "Contraseña incorrecta";
        }
    } 
    else {
        $mensaje = "Usuario o turno incorrecto";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
body {
    background: linear-gradient(135deg, #4c6ef5, #22b8cf);
    font-family: Arial;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-box {
    background: white;
    padding: 35px;
    width: 350px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    animation: drop 0.6s ease;
}

@keyframes drop {
    from {opacity:0; transform: translateY(-40px);}
    to {opacity:1; transform: translateY(0);}
}

input, select {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #bbb;
}

button {
    width: 100%;
    padding: 12px;
    background: #4c6ef5;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background: #364fc7;
}

.error {
    background: #fa5252;
    color: white;
    padding: 8px;
    text-align: center;
    border-radius: 6px;
    margin-bottom:10px;
}
</style>
</head>
<body>

<div class="login-box">

<h2 style="text-align:center;">Acceso a la gestión de "Recibo de dinero"</h2>

<?php if($mensaje != ""){ ?>
<div class="error"><?= $mensaje ?></div>
<?php } ?>

<form method="POST">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>

    <select name="turno" required>
        <option value="" disabled selected>Seleccione turno</option>
        <option value="matutino">Matutino</option>
        <option value="vespertino">Vespertino</option>
        <option value="dual">Dual</option>
    </select>

    <button type="submit">Ingresar</button>
</form>

</div>

</body>
</html>
