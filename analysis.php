<?php

function getTrolleys($trollText) {
    $trolleys = [];
    $keyNum = 0;
    $c = strpos($trollText,"№1,") || strpos($trollText," 1 ") || strpos($trollText," 1,");
    if ($c !== false) {
        $trolleys[$keyNum] = 1;
        $keyNum++;
    }
    $c = strpos($trollText,"2а");
    if ($c !== false) {
        $trolleys[$keyNum] = 20;
        $keyNum++;
    }
    $c = strpos($trollText,"2");
    if ($c !== false) {
        $trolleys[$keyNum] = 2;
         $keyNum++;
    }
    $c = strpos($trollText,"3");
    if ($c !== false) {
        $trolleys[$keyNum] = 3;
         $keyNum++;
    }
    $c = strpos($trollText,"4");
    if ($c !== false) {
        $trolleys[$keyNum] = 4;
         $keyNum++;
    }
    $c = strpos($trollText,"5");
    if ($c !== false) {
        $trolleys[$keyNum] = 5;
         $keyNum++;
    }
    $c = strpos($trollText,"7");
    if ($c !== false) {
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
    $c = strpos($tramText,"2");
    $keyNum = 0;
    if ($c !== false) {
        $trams[$keyNum] = 2;
        $keyNum++;
    }
    $c = strpos($tramText,"3");
    if ($c !== false) {
        $trams[$keyNum] = 3;
        $keyNum++;
    }
    $c = strpos($tramText,"4");
    if ($c !== false) {
        $trams[$keyNum] = 4;
        $keyNum++;
    }
    $c = strpos($tramText,"5");
    if ($c !== false) {
        $trams[$keyNum] = 5;
        $keyNum++;
    }
    $c = strpos($tramText,"6");
    if ($c !== false) {
        $trams[$keyNum] = 6;
        $keyNum++;
    }
    $c = strpos($tramText,"7");
    if ($c !== false) {
        $trams[$keyNum] = 7;
        $keyNum++;
    }
    $c = strpos($tramText,"8");
    if ($c !== false) {
        $trams[$keyNum] = 8;
        $keyNum++;
    }
    $c = strpos($tramText,"9");
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
    $checkIfTrams = strpos($txt,"трамваев");
    $checkIfTrolleys = strpos($txt,"троллейбусов");
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

function postAnalysis($newPosts, $postsFromDB, $cntNewPosts) {
    $analysis = array();
    if ($cntNewPosts == 0) {
        for ($i = 0; $i < 5; $i++) {
            $checkPostDB = $postsFromDB[$i]["text"]; //сохраняем сюда текст поста из БД для дальнейших проверок
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
              $posCheck = strpos($checkPostNew,$checkChange);
              if ($posCheck === false) {
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
    }
    else {
        for ($i = /*$cntNewPosts*/0; $i < 5; $i++) {
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
              $posCheck = strpos($checkPostNew,$checkChange);
              if ($posCheck === false) {
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
        
    }
    
    
    return $analysis;
}


?>
