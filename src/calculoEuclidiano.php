<?php
//funcion de calculo de distancia euclidiana, recibe 2 arreglos
function dist($arrayA, $arrayB){
    //la variable sum se encarga de acumular la sumatoria de la diferencia
    //entre los nodos comparados
    $sum = 0;
    //este for recorre la cantidad de nodos que hayan en los arreglos brindados, osea
    //sus atributos o columnas a restar
    for($i = 0; $i < count($arrayA); $i++){
        //con pow elevamos al cuadrado
        $sum += pow($arrayA[$i]-$arrayB[$i],2);
    }
    //utilizando la funcion sqrt extraigo la raiz cuadrada de la sumatoria
    $distancia = sqrt($sum);

    //retorno el valor de la distancia
    return $distancia;
}
?>