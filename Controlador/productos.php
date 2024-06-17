
<?php
include("../Modelo/funciones.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];
    $nombre_producto = $_POST['nombre_producto'];
    $Precio = $_POST['Precio'];
    $Impuesto = $_POST['Impuesto'];
    $Stock = $_POST['Stock'];
    $id_categoria = $_POST['id_categoria'];
    $Descripción = $_POST['Descripción'];
    $Imagen = $Imagen['id_categoria'];



    // Mostrar el resultado en caso de error
    if ($resultado) {
        echo $resultado;
    }
}

Productos::mostrarProductos();
?>