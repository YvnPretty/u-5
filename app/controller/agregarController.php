<?php
session_start();

if (!isset($_SESSION['usuario_sesion'])) {
    header("location: ../../login.php?error=Acceso no autorizado");
    exit();
}

require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (
        isset($_POST['producto']) && !empty(trim($_POST['producto'])) &&
        isset($_POST['precio']) && trim($_POST['precio']) !== '' &&
        isset($_POST['unidades']) && trim($_POST['unidades']) !== ''
    ) {
        $producto = trim($_POST['producto']);
        $precio = filter_var(trim($_POST['precio']), FILTER_VALIDATE_FLOAT);
        $unidades = filter_var(trim($_POST['unidades']), FILTER_VALIDATE_INT);

        if ($precio === false || $precio < 0) {
            header("location: ../../agregar.php?error=Precio inválido");
            exit();
        }
        if ($unidades === false || $unidades < 0) {
            header("location: ../../agregar.php?error=Unidades inválidas");
            exit();
        }

        try {
            $consulta = $conexion->prepare("INSERT INTO t_inventario (producto, precio, unidades) VALUES (:producto, :precio, :unidades)");
            $consulta->bindParam(":producto", $producto);
            $consulta->bindParam(":precio", $precio);
            $consulta->bindParam(":unidades", $unidades);

            if ($consulta->execute()) {
                header("location: ../../index.php?mensaje=Producto agregado exitosamente");
            } else {
                header("location: ../../agregar.php?error=Error al guardar el producto");
            }
        } catch (PDOException $e) {
            header("location: ../../agregar.php?error=Error de base de datos: " . urlencode($e->getMessage()));
        }
    } else {
        header("location: ../../agregar.php?error=Todos los campos son obligatorios");
    }
} else {
    header("location: ../../index.php");
}
?>
