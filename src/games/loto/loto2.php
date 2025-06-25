<?php
    function tirage() {
            $tabnum = [];
            while (count($tabnum) < 5) {
            $num = rand(1, 49);
            if (!in_array($num, $tabnum)) {
                    $tabnum[] = $num;
                }
            }
                sort($tabnum);

            
            $tabnumspe = [];
            while (count($tabnumspe) < 1) {
            $numspe = rand(1, 10);
            if (!in_array($numspe, $tabnumspe)) {
                    $tabnumspe[] = $numspe;
                }
            }
            sort($tabnumspe);
            

            return [
                'numeros' => $tabnum,
                'etoiles' => $tabnumspe
            ];
        }
        
    $tirage = tirage();
    // Affichage des numéros et des étoiles
    $tirage_base = $tirage['numeros'];  
    echo "Numéros: ";
    // Affichage des numéros
    // $tirage_base = $tirage['numeros'];
    // foreach($tirage_base as $value) {
    //     echo $value . " "  ; 
    // }
    foreach($tirage_base as $value) {
        echo $value . " "  ; 
    }
    echo "<br>";
    echo "Etoiles: ";
    // Affichage des étoiles
    // $tirage_spe = $tirage['etoiles'];
    // foreach($tirage_spe as $value) { 
    //     echo $value . " "; 
    // }
    $tirage_spe = $tirage['etoiles'];
    foreach($tirage_spe as $value) { 
        echo $value . " "; 
    }
    ?>