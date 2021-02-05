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
                $resultArrayBD = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                //definimos la suma de cada tecnica de aprendizale EC, RO, CA, EA
                $EC = $_POST['c5'] + $_POST['c9'] + $_POST['c13'] + $_POST['c17'] + $_POST['c25'] + $_POST['c29'];
                $OR = $_POST['c2'] + $_POST['c10'] + $_POST['c22'] + $_POST['c26'] + $_POST['c30'] + $_POST['c34'];
                $CA = $_POST['c7'] + $_POST['c11'] + $_POST['c15'] + $_POST['c19'] + $_POST['c31'] + $_POST['c35'];
                $EA = $_POST['c4'] + $_POST['c12'] + $_POST['c24'] + $_POST['c28'] + $_POST['c32'] + $_POST['c36'];

                //creamos un arreglo formado de estos 4 valores
                $sampleArray = array(
                        $EC, $OR, $CA, $EA
                );

                //asignamos variable para guardar el mejor valor y compararlo en cada iteracion
                $bestSample = null;
                //en esta variable guardaremos el estilo del registro que tenga una menor distancia euclidiana con los datos del usuario actual
                $sampleStyle = null;
                //con esta variable nos aseguramos de que solo el primer valor que retorne la funcion dist() sea asignada automaticamente
                //para que bestSample no tenga un valor de nulo
                $primerContador = 0;
                //Creamos un ciclo para comparar los datos del usuario actual con los guardados en la base de datos
                foreach ($resultArrayBD as $item) {
                        $baseArray = array(
                                $item['EC'],
                                $item['OR'],
                                $item['CA'],
                                $item['EA'],
                                $item['Estilo']

                        );

                        $distanciaEuclidiana = dist($sampleArray, $baseArray);
                        //nos aseguramos de tener la mejor muestra, en menor es mejor
                        if ($distanciaEuclidiana <= $bestSample || $primerContador == 0) {
                                $bestSample = $distanciaEuclidiana;
                                $sampleStyle = $baseArray[4]; //obtiene el atributo estilo en el indice 4
                                $primerContador++;
                        }
                }


                



                //uso estos echo para poder imprimir el html con los resultados
                echo "<div class='container' style='border: 1px solid black;padding: 28px;'><font color='#2574a9'><font size='6'>Estilo : $sampleStyle</font></font></div><br>";
                echo "<div class='container' style='border: 1px solid black;padding: 28px;'>";
                echo "<label for='ec'>Experimentacion Concreta</label>";
                echo "<input id='ec' value=$EC size='30'>";
                echo "<label for='ec'>Observacion reflexiva</label>";
                echo "<input id='or' value=$OR size='30'>";
                echo "<label for='ec'>Conceptualizacion abstracta</label>";
                echo "<input id='ca' value=$CA size='30'>";
                echo "<label for='ec'>Experimentacion activa</label>";
                echo "<input id='ea' value=$EA size='30'></div>";

                //cierro la conexion con la base de datos para no saturarla
                $connection = null;
        }
        ?>
        <div class="container">
                <p class="western" align="justify" lang="es-ES">
                        <font color="#FF0000">
                                <font size="3"><b>CUAL ES SU ESTILO DE APRENDIZAJE?</b></font>
                        </font>
                </p>
                <p class="western" align="justify" lang="es-ES">
                        <font color="#000000">
                                <font size="3"><b>Instrucciones:</b></font>
                        </font>
                </p>

                <p class="western" align="justify" lang="es-ES">
                        <font color="#000000">
                                <font size="3"> </font>
                        </font>
                </p>

                <p class="western" align="justify" lang="es-ES">
                        <font color="#000000">
                                <font size="3"> Para
                                        utilizar el instrumento usted debe conceder una calificación alta a
                                        aquellas palabras que mejor caracterizan la forma en que usted
                                        aprende, y una calificación baja a las palabras que menos
                                        caracterizan su estilo de aprendizaje.</font>
                        </font>
                </p>

                <p class="western" lang="es-ES"> Le puede ser difícil seleccionar
                        las palabras que mejor describen su estilo de aprendizaje, ya que no
                        hay respuestas correctas o incorrectas.</p>

                <p class="western" align="justify" lang="es-ES">
                        <font color="#000000">
                                <font size="3"> Todas
                                        las respuestas son buenas, ya que el fin del instrumento es describir
                                        cómo y no juzgar su habilidad para aprender.</font>
                        </font>
                </p>

                <p class="western" align="justify" lang="es-ES">
                        <font color="#000000">
                                <font size="3"> De
                                        inmediato encontrará nueve series o líneas de cuatro palabras cada una.
                                        Ordene de mayor a menor cada serie o juego de cuatro palabras que hay en cada línea,
                                        ubicando 4 en la palabra que mejor caracteriza su estilo de
                                        aprendizaje, un 3 en la palabra siguiente en cuanto a la
                                        correspondencia con su estilo; a la siguiente un 2, y un 1 a la
                                        palabra que menos caracteriza su estilo. Tenga cuidado de ubicar un
                                        número distinto al lado de cada palabra en la misma línea. </font>
                        </font>
                </p>
                <big><big>
                                Yo aprendo...</big></big>
                <form method="POST" action="Form1result.php">
                        <table class="table" style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
                                <thead class="thead-dark">
                                        <tr>
                                                <th>Experimentacion concreta</th>
                                                <th>Observacion reflexiva</th>
                                                <th>Conceptualizacion abstracta</th>
                                                <th>Experimentacion activa</th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c1">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        discerniendo<br>
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c2">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        ensayando<br>
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c3">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        involucrándome
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c4">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        practicando
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c5">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        receptivamente
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c6">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        relacionando
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c7">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        analíticamente
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c8">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        imparcialmente
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c9">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        sintiendo
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c10">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        observando
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c11">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        pensando
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c12">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        haciendo
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c13">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        aceptando
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c14">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        arriesgando
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c15">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        evaluando
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c16">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        con cautela
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c17">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        intuitivamente
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c18">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        productivamente
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c19">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        lógicamente
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c20">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        cuestionando
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c21">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        abstracto
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c22">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        observando
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c23">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        concreto
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c24">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        activo
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c25">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        orientado al presente
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c26">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        reflexivamente
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c27">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        orientado hacia el futuro
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c28">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        pragmático
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c29">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        aprendo más de la experiencia
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c30">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        aprendo más de la observación
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c31">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        aprendo más de la conceptualización
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c32">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        aprendo más de la experimentación
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c33">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        emotivo
                                                </td>
                                                <td style="vertical-align: top; width: 25%;">
                                                        <select name="c34">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        reservado
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c35">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        racional
                                                </td>
                                                <td style="vertical-align: top;">
                                                        <select name="c36">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                        </select>
                                                        abierto
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                        <font color="#ff0000">
                                <font size="4"> ------------------</font>
                        </font><input name="calcularBtn" value="Calcular" onclick="calcular()" type="submit"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


                </form>
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

</html>