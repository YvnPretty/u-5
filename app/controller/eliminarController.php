<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_sesion'])) {
    header("location: ../../login.php?error=Acceso no autorizado"); // Redirige a login si no hay sesión
    exit();
}

// __DIR__ asegura que la ruta al archivo de conexión sea correcta.
require_once __DIR__ . '/../config/conexion.php';

// Verificar si el método de la solicitud es GET y si se ha recibido un ID
if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['id'])) {
    // Obtener y validar el ID del producto desde la URL
    $id_inventario = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id_inventario === false || $id_inventario <= 0) {
        // Si el ID no es válido, redirigir con un mensaje de error
        header("location: ../../index.php?mensaje_error=ID de producto no válido.");
        exit();
    }

    try {
        // Preparar la consulta SQL para eliminar el producto
        $eliminacion = $conexion->prepare("DELETE FROM t_inventario WHERE id_inventario = :id_inventario");

        // Asociar el parámetro :id_inventario con el valor recibido en la URL
        $eliminacion->bindParam(":id_inventario", $id_inventario, PDO::PARAM_INT);

        // Ejecutar la consulta para eliminar el producto
        if ($eliminacion->execute()) {
            // Verificar si alguna fila fue afectada (si el producto existía)
            if ($eliminacion->rowCount() > 0) {
                // Redirigir a la página principal con un mensaje de éxito
                header("location: ../../index.php?mensaje=Producto eliminado correctamente");
            } else {
                // Redirigir si el producto no fue encontrado (ID no existe)
                header("location: ../../index.php?mensaje_error=Producto no encontrado o ya había sido eliminado.");
            }
        } else {
            // En caso de error en la ejecución de la consulta
            header("location: ../../index.php?mensaje_error=Error al ejecutar la eliminación del producto.");
        }
    } catch (PDOException $e) {
        // En un entorno de producción, loguear el error $e->getMessage()
        header("location: ../../index.php?mensaje_error=Error de base de datos al eliminar el producto.");
    }
    exit();

} else {
    // Si no se recibe un ID válido o no es método GET, redirigir
    header("location: ../../index.php?mensaje_error=Solicitud no válida para eliminar producto.");
    exit();
}
?>
