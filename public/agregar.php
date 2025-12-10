<?php
// agregar.php (Página para agregar nuevos productos)

// Asegura que la sesión esté iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Valida si no hay sesión activa y redirige al login.php.
if (!isset($_SESSION['usuario_sesion'])) {
  header("Location: login.php?mensaje_error=Debes+iniciar+sesion+para+agregar+productos"); 
  exit();
}

// Lógica para manejar el envío del formulario (POST request)
$mensaje_exito = '';
$mensaje_error_form = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos (asegúrate que la ruta sea correcta)
    require_once './app/config/conexion.php';

    // Validar que los campos necesarios estén presentes y no vacíos
    if (
        isset($_POST['producto_nombre']) && !empty(trim($_POST['producto_nombre'])) &&
        isset($_POST['producto_precio']) && trim($_POST['producto_precio']) !== '' &&
        isset($_POST['producto_unidades']) && trim($_POST['producto_unidades']) !== ''
    ) {
        $nombre = trim($_POST['producto_nombre']);
        $precio = filter_var(trim($_POST['producto_precio']), FILTER_VALIDATE_FLOAT);
        $unidades = filter_var(trim($_POST['producto_unidades']), FILTER_VALIDATE_INT);

        // Validaciones adicionales
        if ($precio === false || $precio < 0) {
            $mensaje_error_form = "El precio ingresado no es válido.";
        } elseif ($unidades === false || $unidades < 0) {
            $mensaje_error_form = "La cantidad de unidades no es válida.";
        } else {
            try {
                $insercion = $conexion->prepare(
                    "INSERT INTO t_inventario (producto, precio, unidades) 
                     VALUES (:producto, :precio, :unidades)"
                );
                $insercion->bindParam(':producto', $nombre, PDO::PARAM_STR);
                $insercion->bindParam(':precio', $precio, PDO::PARAM_STR); // PDO maneja float como string
                $insercion->bindParam(':unidades', $unidades, PDO::PARAM_INT);

                if ($insercion->execute()) {
                    // Redirigir al index.php con mensaje de éxito
                    header("Location: index.php?mensaje=Producto agregado correctamente.");
                    exit();
                } else {
                    $mensaje_error_form = "Error al agregar el producto a la base de datos.";
                }
            } catch (PDOException $e) {
                // En producción, loguear $e->getMessage()
                $mensaje_error_form = "Error de base de datos: No se pudo agregar el producto. " . $e->getMessage();
            }
        }
    } else {
        $mensaje_error_form = "Todos los campos son obligatorios.";
    }
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
  <title>Agregar Producto - Inventario</title>
</head>
<body>
  <?php require_once './components/navbar.php'; // Incluye la barra de navegación ?>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Producto</h3>
          </div>
          <div class="card-body">
            <?php if (!empty($mensaje_error_form)): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje_error_form); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>
            <?php if (isset($_GET['mensaje_error'])): // Para errores pasados por URL ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['mensaje_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>


            <form action="agregar.php" method="POST">
              <div class="mb-3">
                <label for="producto_nombre" class="form-label">Nombre del Producto:</label>
                <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" required>
              </div>
              <div class="mb-3">
                <label for="producto_precio" class="form-label">Precio:</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" class="form-control" id="producto_precio" name="producto_precio" step="0.01" min="0" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="producto_unidades" class="form-label">Unidades:</label>
                <input type="number" class="form-control" id="producto_unidades" name="producto_unidades" min="0" required>
              </div>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Guardar Producto</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancelar</a>
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
    } else {
        echo '<footer class="text-center mt-5 py-4 bg-light border-top"><p class="mb-0">&copy; ' . date("Y") . ' Sistema de Inventario</p></footer>';
    }
  ?>
</body>
</html>
