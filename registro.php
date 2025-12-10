<?php
    //funcion que permite utilizar las variables de sesion 
    //si no esta invocada generara un error al intentar usar una variable de sesion
    session_start();
    //validacion para determinar y ya existe una sesion creada
    //se valida con isset para saber si esta definida y no generar un error
    if(isset($_SESSION['usuario_sesion'])){
        //si la sesion existe entonces debe redirigir al usuario al archivo index.php
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
    <title>TOPICOS | Registro</title>
</head>

<body>

    <?php 
        require_once './components/navbar.php';
    ?>
    <form class="container mt-3" action="./app/controller/registroController.php" method="POST">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4 fondo"> <div class="py-4 px-3"> <h3 class="text-center">Registro de Usuario</h3>
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
                        <input class="form-control" name="nombre" id="nombre" type="text"
                            placeholder="Nombre" required> <label class="text-primary" for="nombre"><i class="fa-solid fa-user me-2"></i>Nombre</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input name="apellido" id="apellido" type="text" class="form-control"
                            placeholder="Apellido" required>
                        <label class="text-primary" for="apellido"><i
                                class="fa-regular fa-address-book me-2"></i>Apellido</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" name="usuario" id="usuario" type="email" placeholder="e-mail" required>
                        <label class="text-primary" for="usuario"><i
                                class="fa-solid fa-envelope me-2"></i>Correo Electrónico (Usuario)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input name="password" id="password" type="password" class="form-control"
                            placeholder="Password" required>
                        <label class="text-primary" for="password"><i class="fa-solid fa-lock me-2"></i>Contraseña</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3"><i class="fa-solid fa-chalkboard-user me-2"></i>Registrar</button>
                    <a href="login.php" class="btn btn-outline-danger w-100"><i class="fa-solid fa-door-open me-2"></i>Ya tengo cuenta (Iniciar sesión)</a>
                </div>
            </div>
        </div>
    </form>
    <?php 
        require_once './components/footer.php';
    ?>
    </body>
</html>
