<?php
session_start(); // Es buena práctica iniciar sesión si vas a manejar mensajes o proteger el controlador

// Verificar si el usuario está autenticado (opcional, pero recomendado si solo usuarios logueados pueden agregar)
if (!isset($_SESSION['usuario_sesion'])) {
    // Si se requiere autenticación, redirigir o mostrar error
    // header("Location: ../../login.php?error=no_autorizado");
    // exit();
    // Por ahora, lo dejaremos pasar, pero considera la seguridad.
}

// __DIR__ asegura que la ruta al archivo de conexión sea correcta independientemente desde dónde se llame el script.
require_once __DIR__ . '/../config/conexion.php';

// Validación por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validar que existan todos los campos y que no estén vacíos
  if (
    isset($_POST['producto']) && !empty(trim($_POST['producto'])) &&
    isset($_POST['precio']) && !empty(trim($_POST['precio'])) &&
    isset($_POST['unidades']) && !empty(trim($_POST['unidades']))
  ) {
    $producto = trim($_POST['producto']);
    // Validar que precio sea un número positivo
    $precio = filter_var(trim($_POST['precio']), FILTER_VALIDATE_FLOAT);
    // Validar que unidades sea un entero positivo
    $unidades = filter_var(trim($_POST['unidades']), FILTER_VALIDATE_INT);

    if ($precio === false || $precio < 0) {
        // Redirige al formulario de agregar con un mensaje de error
        header("Location: ../../formulario_agregar.php?error=El precio no es válido.");
        exit();
    }
    if ($unidades === false || $unidades < 0) {
        // Redirige al formulario de agregar con un mensaje de error
        header("Location: ../../formulario_agregar.php?error=La cantidad de unidades no es válida.");
        exit();
    }

    try {
      $insercion = $conexion->prepare("INSERT INTO t_inventario (producto, precio, unidades) VALUES (:producto, :precio, :unidades)");
      $insercion->bindParam(':producto', $producto, PDO::PARAM_STR);
      $insercion->bindParam(':precio', $precio, PDO::PARAM_STR); // PDO trata los float como STR para precisión en algunos drivers
      $insercion->bindParam(':unidades', $unidades, PDO::PARAM_INT);
      
      if ($insercion->execute()) {
        // Redirige al index con un mensaje de éxito
        header("Location: ../../index.php?mensaje=Producto agregado correctamente");
        exit();
      } else {
        // Redirige al formulario de agregar con un mensaje de error genérico de base de datos
        header("Location: ../../formulario_agregar.php?error=Error al guardar el producto en la base de datos.");
        exit();
      }

    } catch (PDOException $e) {
      // En un entorno de producción, loguear el error $e->getMessage() en lugar de mostrarlo.
      // Redirige al formulario de agregar con un mensaje de error
      header("Location: ../../formulario_agregar.php?error=Error en la base de datos: " . urlencode($e->getMessage()));
      exit();
    }
  } else {
    // Redirige al formulario de agregar con un mensaje de error
    header("Location: ../../formulario_agregar.php?error=¡Datos no válidos! Asegúrate de llenar todos los campos correctamente.");
    exit();
  }
} else {
  // Si no es POST, redirigir o mostrar error
  header("Location: ../../index.php?mensaje_error=Método no permitido para esta acción.");
  exit();
}
