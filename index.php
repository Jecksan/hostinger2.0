<?php
session_start();
require 'conexion.php';

// Proteger acceso
if(!isset($_SESSION["usuario"])){
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION["usuario"];
$rol = $_SESSION["rol"];
$turno = $_SESSION["turno"];

// Consulta de tu tabla REAL
$sql = "SELECT * FROM recibos ORDER BY id DESC";
$result = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Recibos</title>

<style>
body{
    font-family: Arial;
    background: #eef3ff;
    margin: 0;
    padding: 0;
}

/* Contenedor */
.container{
    width: 95%;
    margin: auto;
    margin-top: 40px;
    animation: fadeIn 0.8s ease;
}
@keyframes fadeIn{
    from {opacity:0; transform: translateY(25px);}
    to {opacity:1; transform: translateY(0);}
}

/* Títulos */
h1{
    text-align: center;
    color: #364fc7;
}

/* Botón agregar */
.add-btn{
    background: #4c6ef5;
    padding: 12px 20px;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}
.add-btn:hover{
    background: #364fc7;
}

/* Tabla */
table{
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    animation: tablePop 0.7s ease;
}
@keyframes tablePop{
    from {transform: scale(0.95); opacity:0;}
    to {transform: scale(1); opacity:1;}
}

th, td{
    padding: 10px;
    border-bottom: 1px solid #ccc;
    font-size: 14px;
}

th{
    background: #4c6ef5;
    color: white;
}

/* Botones */
.btn{
    padding: 7px 12px;
    border-radius: 6px;
    color: white;
    text-decoration: none;
    font-size: 13px;
    transition: 0.2s;
}

.edit{ background:#22b8cf; }
.edit:hover{ background:#0d8fa1; }

.delete{ background:#fa5252; }
.delete:hover{ background:#c92a2a; }

.logout{
    display: block;
    width: 200px;
    margin: 25px auto;
    text-align: center;
    padding: 12px;
    background:#111;
    color:white;
    border-radius:8px;
    text-decoration:none;
}

</style>

</head>
<body>

<div class="container">

<h1>Gestión de Recibos</h1>

<?php if($rol == "admin"){ ?>
    <a href="agregar.php" class="add-btn">➕ Agregar recibo</a>
<?php } ?>

<table>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Rol</th>
    <th>Fecha</th>
    <th>Semestre</th>
    <th>Aportación</th>
    <th>Paraescolar</th>
    <th>Credencial</th>
    <th>Otro</th>
    <th>Total</th>
    <th>Firma Alumno</th>
    <th>Firma Entrega</th>
    <th>Acciones</th>
</tr>

<?php
while($row = $result->fetch_assoc()){
?>

<tr>
    <td><?= $row["id"] ?></td>

    <td><?= ($rol == "visitante") ? "XXXXX" : $row["nombre"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["rol"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["fecha"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["semestre"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["aportacion_v"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["paraescolar"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["credencial"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["otro"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["total_pagar"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["firma_alumno"] ?></td>
    <td><?= ($rol == "visitante") ? "XXXXX" : $row["firma_entrega"] ?></td>

    <td>
        <?php if($rol=="admin"){ ?>
            <a class="btn edit" href="editar.php?id=<?= $row['id'] ?>">Editar</a>
            <a class="btn delete" href="eliminar.php?id=<?= $row['id'] ?>"
               onclick="return confirm('¿Eliminar este recibo?');">Eliminar</a>
        <?php } else { echo "—"; } ?>
    </td>
</tr>

<?php } ?>

</table>

<a class="logout" href="logout.php">Cerrar sesión</a>

</div>
</body>
</html>
