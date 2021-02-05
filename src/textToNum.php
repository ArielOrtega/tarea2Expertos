<?php

    //*******TODOS los metodos reciben un valor alfabetico, string o caracter y le asigna un valor numerico
    //*******esto con tal de poder realizar el calculo euclidiano
    function sexoToNum($sexo){
        if($sexo == 'F'){
            return 1;
        }else if($sexo == 'M'){
            return 2;
        }
    }

    function recintoToNum($recinto){
        if($recinto == 'Paraiso'){
            return 1;
        }else{
            return 2;
        }
    }

    function estiloToNum($estilo){
        if($estilo == 'DIVERGENTE'){
            return 1;
        }else if ($estilo == 'CONVERGENTE'){
            return 2;
        }else if ($estilo == 'ASIMILADOR'){
            return 3;
        }else if ($estilo == 'ACOMODADOR'){
            return 4;
        }
    }

    function genderToNum($gender){
        if($gender == 'F'){
            return 1;
        }else if($gender == 'M'){
            return 2;
        }else if($gender == 'NA'){
            return 3;
        }
    }

    function skillToNum($skill){
        if($skill == 'B'){
            return 1;
        }else if($skill == 'I'){
            return 2;
        }else if($skill == 'A'){
            return 3;
        }
    }

    function disciplineToNum($discipline){
        if($discipline == 'DM'){
            return 1;
        }else if($discipline == 'ND'){
            return 2;
        }else if($discipline == 'O'){
            return 3;
        }
    }

    function skillComputerToNum($skillComputer){
        if($skillComputer == 'L'){
            return 1;
        }else if($skillComputer == 'A'){
            return 2;
        }else if($skillComputer == 'H'){
            return 3;
        }
    }

    function expToNum($exp){
        if($exp == 'N'){
            return 1;
        }else if($exp == 'S'){
            return 2;
        }else if($exp == 'O'){
            return 3;
        }
    }

    function netToNum($exp){
        if($exp == 'High'){
            return 1;
        }else if($exp == 'Medium'){
            return 2;
        }else if($exp == 'Low'){
            return 3;
        }
    }
?>