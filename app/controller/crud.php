<?php
    //Importacion a base de datos 

    require_once '../config/conexion.php'; 
    // consulta genral a la base de datos 
    $consulta = $conexion ->prepare("SELECT * FROM t_usuario");// prepara la consulta de la tabla en la base de datos  
    $consulta->execute(); //Ejecuta la consulta que preparamos 
    $datos_recibidos =$consulta->fetchAll(PDO::FETCH_ASSOC); //Relaciona los datos obtenidos de la base de datos a un arreglo para que php lo pueda leeer 
    echo print_r($datos_recibidos);//Imprime los datos que tenemos en nuestra tabla creada 

    //consulta filtrada con blindaje de parametros  
    $id = 1; //Cremaos el id para que este pueda ser dinamico y introducir un valor 
    $pass= "1234"; //Cremaos la variable para que este dato pueda se dinamico 
    $consulta2=$conexion ->prepare ("SELECT * FROM t_usuario WHERE id_usuario = :id_usuario AND password =:password"); //Preparamos la consulta buscando datos esfecificos en la tabla 
    $consulta2 -> bindParam(":id_usuario",$id);  //El bindParam blinda los parametros que vengan del query para que estos sean seguros en la consulata y personas externas que no tengan la licencia o permisos puedan bulnerarlos 
    $consulta2 -> bindParam(":password",$pass); //Los dos puntos sriven para que php no confunda las varaibles ya que los toma como variables diferentes
    $consulta2 -> execute(); //Ejecuta la consulta preparada que hicimos    
    $datos_filtro = $consulta2 ->fetch(PDO::FETCH_ASSOC); //^permite relacionar los datos obtenidos de la base de datos con php lo pueda leer mediate arreglo
    echo"<br><br><br><br>"; //Salto de linea 
    echo print_r($datos_filtro); //Imprimimos los datos de la conuslta mediante arreglos 

    /* Creacion e registros */
    $nombre= "test1"; //Creamos nuestras variables parapoder introducir un nuevo registro
    $usuario = "testing"; //Creamos nuestras variables parapoder introducir un nuevo registro
    $password = "0000"; //Creamos nuestras variables parapoder introducir un nuevo registro
    $inserccion = $conexion -> prepare("INSERT INTO t_usuario (nombre, usuario, password) VALUES (:nombre, :usuario, :password)"); //Preparamas nuestro registro para poder introducir datos
    $inserccion->bindParam(":nombre", $nombre);//Blindamos los parametros para que estos sean mas seguros; los dos puentos sirve para que no sean iguales las variable 
    $inserccion->bindParam(":usuario", $usuario); //Blindamos los parametros para que estos sean mas seguros; los dos puentos sirve para que no sean iguales las variable 
    $inserccion->bindParam(":password", $password); //Blindamos los parametros para que estos sean mas seguros; los dos puentos sirve para que no sean iguales las variable 
    if ($inserccion -> execute()){//Ponemos una condicionala para Ejecutar nustra crecion preparada y si
        echo "Insercion correcta"; //Si hicimos bien nustra creacion podemos nos arrojara la parte de correcta 
    }else{//Parte del fallo
        echo "Insercion fallida!!!!!!!"; //Si no hicimos algo bien nos arrojara a esta parte del if
    }

    /* Actualizacion de registros  */

    $nombre= "test2"; //Datos que vamso a actualizar 
    $usuario ="testing2";
    $password="123456"; 
    $id_usuario=""; 
    $actualizar = $conexion->prepare("UPDATE t_usuario SET  nombre = :nombre, usuario= :usuario, password= :password WHERE id_usuario = :id_usuario"); 
    $actualizar ->bindParam(":nombre",$nombre); //Blindamos los parametros de nombre para que estos sean mas seguros; los dos puentos sirve para que no sean iguales las variable 
    $actualizar ->bindParam(":usuario",$usuario); //Blindamos los parametros de usuario para que estos sean mas seguros; los dos puentos sirve para que no sean iguales las variable 
    $actualizar ->bindParam(":password",$password); //Blindamos los parametros password para que estos sean mas seguros; los dos puentos sirve para que no sean iguales las variable 
    $actualizar ->bindParam(":id_usuario",$id_usuario); //Blindamos los parametros usuario para que estos sean mas seguros; los dos puentos sirve para que no sean iguales las variable 
    if ($actualizar -> execute()){//Ejecutamos la actualizacion mediante una condicional que es el if
        echo "Actualizacion correcta"; //Si hicimos todo bien nos mostrarae esta parte del if
    }else{
        echo "Actualizacion fallida!!!!!!!"; //Si NO HICIMOS LAGO BIEN NOS  MOSTRARA ESYTA PARTE DE IF 
    }
    /* Eliminacion de datos  */
    $id_delete = "3"; //Asignamos el id que quermos eliuminar 
    $eliminacion = $conexion -> prepare(" DELETE FROM t_usuario WHERE id_usuario = :id_usuario"); //Preparamos nuestra eliminacion apuntando a la tabala, y el dato en especifico 
    $eliminacion -> bindParam(":id_usuario", $id_delete); //Blindamos los parametros id para que estos sean mas seguros; los dos puentos sirve para que no sean iguales las variable 
    if ($eliminacion -> execute()){ //eJECUTAMOS NUSTRA ELIMINACION MEDIANTE UNA CONDICIONAL SI HICMOS TODO BIEN SE ELIMINARA MEDIANTE EL ID 
        echo "Eliminacion correcta"; 
    }else{
        echo "Eliminacion fallida!!!!!!!"; // Si no se hizo bien no se eliminara el id 
    }

    /* Notaaa!!!!

        Si no se pone el la condicional where puede eliminar toda la base de datos y no hay retorno 
    
    */
    
?>