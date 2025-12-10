<?php
    session_start();
    require_once '../config/conexion.php';
    
    function validar_sesion(){
        // Si la sesión existe entonces debe redirigir al usuario al archivo index.php
        // este archivo contiene te permite iniciar sesión, por lo que al hacerlo bien una vez no es necesario repetir el proceso
        if(isset($_SESSION['usuario_sesion'])){
            // la función header permite redirigir de manera automática a un archivo interno siempre y cuando se indique adecuadamente la ruta
            // ejemplo header("location: ruta.php");
            // para salir de un directorio debes usar ../
            // esto te permite visualizar archivos en carpetas distintas
            header("location: ../../index.php");
        }
    }

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        validar_sesion();
        if($_POST['usuario'] != "" && $_POST['password'] != ""){
            $consulta = $conexion->prepare("SELECT * FROM t_usuario WHERE usuario = :usuario");
            $consulta->bindParam(":usuario", $_POST['usuario']);
            $consulta->execute();
            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

            if($usuario){
                if(password_verify($_POST['password'], $usuario['password'])){
                    $_SESSION['usuario_sesion'] = $usuario;
                    header("location: ../../index.php");
                }else{
                    header("location: ../../login.php?error=Credenciales+incorrectas");
                }
            }else{
                header("location: ../../login.php?error=Usuario+no+encontrado");
            }
        }else{
            header("location: ../../login.php?error=Por+favor+llena+todos+los+campos");
        }
    }else{
        header("location: ../../login.php?error=Acceso+no+autorizado");
    }
?>
