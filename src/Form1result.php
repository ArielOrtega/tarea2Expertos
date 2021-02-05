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
    if (isset($_POST['calcularBtn'])) {
        include "db_connection.php";
        //creo el objeto de conexion
        $connection = createDatabase();
        //preparo la consulta
        $stmt = $connection->prepare("Select * FROM EstiloSexoPromedioRecinto");
        //definimos el modo de fecth que es la forma en como nos retornara los datos
        //FETCH_ASSOC nos devolvera los datos en un array indexado cuyos keys son el nombre de las columnas.
        $stmt->execute();
        $arrayA = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        //definimos la suma de cada tecnica de aprendizale EC, RO, CA, EA
        $EC = $_POST['c5'] + $_POST['c9'] + $_POST['c13'] + $_POST['c17'] + $_POST['c25'] + $_POST['c29'];
        $OR = $_POST['c2'] + $_POST['c10'] + $_POST['c22'] + $_POST['c26'] + $_POST['c30'] + $_POST['c34'];
        $CA = $_POST['c7'] + $_POST['c11'] + $_POST['c15'] + $_POST['c19'] + $_POST['c31'] + $_POST['c35'];
        $EA = $_POST['c4'] + $_POST['c12'] + $_POST['c24'] + $_POST['c28'] + $_POST['c32'] + $_POST['c36'];

        //creamos un arreglo formado de estos 4 valores
        $arrayB = array(
            $CA, $EC, $EA, $OR
        );

        //en esta variable guardaremos el estilo del registro que tenga una mayor probabilidad segun el calculo de bayes con los datos del usuario actual
        $sampleStyle = null;

        //********************************************************/
        //********************************************************/
        //funcion de calculo bayesiano


        //llamo al procedimiento almacenado que me retorna probabilidades CA[0], EC[1], EA[2], OR[3].
        //TOTAL DE REGISTROS ACOMODADOR[4], ASIMILADOR[5], CONVERGENTE[6], DIVERGENTE[7] Y TOTAL REGISTROS [8]
        //si no estan, las calculo y las inserto en la base de datos

        //Realizo una consulta para saber si ya hay registros de probabilidades calculadas, sino se procede a calcularlas e insertarlas en la tabla correspondiente
        $sql = "Select * from ProbabilidadesTecnicas";
        $checkDB = $connection->prepare($sql);
        $checkDB->execute();
        $arrayD = $checkDB->fetchAll(\PDO::FETCH_ASSOC);

        $arrayProbabilidades = [];
        foreach ($arrayD as $item) {
            $arrayProbabilidades = array(
                $item['probabilidadCA'],
                $item['probabilidadEC'],
                $item['probabilidadEA'],
                $item['probabilidadOR']
            );
        }

        //con este if verifico haber recuperado los datos de probabilidades de la base de datos
        if (count($arrayProbabilidades) > 0) {
            $uniqueCA = $arrayProbabilidades[0];
            //valores distintos en EC
            $uniqueEC = $arrayProbabilidades[1];
            //valores distintos en EA
            $uniqueEA = $arrayProbabilidades[2];
            //valores distintos en OR
            $uniqueOR = $arrayProbabilidades[3];

            $pCA = $uniqueCA;
            $pEC = $uniqueEC;
            $pEA = $uniqueEA;
            $pOR = $uniqueOR;
        } else {
            //calculamos la cantidad de valores distintos en cada atributo *
            //valores distintos en CA
            $allCA = array_column($arrayA, 'CA');
            $uniqueCA = array_unique($allCA);
            //valores distintos en EC
            $allEC = array_column($arrayA, 'EC');
            $uniqueEC = array_unique($allEC);
            //valores distintos en EA
            $allEA = array_column($arrayA, 'EA');
            $uniqueEA = array_unique($allEA);
            //valores distintos en OR
            $allOR = array_column($arrayA, 'OR');
            $uniqueOR = array_unique($allOR);

            $pCA = 1 / count($uniqueCA);
            $pEC = 1 / count($uniqueEC);
            $pEA = 1 / count($uniqueEA);
            $pOR = 1 / count($uniqueOR);

            //guardo los datos en la base de datos para proximos usos
            $insertProbabilidades = $connection->prepare("Insert into ProbabilidadesTecnicas values($pCA, $pEC, $pEA, $pOR)");
            $insertProbabilidades->execute();
        }


        //Realizo una consulta para conocer el total de instancias de registros e instancias en la BD, sino las calculo y procedo a insertar
        $sql2 = "Select * from TotalRegistros";
        $registrosDB = $connection->prepare($sql2);
        $registrosDB->execute();
        $arrayC = $registrosDB->fetchAll(\PDO::FETCH_ASSOC);

        $arrayRegistros = [];
        foreach ($arrayC as $item) {
            $arrayRegistros = array(
                $item['TotalRegistrosAsimilador'],
                $item['TotalRegistrosAcomodador'],
                $item['TotalRegistrosConvergente'],
                $item['TotalRegistrosDivergente'],
                $item['TotalRegistrosEstilo']

            );
        }

        $totalAsimilador = 0;
        $totalAcomodador = 0;
        $totalConvergente = 0;
        $totalDivergente = 0;

        $totalRegistros = 0;
        //verifico si en la base de datos ya estan calculados esos datos
        if (count($arrayRegistros) > 0) {
            $totalAsimilador = $arrayRegistros[0];
            $totalAcomodador = $arrayRegistros[1];
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
                } else if (strcasecmp($arrayA[$i]['Estilo'], 'CONVERGENTE') == 0) {
                    $totalConvergente++;
                } else if (strcasecmp($arrayA[$i]['Estilo'], 'DIVERGENTE') == 0) {
                    $totalDivergente++;
                }
            }

            $totalRegistros = count($arrayA);

            //guardo los datos para futuros calculos
            $insertTotalRegistros = $connection->prepare("Insert into TotalRegistros values($totalAsimilador, $totalAcomodador, $totalConvergente, $totalDivergente, $totalRegistros)");
            $insertTotalRegistros->execute();
        }


        //definimos un M
        $m = 4;

        //creo variables para comparar cuantas veces se repite el valor de un atributo con una clase
        //ACOMODADOR
        $acomodadorCA = 0;
        $acomodadorEC = 0;
        $acomodadorEA = 0;
        $acomodadorOR = 0;
        //ASIMILADOR
        $asimiladorCA = 0;
        $asimiladorEC = 0;
        $asimiladorEA = 0;
        $asimiladorOR = 0;
        //CONVERGENTE
        $convergenteCA = 0;
        $convergenteEC = 0;
        $convergenteEA = 0;
        $convergenteOR = 0;
        //DIVERGENTE
        $divergenteCA = 0;
        $divergenteEC = 0;
        $divergenteEA = 0;
        $divergenteOR = 0;

        for ($i = 0; $i < count($arrayA); $i++) {
            if ($arrayA[$i]['Estilo'] == 'ACOMODADOR') {
                //si el registro es acomodador averiguo si contiene,  valores CA,EC,EA o OR similares a los de entrada
                if ($arrayA[$i]['CA'] == $arrayB[0]) {
                    $acomodadorCA++;
                } else if ($arrayA[$i]['EC'] == $arrayB[1]) {
                    $acomodadorEC++;
                } else if ($arrayA[$i]['EA'] == $arrayB[2]) {
                    $acomodadorEA++;
                } else if ($arrayA[$i]['OR'] == $arrayB[3]) {
                    $acomodadorOR++;
                }
            } else if ($arrayA[$i]['Estilo'] == 'ASIMILADOR') {
                //si el registro es acomodador averiguo si contiene,  valores CA,EC,EA o OR similares a los de entrada
                if ($arrayA[$i]['CA'] == $arrayB[0]) {
                    $asimiladorCA++;
                } else if ($arrayA[$i]['EC'] == $arrayB[1]) {
                    $asimiladorEC++;
                } else if ($arrayA[$i]['EA'] == $arrayB[2]) {
                    $asimiladorEA++;
                } else if ($arrayA[$i]['OR'] == $arrayB[3]) {
                    $asimiladorOR++;
                }
            } else if ($arrayA[$i]['Estilo'] == 'CONVERGENTE') {
                //si el registro es acomodador averiguo si contiene,  valores CA,EC,EA o OR similares a los de entrada
                if ($arrayA[$i]['CA'] == $arrayB[0]) {
                    $convergenteCA++;
                } else if ($arrayA[$i]['EC'] == $arrayB[1]) {
                    $convergenteEC++;
                } else if ($arrayA[$i]['EA'] == $arrayB[2]) {
                    $convergenteEA++;
                } else if ($arrayA[$i]['OR'] == $arrayB[3]) {
                    $convergenteOR++;
                }
            } else if ($arrayA[$i]['Estilo'] == 'DIVERGENTE') {
                //si el registro es acomodador averiguo si contiene,  valores CA,EC,EA o OR similares a los de entrada
                if ($arrayA[$i]['CA'] == $arrayB[0]) {
                    $divergenteCA++;
                } else if ($arrayA[$i]['EC'] == $arrayB[1]) {
                    $divergenteEC++;
                } else if ($arrayA[$i]['EA'] == $arrayB[2]) {
                    $divergenteEA++;
                } else if ($arrayA[$i]['OR'] == $arrayB[3]) {
                    $divergenteOR++;
                }
            }
        }



        //calculamos la probabilidad de las frecuencias
        //CA, EC, EA, OR ACOMODADOR
        $frecuenciaAcomodadorCA = (($acomodadorCA + ($m * ($pCA))) / $totalAcomodador + $m);
        $frecuenciaAcomodadorEC = (($acomodadorEC + ($m * ($pEC))) / $totalAcomodador + $m);
        $frecuenciaAcomodadorEA = (($acomodadorEA + ($m * ($pEA))) / $totalAcomodador + $m);
        $frecuenciaAcomodadorOR = (($acomodadorOR + ($m * ($pOR))) / $totalAcomodador + $m);

        //CA, EC, EA, OR ASIMILADOR
        $frecuenciaAsimiladorCA = (($asimiladorCA + ($m * ($pCA))) / $totalAsimilador + $m);
        $frecuenciaAsimiladorEC = (($asimiladorEC + ($m * ($pEC))) / $totalAsimilador + $m);
        $frecuenciaAsimiladorEA = (($asimiladorEA + ($m * ($pEA))) / $totalAsimilador + $m);
        $frecuenciaAsimiladorOR = (($asimiladorOR + ($m * ($pOR))) / $totalAsimilador + $m);

        //CA, EC, EA, OR CONVERGENTE
        $frecuenciaConvergenteCA = (($convergenteCA + ($m * ($pCA))) / $totalConvergente + $m);
        $frecuenciaConvergenteEC = (($convergenteEC + ($m * ($pEC))) / $totalConvergente + $m);
        $frecuenciaConvergenteEA = (($convergenteEA + ($m * ($pEA))) / $totalConvergente + $m);
        $frecuenciaConvergenteOR = (($convergenteOR + ($m * ($pOR))) / $totalConvergente + $m);

        //CA, EC, EA, OR DIVERGENTE
        $frecuenciaDivergenteCA = (($divergenteCA + ($m * ($pCA))) / $totalDivergente + $m);
        $frecuenciaDivergenteEC = (($divergenteEC + ($m * ($pEC))) / $totalDivergente + $m);
        $frecuenciaDivergenteEA = (($divergenteEA + ($m * ($pEA))) / $totalDivergente + $m);
        $frecuenciaDivergenteOR = (($divergenteOR + ($m * ($pOR))) / $totalDivergente + $m);


        //calculamos productos de frecuencias
        //acomodador
        $acomodadorProducto = $frecuenciaAcomodadorCA * $frecuenciaAcomodadorEC * $frecuenciaAcomodadorEA * $frecuenciaAcomodadorOR;
        //asimilador
        $asimiladorProducto = $frecuenciaAsimiladorCA * $frecuenciaAsimiladorEC * $frecuenciaAsimiladorEA * $frecuenciaAsimiladorOR;
        //convergente
        $convergenteProducto = $frecuenciaConvergenteCA * $frecuenciaConvergenteEC * $frecuenciaConvergenteEA * $frecuenciaConvergenteOR;
        //divergente
        $divergenteProducto = $frecuenciaDivergenteCA * $frecuenciaDivergenteEC * $frecuenciaDivergenteEA * $frecuenciaDivergenteOR;

        //Calculo la probabilidad total a partir del producto de frecuencias y cantidad de clases
        $probabilidadAcomodador = $acomodadorProducto * ($totalAcomodador / $totalRegistros);
        $probabilidadAsimilador = $asimiladorProducto * ($totalAsimilador / $totalRegistros);
        $probabilidadConvergente = $convergenteProducto * ($totalConvergente / $totalRegistros);
        $probabilidadDivergente = $divergenteProducto * ($totalDivergente / $totalRegistros);


        //Teniendo las probabilidades calculadas, obtenemos la mas alta
        if ($probabilidadAcomodador > $probabilidadAsimilador && $probabilidadAcomodador > $probabilidadConvergente && $probabilidadAcomodador > $probabilidadDivergente) {
            $sampleStyle = 'Acomodador';
        } else if ($probabilidadAsimilador > $probabilidadAcomodador && $probabilidadAsimilador > $probabilidadConvergente && $probabilidadAsimilador > $probabilidadDivergente) {
            $sampleStyle = 'Asimilador';
        } else if ($probabilidadConvergente > $probabilidadAcomodador && $probabilidadConvergente > $probabilidadAsimilador && $probabilidadConvergente > $probabilidadDivergente) {
            $sampleStyle = 'Convergente';
        } else {
            $sampleStyle = 'Divergente';
        }



        //cierro la conexion con la base de datos para no saturarla
        $connection = null;
    }
    ?>

    <div class='container' style='border: 1px solid black;padding: 20px;'>
        <font color='#2574a9'>
            <font size='6'>Estilo : <?php echo $sampleStyle; ?></font>
        </font>
    </div>;
    <div class='container' style='border: 1px solid black;padding: 10px;'>
        <label for='ec'>Experimentacion Concreta</label>
        <input id='ec' value=<?php echo $EC; ?> size='30'>
        <label for='ec'>Observacion reflexiva</label>
        <input id='or' value=<?php echo $OR; ?> size='30'>
        <label for='ec'>Conceptualizacion abstracta</label>
        <input id='ca' value=<?php echo $CA; ?> size='30'>
        <label for='ec'>Experimentacion activa</label>
        <input id='ea' value=<?php echo $EA; ?> size='30'>
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
                    <th scope="row">CA</th>
                    <td scope="row"><?php echo $frecuenciaAcomodadorCA; ?></td>
                    <td><?php echo $frecuenciaAsimiladorCA; ?></td>
                    <td><?php echo $frecuenciaConvergenteCA; ?></td>
                    <td><?php echo $frecuenciaDivergenteCA; ?></td>
                </tr>
                <tr>
                    <th scope="row">EC</th>
                    <td scope="row"><?php echo $frecuenciaAcomodadorEC; ?></td>
                    <td><?php echo $frecuenciaAsimiladorEC; ?></td>
                    <td><?php echo $frecuenciaConvergenteEC; ?></td>
                    <td><?php echo $frecuenciaDivergenteEC; ?></td>
                </tr>
                <tr>
                    <th scope="row">EA</th>
                    <td scope="row"><?php echo $frecuenciaAcomodadorEA; ?></td>
                    <td><?php echo $frecuenciaAsimiladorEA; ?></td>
                    <td><?php echo $frecuenciaConvergenteEA; ?></td>
                    <td><?php echo $frecuenciaDivergenteEA; ?></td>
                </tr>
                <tr>
                    <th scope="row">OR</th>
                    <td scope="row"><?php echo $frecuenciaAcomodadorOR; ?></td>
                    <td><?php echo $frecuenciaAsimiladorOR; ?></td>
                    <td><?php echo $frecuenciaConvergenteOR; ?></td>
                    <td><?php echo $frecuenciaDivergenteOR; ?></td>
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
<script>
    //Calculos ya brindados por el profesor, se encargan de realizar la suma para diferentes tecnicas de aprendizaje
    function calcular() {
        ec = parseInt(document.estilo.c5.value) + parseInt(document.estilo.c9.value) + parseInt(document.estilo.c13.value) + parseInt(document.estilo.c17.value) + parseInt(document.estilo.c25.value) + parseInt(document.estilo.c29.value);
        or = parseInt(document.estilo.c2.value) + parseInt(document.estilo.c10.value) + parseInt(document.estilo.c22.value) + parseInt(document.estilo.c26.value) + parseInt(document.estilo.c30.value) + parseInt(document.estilo.c34.value);
        ca = parseInt(document.estilo.c7.value) + parseInt(document.estilo.c11.value) + parseInt(document.estilo.c15.value) + parseInt(document.estilo.c19.value) + parseInt(document.estilo.c31.value) + parseInt(document.estilo.c35.value);
        ea = parseInt(document.estilo.c4.value) + parseInt(document.estilo.c12.value) + parseInt(document.estilo.c24.value) + parseInt(document.estilo.c28.value) + parseInt(document.estilo.c32.value) + parseInt(document.estilo.c36.value);

        document.final.EC.value = ec;
        document.final.RO.value = or;
        document.final.CA.value = ca;
        document.final.EA.value = ea;
    }
</script>
<style>
    label {
        margin: 20px;
    }
</style>

</html>