<!DOCTYPE html>
<html>

<head>

    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <title>Redes</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>



    <meta http-equiv="CONTENT-TYPE" content="text/html; charset=utf-8">


    <meta name="generator" content="Bluefish 2.2.2">
</head>

<body>
    <?php
    //incluyo el header, que contiene la barra de menu, para no repetir el mismo codigo
    include("../header.php");
    ?>
    <h2 class="container">Formulario 6: Adivinar estilo de aprendizaje</h2>
    <br>
    <div class="container" style="border: 1px solid black;padding: 30px;">
        <form method="POST" action="formRedes.php">
            <div class="form-group row">
                <div class="col-sm-3">
                    <label>Indique la fiabilidad:</label>
                </div>
                <div>
                    <input name="Reliability" type="number" min="2" max="5" value="2">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label>Indique el numero de enlaces:</label>
                </div>
                <div>
                    <input name="NumberOfLinks" type="number" min="7" max="20" value="7">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label>Indique la capacidad de red:</label>
                </div>
                <div>
                    <select name="Capacity">
                        <option value="1">Alta</option>
                        <option value="2">Media</option>
                        <option value="3">Baja</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label>Indique el costo de red:</label>
                </div>
                <div>
                    <select name="Cost">
                        <option value="1">Alta</option>
                        <option value="2">Media</option>
                        <option value="3">Baja</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-3">
                    <input name="promedioBtn" type="submit" value="Adivinar">
                </div>
            </div>
        </form>
    </div>
    <?php
    //para poder calcular la distancia euclidiana de texto a caracteres, a cada uno se le asignara un valor numerico
    if (isset($_POST['promedioBtn'])) {
        include "db_connection.php";
        include "calculoEuclidiano.php";
        include "textToNum.php";
        //creo el objeto de conexion
        $connection = createDatabase();
        //preparo la consulta
        $stmt = $connection->prepare("Select * FROM Redes");
        //definimos el modo de fecth que es la forma en como nos retornara los datos
        //FETCH_ASSOC nos devolvera los datos en un array indexado cuyos keys son el nombre de las columnas.
        $stmt->execute();
        $resultArrayBD = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        //obtenemos las caracteristicas brindadas por el usuario
        $Reliability = $_POST['Reliability'];
        $NumberOfLinks = $_POST['NumberOfLinks'];
        $Capacity = $_POST['Capacity'];
        $Cost = $_POST['Cost'];

        //creamos un arreglo formado de estos 3 valores
        $sampleArray = array(
            $Reliability, $NumberOfLinks, $Capacity, $Cost
        );

        //asignamos variable para guardar el mejor valor y compararlo en cada iteracion
        $bestSample = null;
        //en esta variable guardaremos el estilo del registro que tenga una menor distancia euclidiana con los datos del usuario actual
        $sampleClass = null;
        //con esta variable nos aseguramos de que solo el primer valor que retorne la funcion dist() sea asignada automaticamente
        //para que bestSample no tenga un valor de nulo
        $primerContador = 0;
        //Creamos un ciclo para comparar los datos del usuario actual con los guardados en la base de datos
        foreach ($resultArrayBD as $item) {
            $baseArray = array(
                $item['Reliability (R)'],
                $item['Number of links (L)'],
                netToNum($item['Capacity (Ca)']),
                netToNum($item['Costo (Co)']),
                $item['Class']

            );

            $distanciaEuclidiana = dist($sampleArray, $baseArray);
            //nos aseguramos de tener la mejor muestra, en menor es mejor
            if ($distanciaEuclidiana < $bestSample || $primerContador == 0) {
                $bestSample = $distanciaEuclidiana;
                $sampleClass = $baseArray[4]; //obtiene el atributo estilo en el indice 4
                $primerContador++;
            }
        }
        //if para presentar en pantalla 'Masculino o Femenino'
        $CapacityResult = null;
        if ($Capacity == '1') {
            $CapacityResult = 'High';
        } else if ($Capacity == '2'){
            $CapacityResult = 'Medium';
        }else{
            $CapacityResult = 'Low';
        }

        $CostResult = null;
        if ($Cost == '1') {
            $CostResult = 'High';
        } else if ($Cost == '2'){
            $CostResult = 'Medium';
        }else{
            $CostResult = 'Low';
        }

        //uso estos echo para poder imprimir el html con los resultados
        echo "<div class='container' style='border: 1px solid black;padding: 28px;'><font color='#2574a9'><font size='6'>Su calse de red: $sampleClass</font></font><br>";
        echo "<font size='3'>Fiabilidad: $Reliability</font></font><br>";
        echo "<font size='3'>Numero de enlaces: $NumberOfLinks</font></font><br>";
        echo "<font size='3'>Capacidad: $Capacity</font></font><br>";
        echo "<font color='#000000'><font size='3'>Costo: $Cost</font></font></div><br>";

        //cierro la conexion con la base de datos para no saturarla
        $connection = null;
    }
    ?>
</body>

</html>