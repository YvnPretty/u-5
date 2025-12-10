<?php
    session_start();
    if(isset($_SESSION['usuario_sesion'])){
        header("location: index.php");
        exit(); // Añadir exit después de header
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/b5.css">
    <link rel="stylesheet" href="./public/css/main.css">
    <script src="./public/js/b5.js"></script>
    <title>TOPICOS | Login</title>
</head>
<body>
    <?php 
        require_once './components/navbar.php';
    ?>
    <form class="container mt-3" method="POST" action="./app/controller/loginController.php">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4 fondo"> <div class="py-4 px-3"> <h3 class="text-center">Iniciar Sesión</h3>
                    <img src="./public/img/lg.jpg" class="mx-auto d-block rounded-circle mb-3" width="120px" alt="Login"> <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['mensaje'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($_GET['mensaje']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="usuario" name="usuario" type="email" placeholder="e-mail" required> <label class="text-primary" for="usuario"><i
                                class="fa-solid fa-envelope me-2"></i>Correo Electrónico</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input id="password" name="password" type="password" class="form-control"
                            placeholder="Password" required>
                        <label class="text-primary" for="password"><i class="fa-solid fa-lock me-2"></i>Contraseña</label>
                    </div>
                    <button class="btn btn-primary w-100 mb-3" type="submit"><i class="fa-solid fa-door-open me-2"></i>Iniciar sesión</button>
                    <a href="registro.php" class="btn btn-outline-danger w-100 mb-3"><i class="fa-solid fa-user-plus me-2"></i>Crear cuenta nueva</a> </div>
            </div>
        </div>
    </form>
    <?php 
        require_once './components/footer.php';
    ?>
    </body>
</html>
