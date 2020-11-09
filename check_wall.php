<?php

function getIdTime($mysqli, $cntTram, $notifTram, $cntTrol, $notifTrol, $TRAMS, $TROLLEYS, $ONETRANSPORT, $COUNTHOURS, $HOURS) {
    $queryGetId = "";
    //сначала узнаем, сколько сейчас времени и какой день недели
        $hour = date("G") + 1; //прибавляем 1 час, т.к. на сервере московское время
        $week = date("N") - 1;
        //составляем запрос - получить id всех пользователей, которые подписаны на обновления
        //этого транпорта на этот час в этот день недели
        //id времени вычисляется по такой формуле: 
        //для трамваев: номер трамвая в массиве $TRAMS * $ONETRANSPORT + день недели * $COUNTHOURS + номер часа в массиве $HOURS
        //для троллейбусов: (номер троллейбуса в массиве $TROLLEYS + кол-во трамваев) * $ONETRANSPORT + день недели * $COUNTHOURS + номер часа в массиве $HOURS
        if ($cntTram != 0 || $cntTrol != 0) $queryGetId = "SELECT `idUser` FROM `subscriptions` WHERE ";
        foreach ($notifTram as $t) { //для трамваев
            $flagTram = 1;
            $queryGetId .= "`idTime` = '" . ($TRAMS[$t] * $ONETRANSPORT + $week * $COUNTHOURS + $HOURS[$hour]) . "'";
            $cntTram--;
            if ($cntTram != 0) $queryGetId .= " OR "; //если трамвай не последний, то вставляем логическое или
        }
        unset($t); //отвязка ссылки на последний элемент
        if ($flagTram == 1 && $cntTrol != 0) $queryGetId .= " OR ";
        foreach ($notifTrol as $t) { //для троллейбусов
            $queryGetId .= "`idTime` = '" . (($TROLLEYS[$t] + count($TRAMS)) * $ONETRANSPORT + $week * $COUNTHOURS + $HOURS[$hour]) . "'";
            $cntTrol--;
            if ($cntTrol != 0) $queryGetId .= " OR "; //если троллейбус не последний, то вставляем логическое или
        }
        return $queryGetId;
}

function getNotifUsers($mysqli, $queryGetId) {
    $tmpNotifId = $mysqli->query($queryGetId);
    $var = mysqli_fetch_assoc($tmpNotifId);
    $notifId = array();
    for ($j = 0; $var != false; $j++) {
        $notifId[$j] = $var["idUser"];
        $var = mysqli_fetch_assoc($tmpNotifId); 
    }
    $notifIdTrue = array_unique($notifId);
    return $notifIdTrue;
}

////////////////////////////СЛУЖЕБНОЕ ДЛЯ ВК/////////////////////////////////////////////////////////////////////
require_once('access.php');
require_once('simplevk-master/autoload.php'); // Подключение библиотеки для работы с vk api
use DigitalStar\vk_api\VK_api as vk_api; // Основной класс
use DigitalStar\vk_api\VkApiException; // Обработка ошибок

$vk = vk_api::create(VK_KEY, VERSION)->setConfirm(CONFIRM_STR);
$data = json_decode(file_get_contents('php://input')); 

if ($data->type == 'confirmation') { 
    exit(ACCESS_KEY); 
}
$vk->sendOK(); 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////СЛУЖЕБНОЕ ДЛЯ АНАЛИЗА ПОСТОВ//////////////////////////////////////////////////////////////////
require_once('analysis.php'); // Подключение файла, где лежит функция с алгоритмом анализа поста
$countPost = 5; //КОНСТАНТА, ОБОЗНАЧАЮЩАЯ СКОЛЬКО ПОСТОВ СО СТЕНЫ МЫ ХОТИМ ПОЛУЧИТЬ
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$request_params = array(                              //запрос к вк, в ответ на который получаем 5 записей со стены
    'owner_id' => -147755714,
    'count' => $countPost,
    'access_token' => OLEG_KEY,
    'v' => VERSION
);
$get_params = http_build_query($request_params);
$wall = json_decode(file_get_contents('https://api.vk.com/method/wall.get?'.$get_params));

//строю читабельный массив $postsFromDB с постами, полученными из базы данных
$posts = $mysqli->query("SELECT * FROM `posts`"); //все сохраненные записи (5 штук) из БД
$var = mysqli_fetch_assoc($posts);
$postsFromDB = array();
for ($i = 0; $var != false; $i++) {
    $postsFromDB[$i] = $var;            //каждый элемент этого массива содержит в себе поля id, data, text
    $var = mysqli_fetch_assoc($posts);  //обращаться к полю i-того поста можно так: $postsFromDB[$i]["НАЗВАНИЕПОЛЯ"]
}
    
