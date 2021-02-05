<?php
//funcion de calculo bayesiano
function bayesRecintoEstilo($arrayA, $arrayB){
    //total de registros
    $totalRegistros = count($arrayA);
    //variables para el total de registros de cada clase
    $totalAcomodador = 0;
    $totalAsimilador = 0;
    $totalConvergente = 0;
    $totalDivergente = 0;

    //cuento cuantos registros hay en cada estilo, para conocer la cantidad de instancias
    for($i = 0; $i < count($arrayA); $i++){
        if($arrayA[$i]['Estilo'] == 'ACOMODADOR'){
            $totalAcomodador++;
        }else if($arrayA[$i]['Estilo'] == 'ASIMILADOR'){
            $totalAsimilador++;
        }else if($arrayA[$i]['Estilo'] == 'CONVERGENTE'){
            $totalConvergente++;
        }else if($arrayA[$i]['Estilo'] == 'DIVERGENTE'){
            $totalDivergente++;
        }
    }

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

    for($i = 0; $i < count($arrayA); $i++){
        if($arrayA[$i]['Estilo'] == 'ACOMODADOR'){
            //si el registro es acomodador averiguo si contiene,  valores CA,EC,EA o OR similares a los de entrada
            if($arrayA[$i]['CA'] == $arrayB[0]){
                $acomodadorCA++;
            }else if($arrayA[$i]['EC'] == $arrayB[1]){
                $acomodadorEC++;
            }else if($arrayA[$i]['EA'] == $arrayB[2]){
                $acomodadorEA++;
            }else if($arrayA[$i]['OR'] == $arrayB[3]){
                $acomodadorOR++;
            }
        }else if($arrayA[$i]['Estilo'] == 'ASIMILADOR'){
            //si el registro es acomodador averiguo si contiene,  valores CA,EC,EA o OR similares a los de entrada
            if($arrayA[$i]['CA'] == $arrayB[0]){
                $asimiladorCA++;
            }else if($arrayA[$i]['EC'] == $arrayB[1]){
                $asimiladorEC++;
            }else if($arrayA[$i]['EA'] == $arrayB[2]){
                $asimiladorEA++;
            }else if($arrayA[$i]['OR'] == $arrayB[3]){
                $asimiladorOR++;
            }
        }else if($arrayA[$i]['Estilo'] == 'CONVERGENTE'){
            //si el registro es acomodador averiguo si contiene,  valores CA,EC,EA o OR similares a los de entrada
            if($arrayA[$i]['CA'] == $arrayB[0]){
                $convergenteCA++;
            }else if($arrayA[$i]['EC'] == $arrayB[1]){
                $convergenteEC++;
            }else if($arrayA[$i]['EA'] == $arrayB[2]){
                $convergenteEA++;
            }else if($arrayA[$i]['OR'] == $arrayB[3]){
                $convergenteOR++;
            }
        }else if($arrayA[$i]['Estilo'] == 'DIVERGENTE'){
            //si el registro es acomodador averiguo si contiene,  valores CA,EC,EA o OR similares a los de entrada
            if($arrayA[$i]['CA'] == $arrayB[0]){
                $convergenteCA++;
            }else if($arrayA[$i]['EC'] == $arrayB[1]){
                $convergenteEC++;
            }else if($arrayA[$i]['EA'] == $arrayB[2]){
                $convergenteEA++;
            }else if($arrayA[$i]['OR'] == $arrayB[3]){
                $convergenteOR++;
            }
        }
    }

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


    //definimos un M
    $m = 4;
    //establecemos el valor de P para cada caso, segun el calculo que realizamos de valores distintos en cada atributo *
    $pCA = 1/count($uniqueCA);
    $pEC = 1/count($uniqueEC);
    $pEA = 1/count($uniqueEA);
    $pOR = 1/count($uniqueOR);

    //calculamos las frecuencias
    //CA, EC, EA, OR ACOMODADOR
    $frecuenciaAcomodadorAC = (($acomodadorCA+($m*($pCA)))/$totalAcomodador+$m);
    $frecuenciaAcomodadorEC = (($acomodadorEC+($m*($pEC)))/$totalAcomodador+$m);
    $frecuenciaAcomodadorEA = (($acomodadorEA+($m*($pEA)))/$totalAcomodador+$m);
    $frecuenciaAcomodadorOR = (($acomodadorOR+($m*($pOR)))/$totalAcomodador+$m);

    //CA, EC, EA, OR ASIMILADOR
    $frecuenciaAsimiladorAC = (($asimiladorCA+($m*($pCA)))/$totalAsimilador+$m);
    $frecuenciaAsimiladorEC = (($asimiladorEC+($m*($pEC)))/$totalAsimilador+$m);
    $frecuenciaAsimiladorEA = (($asimiladorEA+($m*($pEA)))/$totalAsimilador+$m);
    $frecuenciaAsimiladorOR = (($asimiladorOR+($m*($pOR)))/$totalAsimilador+$m);

    //CA, EC, EA, OR CONVERGENTE
    $frecuenciaConvergenteAC = (($convergenteCA+($m*($pCA)))/$totalConvergente+$m);
    $frecuenciaConvergenteEC = (($convergenteEC+($m*($pEC)))/$totalConvergente+$m);
    $frecuenciaConvergenteEA = (($convergenteEA+($m*($pEA)))/$totalConvergente+$m);
    $frecuenciaConvergenteOR = (($convergenteOR+($m*($pOR)))/$totalConvergente+$m);

    //CA, EC, EA, OR DIVERGENTE
    $frecuenciaDivergenteAC = (($divergenteCA+($m*($pCA)))/$totalDivergente+$m);
    $frecuenciaDivergenteEC = (($divergenteEC+($m*($pEC)))/$totalDivergente+$m);
    $frecuenciaDivergenteEA = (($divergenteEA+($m*($pEA)))/$totalDivergente+$m);
    $frecuenciaDivergenteOR = (($divergenteOR+($m*($pOR)))/$totalDivergente+$m);


    //calculamos productos de frecuencias
    //acomodador
    $acomodadorProducto = $frecuenciaAcomodadorAC * $frecuenciaAcomodadorEC * $frecuenciaAcomodadorEA * $frecuenciaAcomodadorOR;
    //asimilador
    $asimiladorProducto = $frecuenciaAsimiladorAC * $frecuenciaAsimiladorEC * $frecuenciaAsimiladorEA * $frecuenciaAsimiladorOR;
    //convergente
    $convergenteProducto = $frecuenciaConvergenteAC * $frecuenciaConvergenteEC * $frecuenciaConvergenteEA * $frecuenciaConvergenteOR;
    //divergente
    $divergenteProducto = $frecuenciaDivergenteAC * $frecuenciaDivergenteEC * $frecuenciaDivergenteEA * $frecuenciaDivergenteOR;

    //Calculo la probabilidad total a partir del producto de frecuencias y cantidad de clases
    $probabilidadAcomodador = $acomodadorProducto * 4;
    $probabilidadAsimilador = $asimiladorProducto * 4;
    $probabilidadConvergente = $convergenteProducto * 4;
    $probabilidadDivergente = $divergenteProducto * 4;


    //Teniendo las probabilidades calculadas, obtenemos la mas alta
    if($probabilidadAcomodador > $probabilidadAsimilador && $probabilidadAcomodador > $probabilidadConvergente && $probabilidadAcomodador > $probabilidadDivergente){
        return 'Acomodador';
    }else if($probabilidadAsimilador > $probabilidadAcomodador && $probabilidadAsimilador > $probabilidadConvergente && $probabilidadAsimilador > $probabilidadDivergente){
        return 'Asimilador';
    }else if($probabilidadConvergente > $probabilidadAcomodador && $probabilidadConvergente > $probabilidadAsimilador && $probabilidadConvergente > $probabilidadDivergente){
        return 'Convergente';
    }else{
        return 'Divergente';
    }
}
