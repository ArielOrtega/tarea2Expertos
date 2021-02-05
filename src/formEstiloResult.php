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
    $sexo = $_POST['Sexo'];
    $promedio = floatval($_POST['Promedio']);
    $recinto = $_POST['Recinto'];



    //creamos un arreglo formado de estos 3 valores
    $arrayB = array(
        $recinto, $promedio, $sexo
    );


    //en esta variable guardaremos el recinto del registro que tenga una mayor probabilidad segun el calculo de bayes con los datos del usuario actual
    $sampleRecinto = null;
    //para que bestSample no tenga un valor de nulo

    //Realizo una consulta para saber si ya hay registros de probabilidades calculadas, sino se procede a calcularlas e insertarlas en la tabla correspondiente
    $sql = "Select * from ProbabilidadesEstilo";
    $checkDB = $connection->prepare($sql);
    $checkDB->execute();
    $arrayD = $checkDB->fetchAll(\PDO::FETCH_ASSOC);

    $arrayProbabilidades = [];
    foreach ($arrayD as $item) {
        $arrayProbabilidades = array(
            $item['pRecinto'],
            $item['pPromedio'],
            $item['pSexo']
        );
    }

    //con este if verifico haber recuperado los datos de probabilidades de la base de datos
    if (count($arrayProbabilidades) > 0) {
        $uniqueRecinto = $arrayProbabilidades[0];
        //valores distintos en EC
        $uniquePromedio = $arrayProbabilidades[1];
        //valores distintos en EA
        $uniqueSexo = $arrayProbabilidades[2];

        $pRecinto = $uniqueRecinto;
        $pPromedio = $uniquePromedio;
        $pSexo = $uniqueSexo;
    } else {
        //calculamos la cantidad de valores distintos en cada atributo *
        //valores distintos en Sexo
        $allRecinto = array_column($arrayA, 'Recinto');
        $uniqueRecinto = array_unique($allRecinto);
        //valores distintos en Promedio
        $allPromedio = array_column($arrayA, 'Promedio');
        $uniquePromedio = array_unique($allPromedio);
        //valores distintos en Estilo
        $allSexo = array_column($arrayA, 'Sexo');
        $uniqueSexo = array_unique($allSexo);

        $pRecinto = 1 / count($uniqueRecinto);
        $pPromedio = 1 / count($uniquePromedio);
        $pSexo = 1 / count($uniqueSexo);

        //guardo los datos en la base de datos para proximos usos
        $insertProbabilidades = $connection->prepare("Insert into ProbabilidadesEstilo values($pRecinto, $pPromedio, $pSexo)");
        $insertProbabilidades->execute();
    }


    //Realizo una consulta para conocer el total de instancias de registros e instancias en la BD, sino las calculo y procedo a insertar
    $sql2 = "Select * from TotalRegistrosEstilo";
    $registrosDB = $connection->prepare($sql2);
    $registrosDB->execute();
    $arrayC = $registrosDB->fetchAll(\PDO::FETCH_ASSOC);

    $arrayRegistros = [];
    foreach ($arrayC as $item) {
        $arrayRegistros = array(
            $item['TotalAcomodador'],
            $item['TotalAsimilador'],
            $item['TotalConvergente'],
            $item['TotalDivergente'],
            $item['TotalRegistros']
        );
    }

    $totalAcomodador = 0;
    $totalAsimilador = 0;
    $totalConvergente = 0;
    $totalDivergente = 0;

    $totalRegistros = 0;
    //verifico si en la base de datos ya estan calculados esos datos
    if (count($arrayRegistros) > 0) {
        $totalAcomodador = $arrayRegistros[0];
        $totalAsimilador = $arrayRegistros[1];
        $totalConvergente = $arrayRegistros[2];
        $totalDivergente = $arrayRegistros[3];

        $totalRegistros = $arrayRegistros[4];

        //si no es asi, los calculo y los inserto en la base
    } else {

        //cuento cuantos registros hay en cada estilo, para conocer la cantidad de instancias
        for ($i = 0; $i < count($arrayA); $i++) {
            if (strcasecmp($arrayA[$i]['Estilo'], 'ACOMODADOR') == 0) {
                $totalAcomodador++;
            } else if (strcasecmp($arrayA[$i]['Estilo'], 'ASIMILADOR') == 0) {
                $totalAsimilador++;
            }else if (strcasecmp($arrayA[$i]['Estilo'], 'CONVERGENTE') == 0) {
                $totalConvergente++;
            }else if (strcasecmp($arrayA[$i]['Estilo'], 'DIVERGENTE') == 0) {
                $totalDivergente++;
            }
        }

        $totalRegistros = count($arrayA);

        //guardo los datos para futuros calculos
        $insertTotalRegistros = $connection->prepare("Insert into TotalRegistrosEstilo values($totalAcomodador, $totalAsimilador, $totalConvergente, $totalDivergente, $totalRegistros)");
        $insertTotalRegistros->execute();
    }


    //definimos un M, en este caso son 3 atributos
    $m = 3;

    //creo variables para comparar cuantas veces se repite el valor de un atributo con una clase
    //Acomodador
    $acomodadorRecinto = 0;
    $acomodadorPromedio = 0;
    $acomodadorSexo = 0;
    //asimilador
    $asimiladorRecinto = 0;
    $asimiladorPromedio = 0;
    $asimiladorSexo = 0;
    //convergente
    $convergenteRecinto = 0;
    $convergentePromedio = 0;
    $convergenteSexo = 0;
    //divergente
    $divergenteRecinto = 0;
    $divergentePromedio = 0;
    $divergenteSexo = 0;

    for ($i = 0; $i < count($arrayA); $i++) {
        if ($arrayA[$i]['Estilo'] == 'ACOMODADOR') {
            //si el registro es paraiso averiguo si contiene,  valores sexo, promedio o estilo similares a los de entrada
            if ($arrayA[$i]['Recinto'] == $arrayB[0]) {
                $acomodadorRecinto++;
            } else if ($arrayA[$i]['Promedio'] == $arrayB[1]) {
                $acomodadorPromedio++;
            } else if ($arrayA[$i]['Sexo'] == $arrayB[2]) {
                $acomodadorSexo++;
            }
        } else if ($arrayA[$i]['Estilo'] == 'ASIMILADOR') {
            //si el registro es Turrialba averiguo si contiene,  valores sexo, promedio o estilo similares a los de entrada
            if ($arrayA[$i]['Recinto'] == $arrayB[0]) {
                $asimiladorRecinto++;
            } else if ($arrayA[$i]['Promedio'] == $arrayB[1]) {
                $asimiladorPromedio++;
            } else if ($arrayA[$i]['Sexo'] == $arrayB[2]) {
                $asimiladorSexo++;
            }
        } else if ($arrayA[$i]['Estilo'] == 'CONVERGENTE') {
            //si el registro es Turrialba averiguo si contiene,  valores sexo, promedio o estilo similares a los de entrada
            if ($arrayA[$i]['Recinto'] == $arrayB[0]) {
                $convergenteRecinto++;
            } else if ($arrayA[$i]['Promedio'] == $arrayB[1]) {
                $convergentePromedio++;
            } else if ($arrayA[$i]['Sexo'] == $arrayB[2]) {
                $convergenteSexo++;
            }
        } else if ($arrayA[$i]['Estilo'] == 'DIVERGENTE') {
            //si el registro es Turrialba averiguo si contiene,  valores sexo, promedio o estilo similares a los de entrada
            if ($arrayA[$i]['Recinto'] == $arrayB[0]) {
                $divergenteRecinto++;
            } else if ($arrayA[$i]['Promedio'] == $arrayB[1]) {
                $divergentePromedio++;
            } else if ($arrayA[$i]['Sexo'] == $arrayB[2]) {
                $divergenteSexo++;
            }
        }
    }



    //calculamos la probabilidad de las frecuencias
    //Recinto, promedio, Sexo Acomodador
    $frecuenciaAcomodadorRecinto = (($acomodadorRecinto + ($m * ($pRecinto))) / $totalAcomodador + $m);
    $frecuenciaAcomodadorPromedio = (($acomodadorPromedio + ($m * ($pPromedio))) / $totalAcomodador + $m);
    $frecuenciaAcomodadorSexo = (($acomodadorSexo + ($m * ($pSexo))) / $totalAcomodador + $m);

    //Recinto, promedio, Sexo Asimilador
    $frecuenciaAsimiladorRecinto = (($asimiladorRecinto + ($m * ($pRecinto))) / $totalAsimilador + $m);
    $frecuenciaAsimiladorPromedio = (($asimiladorPromedio + ($m * ($pPromedio))) / $totalAsimilador + $m);
    $frecuenciaAsimiladorSexo = (($asimiladorSexo + ($m * ($pSexo))) / $totalAsimilador + $m);

    //Recinto, promedio, Sexo Convergente
    $frecuenciaConvergenteRecinto = (($convergenteRecinto + ($m * ($pRecinto))) / $totalConvergente + $m);
    $frecuenciaConvergentePromedio = (($convergentePromedio + ($m * ($pPromedio))) / $totalConvergente + $m);
    $frecuenciaConvergenteSexo = (($convergenteSexo + ($m * ($pSexo))) / $totalConvergente + $m);

    //Recinto, promedio, Sexo Divergente
    $frecuenciaDivergenteRecinto = (($divergenteRecinto + ($m * ($pRecinto))) / $totalDivergente + $m);
    $frecuenciaDivergentePromedio = (($divergentePromedio + ($m * ($pPromedio))) / $totalDivergente + $m);
    $frecuenciaDivergenteSexo = (($divergenteSexo + ($m * ($pSexo))) / $totalDivergente + $m);


    //calculamos productos de frecuencias
    //Acomodador
    $acomodadorProducto = $frecuenciaAcomodadorRecinto * $frecuenciaAcomodadorPromedio * $frecuenciaAcomodadorSexo;
    //asimilador
    $asimiladorProducto = $frecuenciaAsimiladorRecinto * $frecuenciaAsimiladorPromedio * $frecuenciaAsimiladorSexo;
    //Convergente
    $convergenteProducto = $frecuenciaConvergenteRecinto * $frecuenciaConvergentePromedio * $frecuenciaConvergenteSexo;
    //divergente
    $divergenteProducto = $frecuenciaDivergenteRecinto * $frecuenciaDivergentePromedio * $frecuenciaDivergenteSexo;

    //Calculo la probabilidad total a partir del producto de frecuencias y cantidad de clases
    $probabilidadAcomodador = $acomodadorProducto * ($totalAcomodador / $totalRegistros);
    $probabilidadAsimilador = $asimiladorProducto * ($totalAsimilador / $totalRegistros);
    $probabilidadConvergente = $convergenteProducto * ($totalConvergente / $totalRegistros);
    $probabilidadDivergente = $divergenteProducto * ($totalDivergente / $totalRegistros);


    //Teniendo las probabilidades calculadas, obtenemos la mas alta
    if ($probabilidadAcomodador > $probabilidadAsimilador && $probabilidadAcomodador > $probabilidadConvergente && $probabilidadAcomodador > $probabilidadDivergente) {
        $sampleRecinto = 'Acomodador';
    } else if ($probabilidadAsimilador > $probabilidadAcomodador && $probabilidadAsimilador > $probabilidadConvergente && $probabilidadAsimilador> $probabilidadDivergente) {
        $sampleRecinto = 'Asimilador';
    } else if ($probabilidadConvergente > $probabilidadAcomodador && $probabilidadConvergente > $probabilidadAsimilador && $probabilidadConvergente> $probabilidadDivergente) {
        $sampleRecinto = 'Convergente';
    } else if ($probabilidadDivergente > $probabilidadAcomodador && $probabilidadDivergente > $probabilidadConvergente && $probabilidadDivergente> $probabilidadAsimilador) {
        $sampleRecinto = 'Divergente';
    }



    //cierro la conexion con la base de datos para no saturarla
    $connection = null;
    ?>

    <div class='container' style='border: 1px solid black;padding: 20px;'>
        <font color='#2574a9'>
            <font size='6'>Estilo : <?php echo $sampleRecinto; ?></font>
        </font>
    </div>;
    <div class='container' style='border: 1px solid black;padding: 10px;'>
        <label for='ec'>Recinto</label>
        <input id='ec' value=<?php echo $recinto; ?> size='30'>
        <label for='ec'>Promedio</label>
        <input id='or' value=<?php echo $promedio; ?> size='30'>
        <label for='ec'>Sexo</label>
        <input id='ca' value=<?php echo $sexo; ?> size='30'>
    </div>
    <br>
    <div class="container">
        <h4>Instancias</h4>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Acomodador</th>
                    <th scope="col">Asimilador</th>
                    <th scope="col">Convergente</th>
                    <th scope="col">Divergente</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Cantidad</th>
                    <td><?php echo $totalAcomodador; ?></td>
                    <td><?php echo $totalAsimilador; ?></td>
                    <td><?php echo $totalConvergente; ?></td>
                    <td><?php echo $totalDivergente; ?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h4>Frecuencias</h4>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Acomodador</th>
                    <th scope="col">Asimilador</th>
                    <th scope="col">Convergente</th>
                    <th scope="col">Divergente</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Recinto</th>
                    <td scope="row"><?php echo $frecuenciaAcomodadorRecinto; ?></td>
                    <td><?php echo $frecuenciaAsimiladorRecinto; ?></td>
                    <td><?php echo $frecuenciaConvergenteRecinto; ?></td>
                    <td><?php echo $frecuenciaDivergenteRecinto; ?></td>
                </tr>
                <tr>
                    <th scope="row">Promedio</th>
                    <td scope="row"><?php echo $frecuenciaAcomodadorPromedio; ?></td>
                    <td><?php echo $frecuenciaAsimiladorPromedio; ?></td>
                    <td><?php echo $frecuenciaConvergentePromedio; ?></td>
                    <td><?php echo $frecuenciaDivergentePromedio; ?></td>
                </tr>
                <tr>
                    <th scope="row">Sexo</th>
                    <td scope="row"><?php echo $frecuenciaAcomodadorSexo; ?></td>
                    <td><?php echo $frecuenciaAsimiladorSexo; ?></td>
                    <td><?php echo $frecuenciaConvergenteSexo; ?></td>
                    <td><?php echo $frecuenciaDivergenteSexo; ?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h4>Probabilidad</h4>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Acomodador</th>
                    <th scope="col">Asimilador</th>
                    <th scope="col">Convergente</th>
                    <th scope="col">Divergente</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Porcentaje</th>
                    <td><?php echo $probabilidadAcomodador; ?></td>
                    <td><?php echo $probabilidadAsimilador; ?></td>
                    <td><?php echo $probabilidadConvergente; ?></td>
                    <td><?php echo $probabilidadDivergente; ?></td>
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