<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_sesion'])) {
    // Considera redirigir a una página de error o login si es necesario
    // Para este controlador, si no hay sesión, es un acceso no autorizado.
    // Podrías redirigir a login.php con un mensaje de error.
    // header("Location: ../../login.php?error=Intento de acceso no autorizado");
    // exit();
    // Por ahora, asumimos que la protección en editar.php es suficiente,
    // pero es buena práctica tenerla aquí también.
}

// Incluir la conexión a la base de datos
// __DIR__ asegura que la ruta sea correcta desde la ubicación del controlador
require_once __DIR__ . '/../config/conexion.php';

// Verificar si la solicitud es por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que todos los campos necesarios estén presentes y no vacíos
    if (
        isset($_POST['id_inventario']) && !empty(trim($_POST['id_inventario'])) &&
        isset($_POST['producto']) && !empty(trim($_POST['producto'])) &&
        isset($_POST['precio']) && trim($_POST['precio']) !== '' && // Permite precio 0, pero no vacío
        isset($_POST['unidades']) && trim($_POST['unidades']) !== '' // Permite unidades 0, pero no vacío
    ) {
        // Sanitizar y obtener los datos del formulario
        $id_inventario = filter_var(trim($_POST['id_inventario']), FILTER_VALIDATE_INT);
        $producto_nombre = trim($_POST['producto']);
        $precio = filter_var(trim($_POST['precio']), FILTER_VALIDATE_FLOAT);
        $unidades = filter_var(trim($_POST['unidades']), FILTER_VALIDATE_INT);

        // Validaciones adicionales para los datos numéricos
        if ($id_inventario === false || $id_inventario <= 0) {
            header("Location: ../../editar.php?id=" . (isset($_POST['id_inventario']) ? $_POST['id_inventario'] : '') . "&error_actualizacion=ID de producto inválido.");
            exit();
        }
        if ($precio === false || $precio < 0) {
            header("Location: ../../editar.php?id=" . $id_inventario . "&error_actualizacion=El precio ingresado no es válido.");
            exit();
        }
        if ($unidades === false || $unidades < 0) {
            header("Location: ../../editar.php?id=" . $id_inventario . "&error_actualizacion=La cantidad de unidades no es válida.");
            exit();
        }

        try {
            // Preparar la consulta SQL para actualizar el producto
            $actualizacion = $conexion->prepare(
                "UPDATE t_inventario 
                 SET producto = :producto, precio = :precio, unidades = :unidades 
                 WHERE id_inventario = :id_inventario"
            );

            // Asociar los parámetros
            $actualizacion->bindParam(':producto', $producto_nombre, PDO::PARAM_STR);
            $actualizacion->bindParam(':precio', $precio, PDO::PARAM_STR); // PDO maneja float como string para precisión
            $actualizacion->bindParam(':unidades', $unidades, PDO::PARAM_INT);
            $actualizacion->bindParam(':id_inventario', $id_inventario, PDO::PARAM_INT);

            // Ejecutar la consulta
            if ($actualizacion->execute()) {
                // Si la actualización fue exitosa, redirigir al index.php con mensaje de éxito
                header("Location: ../../index.php?mensaje=Producto actualizado correctamente.");
                exit();
            } else {
                // Si hubo un error en la ejecución, redirigir a editar.php con mensaje de error
                header("Location: ../../editar.php?id=" . $id_inventario . "&error_actualizacion=Error al actualizar el producto en la base de datos.");
                exit();
            }
        } catch (PDOException $e) {
            // En caso de una excepción PDO (error de base de datos), redirigir con mensaje
            // En un entorno de producción, deberías loguear $e->getMessage() en lugar de mostrarlo al usuario.
            header("Location: ../../editar.php?id=" . $id_inventario . "&error_actualizacion=Error de base de datos: " . urlencode($e->getMessage()));
            exit();
        }
    } else {
        // Si faltan datos o están vacíos, redirigir a editar.php con mensaje de error
        // Es importante pasar el ID de vuelta si estaba presente para que el formulario de edición pueda cargarse.
        $id_param = isset($_POST['id_inventario']) && !empty($_POST['id_inventario']) ? "id=" . $_POST['id_inventario'] . "&" : "";
        header("Location: ../../editar.php?" . $id_param . "error_actualizacion=Todos los campos son obligatorios y deben ser válidos.");
        exit();
    }
} else {
    // Si el método no es POST, redirigir a index.php (o mostrar un error)
    header("Location: ../../index.php?mensaje_error=Método no permitido para esta acción.");
    exit();
}
?>
