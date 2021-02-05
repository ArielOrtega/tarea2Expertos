<!DOCTYPE html>
<html>

<head>

        <meta content="text/html; charset=UTF-8" http-equiv="content-type">
        <title>Tipo de profesor</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>



        <meta http-equiv="CONTENT-TYPE" content="text/html; charset=utf-8">


        <meta name="generator" content="Bluefish 2.2.2">


        <style type="text/css">
                <!--
                @page {
                        margin: 2cm
                }

                P {
                        margin-bottom: 0cm;
                        text-align: justify
                }

                P.western {
                        so-language: es-ES
                }
                -->
        </style>
</head>

<body>
        <?php
        //incluyo el header, que contiene la barra de menu, para no repetir el mismo codigo
        include("../header.php");
        ?>
        <h2 class="container">Formulario 5: Determinar tipo de profesor</h2>
        <br>
        <div class="container" style="border: 1px solid black;padding: 30px;">
                <form method="POST" action="formProfesor.php">
                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <label>Indique su Edad en años:</label>
                                </div>
                                <div>
                                        <select name="A">
                                                <option value="1">Menos de 30</option>
                                                <option value="2">Entre 30 y 55</option>
                                                <option value="3">Más de 55</option>
                                        </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <label>Indique su género:</label>
                                </div>
                                <div>
                                        <select name="B">
                                                <option value="1">Hombre</option>
                                                <option value="2">Mujer</option>
                                                <option value="3">No disponible</option>
                                        </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <label>Indique su Nivel de experiencia:</label>
                                </div>
                                <div>
                                        <select name="C">
                                                <option value="1">Principiante</option>
                                                <option value="2">Intermedio</option>
                                                <option value="3">Avanzado</option>
                                        </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <label>Cantidad de veces que dio cursos de este tipo:</label>
                                </div>
                                <div>
                                        <select name="D">
                                                <option value="1">Nunca</option>
                                                <option value="2">1 a 5 veces</option>
                                                <option value="3">Más de 5 veces</option>
                                        </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <label>Indique su disciplina:</label>
                                </div>
                                <div>
                                        <select name="E">
                                                <option value="1">Toma de decisiones</option>
                                                <option value="2">Diseño de redes</option>
                                                <option value="3">Otros</option>
                                        </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <label>Nivel de habilidad con computadoras:</label>
                                </div>
                                <div>
                                        <select name="F">
                                                <option value="1">Bajo</option>
                                                <option value="2">Intermedio</option>
                                                <option value="3">Alto</option>
                                        </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <label>Experiencia usando tecnologías web para enseñar:</label>
                                </div>
                                <div>
                                        <select name="G">
                                                <option value="1">Nunca</option>
                                                <option value="2">Algunas veces</option>
                                                <option value="3">A menudo</option>
                                        </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <label>Experiencia usando un sitio web:</label>
                                </div>
                                <div>
                                        <select name="H">
                                                <option value="1">Nunca</option>
                                                <option value="2">Algunas veces</option>
                                                <option value="3">A menudo</option>
                                        </select>
                                </div>
                        </div>

                        <div class="form-group row">
                                <div class="col-sm-3">
                                        <input name="promedioBtn" type="submit" value="Determinar">
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
                $stmt = $connection->prepare("Select * FROM Profesores");
                //definimos el modo de fecth que es la forma en como nos retornara los datos
                //FETCH_ASSOC nos devolvera los datos en un array indexado cuyos keys son el nombre de las columnas.
                $stmt->execute();
                $resultArrayBD = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                //obtenemos las caracteristicas brindadas por el usuario
                $A = $_POST['A'];
                $B = $_POST['B'];
                $C = $_POST['C'];
                $D = $_POST['D'];
                $E = $_POST['E'];
                $F = $_POST['F'];
                $G = $_POST['G'];
                $H = $_POST['H'];

                //creamos un arreglo formado de estos 3 valores
                $sampleArray = array(
                        $A,$B,$C,$D,$E,$F,$G,$H
                );

                //asignamos variable para guardar el mejor valor y compararlo en cada iteracion
                $bestSample = null;
                //en esta variable guardaremos la clase de profesor del registro que tenga una menor distancia euclidiana con los datos del usuario actual
                $sampleClass = null;
                //con esta variable nos aseguramos de que solo el primer valor que retorne la funcion dist() sea asignada automaticamente
                //para que bestSample no tenga un valor de nulo
                $primerContador = 0;
                //Creamos un ciclo para comparar los datos del usuario actual con los guardados en la base de datos
                foreach ($resultArrayBD as $item) {
                        $baseArray = array(
                                $item['A'],
                                genderToNum($item['B']),
                                skillToNum($item['C']),
                                $item['D'],
                                disciplineToNum($item['E']),
                                skillComputerToNum($item['F']),
                                expToNum($item['G']),
                                expToNum($item['H']),
                                $item['Class']

                        );

                        $distanciaEuclidiana = dist($sampleArray, $baseArray);
                        //nos aseguramos de tener la mejor muestra, en menor es mejor
                        if ($distanciaEuclidiana < $bestSample || $primerContador == 0) {
                                $bestSample = $distanciaEuclidiana;
                                $sampleClass = $baseArray[8]; //obtiene el atributo estilo en el indice 4
                                $primerContador++;
                        }
                }
                //uso estos echo para poder imprimir el html con los resultados
                echo "<div class='container' style='border: 1px solid black;padding: 28px;'><font color='#2574a9'><font size='6'>Tipo de profesor: $sampleClass</font></font></div><br>";

                //cierro la conexion con la base de datos para no saturarla
                $connection = null;
        }
        ?>
</body>

</html>