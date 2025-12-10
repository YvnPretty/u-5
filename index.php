    }

    // Conexión a la base de datos. Asegúrate que la ruta sea correcta.
    require_once './app/config/conexion.php';

    // Intentar obtener los datos del inventario
    try {
        $consulta = $conexion->prepare("SELECT * FROM t_inventario ORDER BY id_inventario DESC");
        $consulta->execute();
        $inventario = $consulta->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $inventario = [];
        $error_db = "Error al conectar con la base de datos: " . $e->getMessage();
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
      <title>Inventario - Home</title>
    </head>
    <body>
      <?php require_once './components/navbar.php'; // Incluye la barra de navegación ?>

      <div class="container mt-4">
        <div class="row justify-content-center mb-3">
          <div class="col-md-10 text-center">
            <h1>Inventario Principal</h1>
          </div>
        </div>

        <?php if (isset($_GET['mensaje'])): ?>
          <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?php echo htmlspecialchars($_GET['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if (isset($_GET['mensaje_error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <?php echo htmlspecialchars($_GET['mensaje_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if (isset($error_db)): // Muestra error de DB si ocurrió ?>
          <div class="alert alert-danger mt-3" role="alert">
            <?php echo htmlspecialchars($error_db); ?>
          </div>
        <?php endif; ?>

        <div class="row justify-content-end my-3">
          <div class="col-md-4 col-lg-3 text-end">
            <a href="agregar.php" class="btn btn-success w-100">
              <i class="fas fa-plus me-2"></i>Agregar Producto
            </a>
          </div>
        </div>

        <div class="row justify-content-center mt-2">
          <div class="col-md-12">
            <?php if (empty($inventario) && !isset($error_db)): ?>
              <div class="alert alert-info" role="alert">
                No hay productos en el inventario. ¡Agrega algunos!
              </div>
            <?php elseif (!empty($inventario)): ?>
              <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                  <thead class="table-dark">
                    <tr>
                      <th scope="col">ID</th>
                      <th scope="col">Producto</th>
                      <th scope="col">Precio</th>
                      <th scope="col">Unidades</th>
                      <th scope="col" class="text-center">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($inventario as $producto): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($producto['id_inventario']); ?></td>
                        <td><?php echo htmlspecialchars($producto['producto']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format((float)$producto['precio'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($producto['unidades']); ?></td>
                        <td class="text-center">
                          <a href="editar.php?id=<?php echo $producto['id_inventario']; ?>" class="btn btn-sm btn-warning me-1 mb-1 mb-md-0">
                            <i class="fas fa-edit me-1"></i>Editar
                          </a>
                          <a href="./app/controller/eliminarController.php?id=<?php echo $producto['id_inventario']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                            <i class="fas fa-trash-alt me-1"></i>Eliminar
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php elseif (isset($error_db) && empty($inventario)): ?>
                <div class="alert alert-warning" role="alert">
                    No se pudieron cargar los productos debido a un error con la base de datos.
                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <?php
        if (file_exists('./components/footer.php')) {
            require_once './components/footer.php';
        } else {
            echo '<footer class="text-center mt-5 py-4 bg-light border-top"><p class="mb-0">&copy; ' . date("Y") . ' Sistema de Inventario Tópicos Avanzados</p></footer>';
        }
      ?>
    </body>
    </html>
    