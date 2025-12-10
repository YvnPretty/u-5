<?php
define("SERVIDOR", "localhost");
define("USUARIO", "tu_usuario");
define("PASSWORD", "tu_password");
define("BASE_DATOS", "topicos");
define("PUERTO", "3306");

try {
    // Variable global
    $conexion = new PDO("mysql:host=" . SERVIDOR . ";port=" . PUERTO . ";dbname=" . BASE_DATOS . ";charset=utf8", USUARIO, PASSWORD);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    die("Error al conectar con la base de datos: " . $error->getMessage()); // Detiene ejecución con mensaje
}
?>
