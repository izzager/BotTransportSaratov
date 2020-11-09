<?php

function getTrolleys($trollText) {
    $trolleys = [];
    $keyNum = 0;
    if (strpos($trollText,"№1,") !== false || strpos($trollText," 1 ") !== false || strpos($trollText," 1,") !== false) {
        $trolleys[$keyNum] = 1;
        $keyNum++;
    }
    if (strpos($trollText,"№2а,") !== false || strpos($trollText," 2а") !== false || strpos($trollText," 2а,") !== false) {
        $trolleys[$keyNum] = 20;
        $keyNum++;
    }
    if (strpos($trollText,"№2,") !== false || strpos($trollText," 2") !== false || strpos($trollText," 2,") !== false) {
        $trolleys[$keyNum] = 2;
         $keyNum++;
    }
    if (strpos($trollText,"№3,") !== false || strpos($trollText," 3") !== false || strpos($trollText," 3,") !== false) {
        $trolleys[$keyNum] = 3;
         $keyNum++;
    }
    if (strpos($trollText,"№4,") !== false || strpos($trollText," 4") !== false || strpos($trollText," 4,") !== false) {
        $trolleys[$keyNum] = 4;
         $keyNum++;
    }
    if (strpos($trollText,"№5,") !== false || strpos($trollText," 5") !== false || strpos($trollText," 5,") !== false) {
        $trolleys[$keyNum] = 5;
         $keyNum++;
    }
    if (strpos($trollText,"№7,") !== false || strpos($trollText," 7") !== false || strpos($trollText," 7,") !== false) {
        $trolleys[$keyNum] = 7;
         $keyNum++;
    }
    $c = strpos($trollText,"10");
    if ($c !== false) {
        $trolleys[$keyNum] = 10;
         $keyNum++;
    }
    $c = strpos($trollText,"11");
    if ($c !== false) {
        $trolleys[$keyNum] = 11;
         $keyNum++;
    }
    $c = strpos($trollText,"15");
    if ($c !== false) {
        $trolleys[$keyNum] = 15;
         $keyNum++;
    }
    $c = strpos($trollText,"16");
    if ($c !== false) {
        $trolleys[$keyNum] = 16;
         $keyNum++;
    }
    return $trolleys;
}

function getTrams($tramText) {
    $trams = [];
    $c = strpos($tramText,"№2,") || strpos($tramText," 2") || strpos($tramText," 2,");
    $keyNum = 0;
    if ($c !== false) {
        $trams[$keyNum] = 2;
        $keyNum++;
    }
    $c = strpos($tramText,"№3,") || strpos($tramText," 3") || strpos($tramText," 3,");
    if ($c !== false) {
        $trams[$keyNum] = 3;
        $keyNum++;
    }
    $c = strpos($tramText,"№4,") || strpos($tramText," 4") || strpos($tramText," 4,");
    if ($c !== false) {
        $trams[$keyNum] = 4;
        $keyNum++;
    }
    $c = strpos($tramText,"№5,") || strpos($tramText," 5") || strpos($tramText," 5,");
    if ($c !== false) {
        $trams[$keyNum] = 5;
        $keyNum++;
    }
   $c = strpos($tramText,"№6,") || strpos($tramText," 6") || strpos($tramText," 6,");
    if ($c !== false) {
        $trams[$keyNum] = 6;
        $keyNum++;
    }
    $c = strpos($tramText,"№7,") || strpos($tramText," 7") || strpos($tramText," 7,");
    if ($c !== false) {
        $trams[$keyNum] = 7;
        $keyNum++;
    }
    $c = strpos($tramText,"№8,") || strpos($tramText," 8") || strpos($tramText," 8,");
    if ($c !== false) {
        $trams[$keyNum] = 8;
        $keyNum++;
    }
    $c = strpos($tramText,"№9,") || strpos($tramText," 9") || strpos($tramText," 9,");
    if ($c !== false) {
        $trams[$keyNum] = 9;
        $keyNum++;
    }
    $c = strpos($tramText,"10");
    if ($c !== false) {
        $trams[$keyNum] = 10;
        $keyNum++;
    }
    $c = strpos($tramText,"11");
    if ($c !== false) {
        $trams[$keyNum] = 11;
        $keyNum++;
    }
    return $trams;
}

