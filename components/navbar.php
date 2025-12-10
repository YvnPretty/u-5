<?php
// components/navbar.php

// Asegura que la sesión esté iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario ha iniciado sesión.
$isLoggedIn = isset($_SESSION['usuario_sesion']);

// Define la ruta base para los enlaces.
// Asumimos que este archivo (navbar.php) está en './components/'
// y es llamado desde archivos en la raíz del proyecto (ej: U-5/).
// Por lo tanto, "./" desde el script que lo incluye (ej. login.php)
// se refiere a la raíz del proyecto.
$base_url = "./"; 

// Obtiene el nombre del archivo actual para marcar el enlace activo.
$current_page = basename($_SERVER['PHP_SELF']);

// Condición para activar el enlace "Inicio"
$isInicioPage = ($current_page == 'inicio.php');

// Texto y enlace de la marca de la barra de navegación.
$navbar_brand_text = "TOPICOS";
$navbar_brand_link = $isLoggedIn ? ($base_url . 'index.php') : ($base_url . 'inicio.php');

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo htmlspecialchars($navbar_brand_link); ?>"><?php echo htmlspecialchars($navbar_brand_text); ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav ms-auto">
        <a class="nav-link <?php echo $isInicioPage ? 'active' : ''; ?>" href="<?php echo $base_url; ?>inicio.php">Inicio</a>

        <?php if ($isLoggedIn): ?>
          <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>index.php">Inventario</a>
          <a class="nav-link <?php echo ($current_page == 'agregar.php') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>agregar.php">Agregar Producto</a>
          
          <?php 
            $userNameDisplay = '';
            if (isset($_SESSION['usuario_sesion'])) {
                if (is_array($_SESSION['usuario_sesion']) && isset($_SESSION['usuario_sesion']['nombre'])) {
                    $userNameDisplay = $_SESSION['usuario_sesion']['nombre'];
                } elseif (is_string($_SESSION['usuario_sesion'])) {
                    $userNameDisplay = $_SESSION['usuario_sesion']; 
                }
            }
          ?>
          <?php if (!empty($userNameDisplay)): ?>
            <span class="navbar-text me-3 ms-3">
              Hola, <?php echo htmlspecialchars($userNameDisplay); ?>
            </span>
          <?php endif; ?>

          <a class="nav-link" href="<?php echo $base_url; ?>logout.php">Cerrar Sesión</a>
        <?php else: // Usuario no logueado ?>
          <a class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>login.php">Login</a>
          <a class="nav-link <?php echo ($current_page == 'registro.php') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>registro.php">Registro</a>
          <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>login.php?redirect_to=index.php&mensaje_info=Debes+iniciar+sesion+para+ver+el+inventario">Inventario</a>
        <?php endif; ?>
        
      </div>
    </div>
  </div>
</nav>
