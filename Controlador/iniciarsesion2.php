<?php
include("../Modelo/funciones.php");

session_start();

if (!isset($_SESSION['usuario'])) {
    if (isset($_POST['correo']) && isset($_POST['password'])) {
        $correo = $_POST['correo'];
        $password = $_POST['password'];

        $resultado = InicioSesionUsuario::iniciarSesion($correo, $password);

        if ($resultado !== true) {
            echo $resultado; // Mostrar mensaje de error si las credenciales son incorrectas
        }
    } else {
        echo "Por favor, introduzca sus credenciales.";
    }
} else {
    // Redirigir si ya está autenticado
    header("Location: controlador.php?seccion=seccion9");
    exit();
}
?>