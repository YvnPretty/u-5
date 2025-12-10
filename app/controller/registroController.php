<?php
    session_start(); 
    require_once '../config/conexion.php';
    function validar_sesion(){
        if(isset($_SESSION['usuario_sesion'])){
                header("location: ../../index.php");
        }
    }
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        validar_sesion();
        if($_POST['nombre'] != "" && $_POST['apellido'] != "" && $_POST['usuario']!="" && $_POST['password'] != ""  ){
            $insercion = $conexion -> prepare("INSERT INTO t_usuario (usuario, password,nombre,apellido ) VALUES (:usuario, :password, :nombre, :apellido)"); 
            $insercion ->bindParam(":usuario", $_POST['usuario']); 
            $password_cifrado= password_hash($_POST['password'],PASSWORD_BCRYPT);
            $insercion ->bindParam(":password", $password_cifrado); 
            $insercion ->bindParam(":nombre", $_POST['nombre']); 
            $insercion ->bindParam(":apellido", $_POST['apellido']); 
            $insercion-> execute(); 
            if($insercion){
                header("location: ../../login.php?mensaje=Registro+exitoso");
            }else{
                header("location: ../../registro.php?error=Error+al+registrar");
            }
        }else{
            header("location: ../../registro.php?error=Por+favor+llena+todos+los+campos");
        }
    }else{
        header("location: ../../registro.php?error=Acceso+no+autorizado");
    }
?>