//строю массив $newPosts с постами, которые нужно проанализировать
$newPosts = array();
for ($i = 0; $i < $countPost; $i++) {
    $newPosts[$i] = [ "date" => $wall->response->items[$i]->date,   /*каждый элемент массива содержит в себе дату поста*/
                      "text" => $wall->response->items[$i]->text   /*и текст поста*/
                    ];
    //обращаться к тексту i-того поста можно так: $newPosts[$i]["text"]
}
$date = array_column($newPosts, 'date');
array_multisort($date, SORT_DESC, $newPosts);

//определяем, сколько новых записей появилось
$tpost1FromDB = $mysqli->query("SELECT * FROM `posts` WHERE `id` = 1"); //для этого получаем данные последней записи из БД
$post1FromDB = mysqli_fetch_assoc($tpost1FromDB);
$cntNewPosts = 0; //количество новых постов
while ($cntNewPosts < $countPost && $newPosts[$cntNewPosts]["date"] > $post1FromDB["date"]) { 
    $cntNewPosts++;  ////идем по постам, полученным из вк, ища последний пост из БД, сравнивая даты постов
} 

echo $cntNewPosts;
echo "\n";

//["date" => .., "text" => .., "service" => false, "status" = true, "masTram" = [], "masTrol" = []]
$analysedPosts = postAnalysis($newPosts, $cntNewPosts);

//для случая, когда запись удалили
if ($newPosts[0]["date"] < $post1FromDB["date"]) {
    $cntNewPosts = -1;
    echo $cntNewPosts;
    $analysisDelete = postAnalysis($postsFromDB, 1);
    if ($analysisDelete[0]["status"] === false) {
        $notifTram = $analysisDelete[0]["masTram"][0];
        $notifTrol = $analysisDelete[0]["masTrol"][0];
        foreach ($notifTram as $transportGo1) {
            $mysqli->query("UPDATE `transport` SET `Status` = '1' WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '" . $transportGo1 . "'");
        }
        foreach ($notifTrol as $transportGo2) {
            $mysqli->query("UPDATE `transport` SET `Status` = '1' WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '" . $transportGo2 . "'");
        }
        $cntTram = count($notifTram);
        $cntTrol = count($notifTrol);
        $queryGetId = getIdTime($mysqli, $cntTram, $notifTram, $cntTrol, $notifTrol, $TRAMS, $TROLLEYS, $ONETRANSPORT, $COUNTHOURS, $HOURS);
        if ($queryGetId != "") {
            $notifIdTrue = getNotifUsers($mysqli, $queryGetId);
                
            foreach ($notifIdTrue as $idUs) {
                $vk->sendMessage($idUs, "Движение было восстановлено. \n \n" . $postsFromDB[0]["text"]);
            }
        }
    }
}

echo json_encode($analysedPosts, JSON_UNESCAPED_UNICODE);

