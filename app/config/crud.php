<?php

// importacion de la conexion a base de datos
require_once '../config/conexion.php';

// consulta general a la base de datos
$consulta2 = $conexion->prepare("SELECT * FROM t_usuario");
$consulta2->execute();
$datos_recibidos = $consulta2->fetchAll(PDO::FETCH_ASSOC); // se obtienen todos los registros como arreglo asociativo
echo print_r($datos_recibidos); // se imprimen los datos recibidos

echo "<br><br>";

// consulta filtrada con blindaje de parametros
$id = 1;
$pass = '1234';
$consulta2 = $conexion->prepare("SELECT * FROM t_usuario WHERE id_usuario = :id_usuario AND password = :password");
$consulta2->bindParam(':id_usuario', $id); // se vincula el parametro id_usuario con la variable $id
$consulta2->bindParam(':password', $pass); // se vincula el parametro password con la variable $pass
$consulta2->execute();
$datos_filtro = $consulta2->fetch(PDO::FETCH_ASSOC); // se obtiene un solo resultado
echo "<br><br>";
echo print_r($datos_filtro); // se imprime el resultado filtrado

// creacion de registros
$nombre = 'test1';
$usuario = 'testing';
$password = 'eeee';
$insercion = $conexion->prepare("INSERT INTO t_usuario (nombre,usuario,password) VALUES (:nombre,:usuario,:password)");
$insercion->bindParam(':nombre', $nombre); // se vincula el parametro nombre
$insercion->bindParam(':usuario', $usuario); // se vincula el parametro usuario
$insercion->bindParam(':password', $password); // se vincula el parametro password
if($insercion->execute()){ // se ejecuta la insercion
    echo "insercion correcta";
}else{
    echo "insercion fallida :c";
}

// actualizacion de registros
$nombre = 'test2';
$usuario = 'testing';
$password = '123456';
$id_usuario = 4;
$actualizar = $conexion->prepare("UPDATE t_usuario SET nombre = :nombre, usuario = :usuario, password = :password WHERE id_usuario = :id_usuario");
$actualizar->bindParam(':nombre', $nombre); // se vincula el parametro nombre
$actualizar->bindParam(':usuario', $usuario); // se vincula el parametro usuario
$actualizar->bindParam(':password', $password); // se vincula el parametro password
$actualizar->bindParam(':id_usuario', $id_usuario); // se vincula el parametro id_usuario
if($actualizar->execute()){ // se ejecuta la actualizacion
    echo "actualizacion correcta";
}else{
    echo "actualizacion fallida :c";
}

// eliminacion de datos
$id_delete = 3;
$eliminacion = $conexion->prepare("DELETE FROM t_usuario WHERE id_usuario = :id_usuario");
$eliminacion->bindParam(':id_usuario', $id_delete); // se vincula el parametro id_usuario con el valor a eliminar
if($eliminacion->execute()){ // se ejecuta la eliminacion
    echo "eliminacion correcta";
}else{
    echo "eliminacion fallida :c";
}
