<?php

function getPoints(array $lignes, string $mur = "#", string $begin = "X", string $end = "@"){
    $wallPoints = [];
    $spacePoints = [];
    $bidim = [];
    foreach ($lignes as $x => $ligne) {
        // Init points
        $bidim[] = str_split($ligne);
        for($y = 0; $y < strlen($ligne); $y++){
            switch ($ligne[$y]) {
                case $begin:
                    $startPoint = ['x' => $x, 'y' => $y];
                    $actually = $startPoint;
                    break;
                case $end:
                    $endPoint = ['x' => $x, 'y' => $y];
                    break;
                case $mur:
                    $wallPoints[] = ['x' => $x, 'y' => $y];
                    break;
                case ' ':
                    $spacePoints[] = ['x' => $x, 'y' => $y];
                    break;
            }
        }
    }
    return [
        'actually' => $actually,
        'startPosition' => $startPoint,
        'endPoint' => $endPoint,
        'wallPoints' => $wallPoints,
        'spacePoints' => $spacePoints,
        'bidim' => $bidim
    ];
}

function wallOrNot(array $direction, array $points, array $goTo){
    $index = array_search($direction, $points['spacePoints']);
    if(in_array($direction, $points['wallPoints']) || in_array($direction, $goTo)){
        return false;
    }else if($direction == $points['endPoint']){
        return 'end';
    }
    return $index;
}

function plusCourtChemin(string $path = './laby.txt', string $mur = "#", string $begin = "X", string $end = "@"){
    $lignes = file($path);
    $points = getPoints($lignes, $mur, $begin, $end);
    $bidim = $points['bidim'];
    $actually = $points['actually'];
    $goTo = [];
    $old = [];
    $tmpGoTo = [];
    while($actually !== $points['endPoint']){
        $combinaisons = [];
        $droite = ['x' => $actually['x'], 'y' => $actually['y']+1];
        $gauche = ['x' => $actually['x'], 'y' => $actually['y']-1];
        $bas = ['x' => $actually['x']+1, 'y' => $actually['y']];
        $haut = ['x' => $actually['x']-1, 'y' => $actually['y']];
        
        if($index = wallOrNot($droite, $points, $goTo)){
            $combinaisons[$index] = $droite;
        }
        if($index = wallOrNot($gauche, $points, $goTo)){
            $combinaisons[$index] = $gauche;
        }
        if($index = wallOrNot($haut, $points, $goTo)){
            $combinaisons[$index] = $haut;
        }
        if($index = wallOrNot($bas, $points, $goTo)){
            $combinaisons[$index] = $bas;
        }
        if(count($combinaisons) > 1){
            $old = $actually;
            $tmpGoTo = $goTo;
            $rand = array_rand($combinaisons);
            $actually = $combinaisons[$rand];
            $goTo[] = $actually;
        }else if(count($combinaisons) === 1){
            $actually = array_values($combinaisons)[0];
            $goTo[] = $actually;
        }else if(count($combinaisons) === 0){
            $actually = $old;
            $goTo = $tmpGoTo;
        }
    }
    
    $bidim = writePoint($goTo, $bidim);
    return convertToText($bidim);
}

function writePoint(array $goTo, array $bidim){
    foreach ($goTo as $value) {
        $bidim[$value['x']][$value['y']] = '.';
    }

    return $bidim;
}

function convertToText(array $array){
    $result = '';
    foreach ($array as $value) {
        foreach ($value as $v) {
            $result .= $v;
        }
        $result .= "\n";
    }

    return $result;
}

var_dump(plusCourtChemin());