//для каждого проанализированного поста (начинаем с конца, т.к. в конце более старые посты)
for ($i = $countPost - 1; $i >= 0; $i--) {
    if ($analysedPosts[$i]["service"] === false) {
        //два разных случая - когда есть новые посты, и когда их нет
        if ($i >= $cntNewPosts) { //новых постов нет
            //обновляем в БД значения, если движение восстановилось
            $notifTram = array();
            $cntTram = 0;
            $notifTrol = array();
            $cntTrol = 0;
            $masTram = $analysedPosts[$i]["masTram"][0];
            $masTrol = $analysedPosts[$i]["masTrol"][0];
            //первое условия дя проверки - вышли ли за границы массива
            if ($i - $cntNewPosts < $countPost && $postsFromDB[$i - $cntNewPosts]["Status"] == 0 && $analysedPosts[$i]["status"] == true) {
                foreach ($masTram as $tram) {
                    $mysqli->query("UPDATE `transport` SET `Status` = '1', 
                                                          `IsNotificated` = '0', 
                                                          `TextPost` = '" . $analysedPosts[$i]["text"] . "' 
                                                      WHERE `transport`.`TypeTransport` = 'Tram'
                                                      AND `NumberTransport` = '" . $tram . "'");
                    $notifTram[$cntTram] = $tram;
                    $cntTram++;
                }
                foreach ($masTrol as $trol) {
                    $mysqli->query("UPDATE `transport` SET `Status` = '1', 
                                                          `IsNotificated` = '0', 
                                                          `TextPost` = '" . $analysedPosts[$i]["text"] . "' 
                                                      WHERE `transport`.`TypeTransport` = 'Trolley'
                                                      AND `NumberTransport` = '" . $trol . "'");
                    $notifTrol[$cntTrol] = $trol;
                    $cntTrol++;
                }
            }
            else if ($i - $cntNewPosts < $countPost && $postsFromDB[$i - $cntNewPosts]["Status"] != 0 && $analysedPosts[$i]["status"] == false) {
                foreach ($masTram as $tram) {
                    $mysqli->query("UPDATE `transport` SET `Status` = '0', 
                                                          `IsNotificated` = '0', 
                                                          `TextPost` = '" . $analysedPosts[$i]["text"] . "' 
                                                      WHERE `transport`.`TypeTransport` = 'Tram'
                                                      AND `NumberTransport` = '" . $tram . "'");
                    $notifTram[$cntTram] = $tram;
                    $cntTram++;
                }
                foreach ($masTrol as $trol) {
                    $mysqli->query("UPDATE `transport` SET `Status` = '0', 
                                                          `IsNotificated` = '0', 
                                                          `TextPost` = '" . $analysedPosts[$i]["text"] . "' 
                                                      WHERE `transport`.`TypeTransport` = 'Trolley'
                                                      AND `NumberTransport` = '" . $trol . "'");
                    $notifTrol[$cntTrol] = $trol;
                    $cntTrol++;
                }
            }
        }

        else {
            $notifTram = array();
            $cntTram = 0;
            $notifTrol = array();
            $cntTrol = 0;
            $masTram = $analysedPosts[$i]["masTram"][0];
            $masTrol = $analysedPosts[$i]["masTrol"][0];
            
            if ($analysedPosts[$i]["status"] == false) {
                foreach ($masTram as $tram) {
                    $mysqli->query("UPDATE `transport` SET `Status` = '0', 
                                                          `IsNotificated` = '0', 
                                                          `TextPost` = '" . $analysedPosts[$i]["text"] . "' 
                                                      WHERE `transport`.`TypeTransport` = 'Tram'
                                                      AND `NumberTransport` = '" . $tram . "'");
                    $notifTram[$cntTram] = $tram;
                    $cntTram++;
                }
                foreach ($masTrol as $trol) {
                    $mysqli->query("UPDATE `transport` SET `Status` = '0', 
                                                          `IsNotificated` = '0', 
                                                          `TextPost` = '" . $analysedPosts[$i]["text"] . "' 
                                                      WHERE `transport`.`TypeTransport` = 'Trolley'
                                                      AND `NumberTransport` = '" . $trol . "'");
                    $notifTrol[$cntTrol] = $trol;
                    $cntTrol++;
                    
                }
            }
            else {
                foreach ($masTram as $tram) {
                    $mysqli->query("UPDATE `transport` SET `Status` = '1', 
                                                          `IsNotificated` = '0', 
                                                          `TextPost` = '" . $analysedPosts[$i]["text"] . "' 
                                                      WHERE `transport`.`TypeTransport` = 'Tram'
                                                      AND `NumberTransport` = '" . $tram . "'");
                }
                foreach ($masTrol as $trol) {
                    $mysqli->query("UPDATE `transport` SET `Status` = '1', 
                                                          `IsNotificated` = '0', 
                                                          `TextPost` = '" . $analysedPosts[$i]["text"] . "' 
                                                      WHERE `transport`.`TypeTransport` = 'Trolley'
                                                      AND `NumberTransport` = '" . $trol . "'");
                    
                }
            }
        }
        //оповещаем пользователей
        //получаем id времен транспорта
        $queryGetId = "";
        $queryGetId = getIdTime($mysqli, $cntTram, $notifTram, $cntTrol, $notifTrol, $TRAMS, $TROLLEYS, $ONETRANSPORT, $COUNTHOURS, $HOURS);
        echo $queryGetId;
        
        //получаем id пользователей, которых надо оповестить
        if ($queryGetId != "" && $cntNewPosts != -1) {
            $notifIdTrue = getNotifUsers($mysqli, $queryGetId);
            
            foreach ($notifIdTrue as $idUs) {
                $vk->sendMessage($idUs, $analysedPosts[$i]["text"]);
            }
        }
        $queryGetId = "";
        $tmpNotifId = [];
        $notifId = [];
    }
}

for($i = 0; $i < $countPost; $i++) {
    
    if ($analysedPosts[$i]["service"] == true) $stat = 10;
    else $stat = $analysedPosts[$i]["status"];
    $mysqli->query("UPDATE `posts` SET `date` = '" . $analysedPosts[$i]["date"] . "', `text` = '" . $analysedPosts[$i]["text"] . "', 
                                        `Status` = '" . $stat . "' 
                                    WHERE `posts`.`id` = '" . ($i + 1) . "'");
}


$mysqli->close();
?>