function getTransportFromText($txt) {
    $result = ["trams"=>NULL, "trolleys"=>NULL];
    $checkIfTrams = strpos($txt,"трамв");
    $checkIfTrolleys = strpos($txt,"троллейбус");
    $mainPos = strpos($txt,"ост");
    if ($mainPos === false) {
        $mainPos = strpos($txt,"ул");
    }
    if ($checkIfTrams === false) {
        $result["trams"] = [];
        $result["trolleys"] = getTrolleys(mb_strcut($txt,$checkIfTrolleys,$mainPos - $checkIfTrolleys));
    }
    elseif ($checkIfTrolleys===false) {
        $result["trolleys"] = [];
        $result["trams"] = getTrams(mb_strcut($txt,$checkIfTrams,$mainPos - $checkIfTrams));
    }
    else {
        $txt1 = "";
        $txt2 ="";
        if ($checkIfTrams < $checkIfTrolleys) {
           $result["trams"] = getTrams(mb_strcut($txt,$checkIfTrams,$checkIfTrolleys - $checkIfTrams));
           $result["trolleys"] = getTrolleys(mb_strcut($txt,$checkIfTrolleys,$mainPos - $checkIfTrolleys));
        }
        else {
            $result["trams"] = getTrams(mb_strcut($txt,$checkIfTrams,$mainPos - $checkIfTrams));
           $result["trolleys"] = getTrolleys(mb_strcut($txt,$checkIfTrolleys,$checkIfTrams - $checkIfTrolleys));
        }
    }
    return $result;
}

function postAnalysis($newPosts, $cntNewPosts) {
    $analysis = array();
    for ($i = 0; $i < 5; $i++) {
        $checkPostNew = $newPosts[$i]["text"]; //сохраняем сюда текст поста со стенки для дальнейших проверок
        $checkIfNot = "Прервано движение"; //проверочная подстрока для выявления того, что это нужный вид поста
        $posNot = strpos($checkPostNew,$checkIfNot);
        if ($posNot === false) {
            $analysis[$i]["date"] = $newPosts[$i]["date"];
            $analysis[$i]["text"] = $newPosts[$i]["text"];
            $analysis[$i]["service"] = true;
            $analysis[$i]["status"] = false;
            $analysis[$i]["masTram"] = [];
            $analysis[$i]["masTrol"] = [];
        }
        else {
            $checkChange = "Движение восстановлено";
            $transportFromPost = getTransportFromText($checkPostNew);
            $posCheck1 = strpos($checkPostNew,$checkChange);
            $checkChange = "Восстановлено движение";
            $posCheck2 = strpos($checkPostNew,$checkChange);
            if ($posCheck1 === false && $posCheck2 === false) {
                $analysis[$i]["date"] = $newPosts[$i]["date"];
                $analysis[$i]["text"] = $newPosts[$i]["text"];
                $analysis[$i]["service"] = false;
                $analysis[$i]["status"] = false;
                $analysis[$i]["masTram"] = [$transportFromPost["trams"]];
                $analysis[$i]["masTrol"] = [$transportFromPost["trolleys"]];
            }
            else {
                $analysis[$i]["date"] = $newPosts[$i]["date"];
                $analysis[$i]["text"] = $newPosts[$i]["text"];
                $analysis[$i]["service"] = false;
                $analysis[$i]["status"] = true;
                $analysis[$i]["masTram"] = [$transportFromPost["trams"]];
                $analysis[$i]["masTrol"] = [$transportFromPost["trolleys"]];
            }
        }
    }
    
    return $analysis;
}


?>