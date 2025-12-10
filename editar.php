<?php
session_start();
if (!isset($_SESSION['usuario_sesion'])) {
    header("Location: login.php?mensaje_error=Debes+iniciar+sesion");
    exit();
}

require_once './app/config/conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?mensaje_error=ID de producto no especificado");
    exit();
}

$id_inventario = $_GET['id'];
$producto_data = null;

try {
    $consulta = $conexion->prepare("SELECT * FROM t_inventario WHERE id_inventario = :id");
    $consulta->bindParam(":id", $id_inventario);
    $consulta->execute();
    $producto_data = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$producto_data) {
        header("Location: index.php?mensaje_error=Producto no encontrado");
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener el producto: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/b5.css">
    <link rel="stylesheet" href="./public/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="./public/js/b5.js" defer></script>
    <title>Editar Producto</title>
</head>
<body>
    <?php require_once './components/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Producto</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error_actualizacion'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($_GET['error_actualizacion']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="./app/controller/actualizarController.php" method="POST">
                            <input type="hidden" name="id_inventario" value="<?php echo htmlspecialchars($producto_data['id_inventario']); ?>">
                            
                            <div class="mb-3">
                                <label for="producto" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="producto" name="producto" value="<?php echo htmlspecialchars($producto_data['producto']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" value="<?php echo htmlspecialchars($producto_data['precio']); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="unidades" class="form-label">Unidades</label>
                                <input type="number" class="form-control" id="unidades" name="unidades" min="0" value="<?php echo htmlspecialchars($producto_data['unidades']); ?>" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Actualizar Producto</button>
                                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (file_exists('./components/footer.php')) {
        require_once './components/footer.php';
    }
    ?>
</body>
</html>
