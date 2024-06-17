<!-- FUNCION DE REGISTAR USAURIO -->
<?php
class registro_usuario {
    public static function registrar_usuario($documento, $nombre, $correo, $password_hash) {
        // Paso 1: Conexión a la base de datos
        $conexion = mysqli_connect('localhost', 'root', '', 'jj_bd');

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Escapar variables para prevenir SQL Injection
        $documento = mysqli_real_escape_string($conexion, $documento);
        $nombre = mysqli_real_escape_string($conexion, $nombre);
        $correo = mysqli_real_escape_string($conexion, $correo);
        $password_hash = mysqli_real_escape_string($conexion, $password_hash);

        // Verificar si el correo ya está registrado
        $q = "SELECT count(*) as contar from clientes where correo = '$correo'";
        $consulta = mysqli_query($conexion, $q);
        $array = mysqli_fetch_array($consulta);

        if ($array['contar'] > 0) {
            return "El correo electrónico ya está registrado.";
        }

        // Insertar nuevo usuario en la base de datos
        $q = "INSERT INTO clientes (documento, nombre, correo, password_hash) VALUES ('$documento', '$nombre', '$correo', '$password_hash')";
        if (mysqli_query($conexion, $q)) {
            // Redirigir después de la inserción
            header("location: controlador.php?seccion=seccion9");
            exit; // Aseguramos que se detenga la ejecución después de la redirección
        } else {
            return "Error al registrar el usuario: " . mysqli_error($conexion);
        }
    }
}
?>

<!-- FUNCION DE INICIAR SESION -->
<?php
class InicioSesionUsuario {
    public static function iniciarSesion($correo, $password) {
        // Paso 1: Conexión a la base de datos
        $conexion = new mysqli('localhost', 'root', '', 'jj_bd');

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Usar prepared statements para prevenir SQL Injection
        $stmt = $conexion->prepare("SELECT password_hash FROM clientes WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();
        
        // Verificar si se encontró un usuario con ese correo
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($password_hash);
            $stmt->fetch();
            
            // Verificar la contraseña
            if (password_verify($password, $password_hash)) {
                // Iniciar la sesión si aún no ha sido iniciada
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['usuario'] = $correo; // Guardar el correo como nombre de usuario
                // Redirigir a la sección 9 después de iniciar sesión exitosamente
                header("Location: controlador.php?seccion=seccion9");
                exit();
            } else {
                return "Correo o contraseña incorrectos.";
            }
        } else {
            return "Correo o contraseña incorrectos.";
        }

        // Cerrar la conexión a la base de datos
        $stmt->close();
        $conexion->close();
    }
}
?>

<!-- FUNCION PARA INICIAR SESION EL ADMIN -->
<?php
class inicio_sesion_admin {
    public static function admin($correo, $password) {
        // Paso 1: Conexión a la base de datos
        $conexion = mysqli_connect('localhost', 'root', '', 'jj_bd');

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Sanitizar entradas
        $correo = $conexion->real_escape_string($correo);

        // Paso 2: Consulta SQL para obtener la contraseña encriptada del usuario
        $sql = "SELECT password FROM admin WHERE correo = ?";
        
        // Preparar la consulta
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            // El correo existe en la base de datos
            $fila = $resultado->fetch_assoc();
            $hash_guardado = $fila['password']; // Obtener la contraseña encriptada

            // Verificar la contraseña
            if (password_verify($password, $hash_guardado)) {
                // La contraseña es correcta
                header("location: controlador.php?seccion=seccion7");
                exit(); // Asegurarse de que la ejecución se detenga después de la redirección
            } else {
                echo "Error: Contraseña incorrecta.";
            }
        } else {
            echo "Error: Correo no registrado.";
        }

        // Cerrar la conexión a la base de datos
        $stmt->close();
        $conexion->close();
    }
}
?>


<!-- Funcion de Productos -->
<?php
class Productos {
    public static function mostrarProductos() {
        // Paso 1: Conexión a la base de datos
        $conexion = mysqli_connect('localhost', 'root', '', 'jj_bd');

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Paso 2: Consulta SQL para obtener los productos
        $sql = "SELECT * FROM productos";
        
        // Ejecutar la consulta
        $result = $conexion->query($sql);

        // Verificar si se encontraron resultados
        if ($result->num_rows > 0) {
            echo '<div class="container mt-5">';
            echo '<h1 class="text-center mb-4">Ofertas Disponibles</h1>';
            echo '<div class="row">';
            
            // Iterar sobre los productos y generar el HTML
            while ($producto = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card h-100">';
                echo '<a href="descripcion.html">';
                echo '<img src="http://localhost/Sitio/img/' . $producto['imagen_url'] . '" class="card-img-top" alt="' . $producto['nombre_producto'] . '">';
                echo '</a>';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $producto['nombre_producto'] . '</h5>';
                echo '<h5 class="card-title">$' . $producto['precio'] . '</h5>';
                // Si existe el campo 'descuento', mostrarlo
                if (isset($producto['descuento'])) {
                    echo '<h6 class="card-subtitle mb-2 text-muted">' . $producto['descuento'] . '</h6>';
                }
                // Si existe el campo 'descripcion', mostrarlo
                if (isset($producto['descripcion'])) {
                    echo '<p class="card-text">' . $producto['descripcion'] . '</p>';
                }
                echo '<a href="carrrito.html"><button class="btn btn-warning">Agregar Al Carrito</button></a>';
                echo '<a href="pago.html"><button type="button" class="btn btn-success">Comprar</button></a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            
            echo '</div>'; // Cierre de row
            echo '</div>'; // Cierre de container
        } else {
            echo "No se encontraron productos.";
        }

        // Cerrar la conexión a la base de datos
        $conexion->close();
    }
}
?>






<!-- 
 //******************************ELIMINACION DE DATOS DEL USUSARIO****************************//

//Creacion De una Función
function eliminar($Documento)
{
    //Paso 1: Conexión a la base de datos
    $conexion = mysqli_connect('localhost', 'root', 'root', '20231019a');
    // Paso 2: Consulta SQL para contar los registros en la tabla "Personas"
    $sql = "DELETE FROM Usuario Where Documento=$Documento";
    // Ejecuta una consulta
    $resultado = $conexion->query($sql);
    // Paso 3: Obtener el resultado de la consulta
    if ($resultado) {
        return "Elimiación  exitoso";
    } else {
        return "Error en al Eliminar: " . $conexion->error;
    }
    // Paso 4: Cerrar la conexión a la base de datos
    $conexion->close();
}


//******************************ACTUALIZAR DE DATOS DEL USUSARIO****************************//

// Creación de una función para actualizar datos
function actualizar($Documento, $Nombre, $Sitio)
{
    // Paso 1: Conexión a la base de datos
    $conexion = mysqli_connect('localhost', 'root', 'root', '20231019a');

    // Verificar si la conexión se realizó correctamente
    if (!$conexion) {
        return "Error en la conexión a la base de datos: " . mysqli_connect_error();
    }

    // Paso 2: Consulta SQL para actualizar los datos en la tabla "Usuario"
    $sql = "UPDATE Usuario SET Nombre = '$Nombre', Sitio = '$Sitio' WHERE Documento = $Documento";

    // Ejecutar la consulta
    $resultado = mysqli_query($conexion, $sql);

    // Paso 3: Verificar si la consulta se ejecutó con éxito
    if ($resultado) {
        // Paso 4: Cerrar la conexión a la base de datos
        mysqli_close($conexion);
        return "Actualización exitosa";
    } else {
        return "Error al actualizar: " . mysqli_error($conexion);
    }
}
 
 -->
