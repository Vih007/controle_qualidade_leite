<?php 
//agoritmo quicksort
function quicksortPorData($array) {
    // Caso base: se o array tem menos de 2 elementos, já está ordenado
    if (count($array) < 2) {
        return $array;
    }

    // Escolhe o primeiro elemento como pivô
    $pivot = $array[0];
    $pivotDate = strtotime($pivot["data"]);

    $left = [];  // itens com data mais recente que o pivô
    $right = []; // itens com data igual ou mais antiga que o pivô

    // Percorre os elementos restantes para separar em left e right
    for ($i = 1; $i < count($array); $i++) {
        $currentDate = strtotime($array[$i]["data"]);
        if ($currentDate > $pivotDate) { // ordem decrescente: mais recente primeiro
            $left[] = $array[$i];
        } else {
            $right[] = $array[$i];
        }
    }

    // Recursivamente ordena as sublistas e junta com o pivô no meio
    return array_merge(quicksortPorData($left), [$pivot], quicksortPorData($right));
}
?>
