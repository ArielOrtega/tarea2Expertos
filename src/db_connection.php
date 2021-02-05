<?php
//funcion para conectar con la base de datos, ubicada en el servidor guayabo
function createDatabase()
{
    $servername = "163.178.107.2";
    $username = "labsturrialba";
    $password = "Saucr.2191";
    $db = "IF7103_2021_tarea2_b75567";
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);//se crea la conexion con la informacion brindada anteriormente
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;//retorna el objeto de coneccion para ser utilizada en la pagina que lo necesite
    } catch (PDOException $e) {
        //en caso de que haya un error con la conecion, se imprimira un mensaje
        echo "Hubo un error al conectarse con la base de datos.\nMensaje: " . $e->getMessage();
    }
}
