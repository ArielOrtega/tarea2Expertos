<!DOCTYPE html>
<html>

<head>

    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <title>Estilos</title>
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
    <?php
        include "db_connection.php";
        //creo el objeto de conexion
        $connection = createDatabase();
        //preparo la consulta
        $stmt = $connection->prepare("Select * FROM EstiloSexoPromedioRecinto");
        //definimos el modo de fecth que es la forma en como nos retornara los datos
        //FETCH_ASSOC nos devolvera los datos en un array indexado cuyos keys son el nombre de las columnas.
        $stmt->execute();
        $arrayA = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        //obtenemos las caracteristicas brindadas por el usuario
        $estilo = $_POST['Estilo'];
        $promedio = floatval($_POST['Promedio']);
        $sexo = $_POST['Sexo'];

        //creamos un arreglo formado de estos 3 valores
        $arrayB = array(
            $sexo, $promedio, $estilo 
        );

        
        //en esta variable guardaremos el recinto del registro que tenga una mayor probabilidad segun el calculo de bayes con los datos del usuario actual
        $sampleRecinto = null;
        //para que bestSample no tenga un valor de nulo

        //Realizo una consulta para saber si ya hay registros de probabilidades calculadas, sino se procede a calcularlas e insertarlas en la tabla correspondiente
        $sql = "Select * from ProbabilidadesRecinto";
        $checkDB = $connection->prepare($sql);
        $checkDB->execute();
        $arrayD = $checkDB->fetchAll(\PDO::FETCH_ASSOC);

        $arrayProbabilidades = [];
        foreach ($arrayD as $item) {
            $arrayProbabilidades = array(
                $item['pSexo'],
                $item['pPromedio'],
                $item['pEstilo']
            );
        }

        //con este if verifico haber recuperado los datos de probabilidades de la base de datos
        if (count($arrayProbabilidades) > 0) {
            $uniqueSexo = $arrayProbabilidades[0];
            //valores distintos en EC
            $uniquePromedio = $arrayProbabilidades[1];
            //valores distintos en EA
            $uniqueEstilo = $arrayProbabilidades[2];

            $pSexo = $uniqueSexo;
            $pPromedio = $uniquePromedio;
            $pEstilo = $uniqueEstilo;
        } else {
            //calculamos la cantidad de valores distintos en cada atributo *
            //valores distintos en Sexo
            $allSexo = array_column($arrayA, 'Sexo');
            $uniqueSexo = array_unique($allSexo);
            //valores distintos en Promedio
            $allPromedio = array_column($arrayA, 'Promedio');
            $uniquePromedio = array_unique($allPromedio);
            //valores distintos en Estilo
            $allEstilo = array_column($arrayA, 'Estilo');
            $uniqueEstilo = array_unique($allEstilo);

            $pSexo = 1 / count($uniqueSexo);
            $pPromedio = 1 / count($uniquePromedio);
            $pEstilo = 1 / count($uniqueEstilo);

            //guardo los datos en la base de datos para proximos usos
            $insertProbabilidades = $connection->prepare("Insert into ProbabilidadesRecinto values($pSexo, $pPromedio, $pEstilo)");
            $insertProbabilidades->execute();
        }


        //Realizo una consulta para conocer el total de instancias de registros e instancias en la BD, sino las calculo y procedo a insertar
        $sql2 = "Select * from TotalRegistrosRecinto";
        $registrosDB = $connection->prepare($sql2);
        $registrosDB->execute();
        $arrayC = $registrosDB->fetchAll(\PDO::FETCH_ASSOC);

        $arrayRegistros = [];
        foreach ($arrayC as $item) {
            $arrayRegistros = array(
                $item['TotalParaiso'],
                $item['TotalTurrialba'],
                $item['TotalRegistros']
            );
        }

        $totalParaiso = 0;
        $totalTurrialba = 0;

        $totalRegistros = 0;
        //verifico si en la base de datos ya estan calculados esos datos
        if (count($arrayRegistros) > 0) {
            $totalParaiso = $arrayRegistros[0];
            $totalTurrialba = $arrayRegistros[1];

            $totalRegistros = $arrayRegistros[2];

            //si no es asi, los calculo y los inserto en la base
        } else {

            //cuento cuantos registros hay en cada estilo, para conocer la cantidad de instancias
            for ($i = 0; $i < count($arrayA); $i++) {
                if (strcasecmp($arrayA[$i]['Recinto'], 'Paraiso') == 0) {
                    $totalParaiso++;
                } else if (strcasecmp($arrayA[$i]['Recinto'], 'Turrialba') == 0) {
                    $totalTurrialba++;
                }
            }

            $totalRegistros = count($arrayA);

            //guardo los datos para futuros calculos
            $insertTotalRegistros = $connection->prepare("Insert into TotalRegistrosRecinto values($totalParaiso, $totalTurrialba, $totalRegistros)");
            $insertTotalRegistros->execute();
        }


        //definimos un M, en este caso son 3 atributos
        $m = 3;

        //creo variables para comparar cuantas veces se repite el valor de un atributo con una clase
        //Paraiso
        $paraisoSexo = 0;
        $paraisoPromedio = 0;
        $paraisoEstilo = 0;
        //Turrialba
        $turrialbaSexo = 0;
        $turrialbaPromedio = 0;
        $turrialbaEstilo = 0;

        for ($i = 0; $i < count($arrayA); $i++) {
            if ($arrayA[$i]['Recinto'] == 'Paraiso') {
                //si el registro es paraiso averiguo si contiene,  valores sexo, promedio o estilo similares a los de entrada
                if ($arrayA[$i]['Sexo'] == $arrayB[0]) {
                    $paraisoSexo++;
                } else if ($arrayA[$i]['Promedio'] == $arrayB[1]) {
                    $paraisoPromedio++;
                } else if ($arrayA[$i]['Estilo'] == $arrayB[2]) {
                    $paraisoEstilo++;
                }
            } else if ($arrayA[$i]['Recinto'] == 'Turrialba') {
                //si el registro es Turrialba averiguo si contiene,  valores sexo, promedio o estilo similares a los de entrada
                if ($arrayA[$i]['Sexo'] == $arrayB[0]) {
                    $turrialbaSexo++;
                } else if ($arrayA[$i]['Promedio'] == $arrayB[1]) {
                    $turrialbaPromedio++;
                } else if ($arrayA[$i]['Estilo'] == $arrayB[2]) {
                    $turrialbaEstilo++;
                }
            }
        }



        //calculamos la probabilidad de las frecuencias
        //Sexo, promedio, estilo Paraiso
        $frecuenciaParaisoSexo = (($paraisoSexo + ($m * ($pSexo))) / $totalParaiso + $m);
        $frecuenciaParaisoPromedio = (($paraisoPromedio + ($m * ($pPromedio))) / $totalParaiso + $m);
        $frecuenciaParaisoEstilo = (($paraisoEstilo + ($m * ($pEstilo))) / $totalParaiso + $m);

        //Sexo, promedio, estilo Turrialba
        $frecuenciaTurrialbaSexo = (($turrialbaSexo + ($m * ($pSexo))) / $totalTurrialba + $m);
        $frecuenciaTurrialbaPromedio = (($turrialbaPromedio + ($m * ($pPromedio))) / $totalTurrialba + $m);
        $frecuenciaTurrialbaEstilo = (($turrialbaEstilo + ($m * ($pEstilo))) / $totalTurrialba + $m);


        //calculamos productos de frecuencias
        //Paraiso
        $paraisoProducto = $frecuenciaParaisoSexo * $frecuenciaParaisoPromedio * $frecuenciaParaisoEstilo;
        //Paraiso
        $turrialbaProducto = $frecuenciaTurrialbaSexo * $frecuenciaTurrialbaPromedio * $frecuenciaTurrialbaEstilo;

        //Calculo la probabilidad total a partir del producto de frecuencias y cantidad de clases
        $probabilidadParaiso = $paraisoProducto * ($totalParaiso / $totalRegistros);
        $probabilidadTurrialba = $turrialbaProducto * ($totalTurrialba / $totalRegistros);


        //Teniendo las probabilidades calculadas, obtenemos la mas alta
        if ($probabilidadParaiso >= $probabilidadTurrialba) {
            $sampleRecinto = 'Paraiso';
        } else if ($probabilidadTurrialba > $probabilidadParaiso) {
            $sampleRecinto = 'Turrialba';
        }



        //cierro la conexion con la base de datos para no saturarla
        $connection = null;
    ?>

    <div class='container' style='border: 1px solid black;padding: 20px;'>
        <font color='#2574a9'>
            <font size='6'>Recinto : <?php echo $sampleRecinto; ?></font>
        </font>
    </div>;
    <div class='container' style='border: 1px solid black;padding: 10px;'>
        <label for='ec'>Sexo</label>
        <input id='ec' value=<?php echo $sexo; ?> size='30'>
        <label for='ec'>Promedio</label>
        <input id='or' value=<?php echo $promedio; ?> size='30'>
        <label for='ec'>Estilo</label>
        <input id='ca' value=<?php echo $estilo; ?> size='30'>
    </div>
    <br>
    <div class="container">
        <h4>Instancias</h4>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Paraiso</th>
                    <th scope="col">Turrialba</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Cantidad</th>
                    <td><?php echo $totalParaiso; ?></td>
                    <td><?php echo $totalTurrialba; ?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h4>Frecuencias</h4>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Paraiso</th>
                    <th scope="col">Turrialba</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Sexo</th>
                    <td scope="row"><?php echo $frecuenciaParaisoSexo; ?></td>
                    <td><?php echo $frecuenciaTurrialbaSexo; ?></td>
                </tr>
                <tr>
                    <th scope="row">Promedio</th>
                    <td scope="row"><?php echo $frecuenciaParaisoPromedio; ?></td>
                    <td><?php echo $frecuenciaTurrialbaPromedio; ?></td>
                </tr>
                <tr>
                    <th scope="row">Estilo</th>
                    <td scope="row"><?php echo $frecuenciaParaisoEstilo; ?></td>
                    <td><?php echo $frecuenciaTurrialbaEstilo; ?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h4>Probabilidad</h4>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Paraiso</th>
                    <th scope="col">Turrialba</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Porcentaje</th>
                    <td><?php echo $probabilidadParaiso; ?></td>
                    <td><?php echo $probabilidadTurrialba; ?></td>
                </tr>
            </tbody>
        </table>
    </div>


</body>
<style>
    label {
        margin: 20px;
    }
</style>

</html>