
<?php
include("../Modelo/funciones.php");

$documento = $_POST['documento'];
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$password = $_POST['password'];

$resultado = registro_usuario::registrar_usuario($documento,$nombre, $correo, $password);


?>