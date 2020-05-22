<?php

/////////////////////////////СЛУЖЕБНОЕ ДЛЯ БАЗЫ ДАННЫХ/////////////////////////////////////////////////////////
/*$TRAMS = [2 => 0,3 => 1,4 => 2,5 => 3,6 => 4,7 => 5,8 => 6,9 => 7,10 => 8,11 => 9];
$TROLLEYS = [1 => 0,20 => 1,2 => 2,3 => 3,4 => 4,5 => 5,7 => 6,10 => 7,11 => 8,15 => 9,16 => 10];
$HOURS = [5 => 1,6 => 2,7 => 3,8 => 4,9 => 5,10 => 6,11 => 7,12 => 8,13 => 9,14 => 10,
          15 => 11,16 => 12,17 => 13,18 => 14,19 => 15,20 => 16,21 => 17,22 => 18,23 => 19];
$COUNTHOURS = count($HOURS);
$ONETRANSPORT = $COUNTHOURS * 7; //кол-во рабочих часов на 7 дней*/
//////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////СЛУЖЕБНОЕ ДЛЯ ВК/////////////////////////////////////////////////////////////////////
require_once('access.php');
require_once('analysis.php');
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
    'owner_id' => -/*****/,
    'count' => $countPost,
    'access_token' => '*******************',
    'v' => VERSION
);
$get_params = http_build_query($request_params);
$wall = json_decode(file_get_contents('https://api.vk.com/method/wall.get?'.$get_params));
$posts5 = $wall->response->items;


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
$post1FromDB = mysqli_fetch_assoc ($tpost1FromDB);
$cntNewPosts = 0; //количество новых постов
while ($cntNewPosts < $countPost && $newPosts[$cntNewPosts]["date"] > $post1FromDB["date"]) { 
    $cntNewPosts++;  ////идем по постам, полученным из вк, ища последний пост из БД, сравнивая даты постов
} 

echo $cntNewPosts;
echo "\n";

//["date" => .., "text" => .., "service" => false, "status" = true, "masTram" = [], "masTrol" = []]
$analysedPosts = postAnalysis($newPosts, $postsFromDB, $cntNewPosts);
echo json_encode($analysedPosts);

//для случая, когда запись удалили
if ($newPosts[0]["date"] < $post1FromDB["date"]) {
	$cntNewPosts = -1;
	//значт транспорт ходит
	$mysqli->query("UPDATE `transport` SET `Status` = '1' WHERE `transport`.`TextPost` = '" . $post1FromDB["date"] . "'");
}

//для каждого проанализированного поста (начинаем с конца, т.к. в конце более старые посты)
for ($i = $countPost - 1; $i >= 0; $i--) {
    if ($analysedPosts[$i]["service"] == true) {
    }
    else {
        //два разных случая - когда есть новые посты, и когда их нет
        if ($i >= $cntNewPosts) { //новых постов нет
            //обновляем в БД значения, если движение восстановилось
            $notifTram = array();
            $cntTram = 0;
            $notifTrol = array();
            $cntTrol = 0;
            $masTram = $analysedPosts[$i]["masTram"][0];
            $masTrol = $analysedPosts[$i]["masTrol"][0];
            echo "\n";
            echo json_encode($postsFromDB[$i - $cntNewPosts]);
            echo "\n";
            echo json_encode($analysedPosts[$i]);
            //первое условия дя проверки - вышли ли за границы массива
            if ($i - $cntNewPosts < $countPost && $postsFromDB[$i - $cntNewPosts]["Status"] == 0 && $analysedPosts[$i]["status"] == true) {
                //$mysqli->query("UPDATE `posts` SET `Status` = '1' WHERE `posts`.`id` = '" . ($i + 1) . "'");
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
                //$mysqli->query("UPDATE `posts` SET `Status` = '0' WHERE `posts`.`id` = '" . ($i + 1) . "'");
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

        else { //есть новые посты
            $notifTram = array();
            $cntTram = 0;
            $notifTrol = array();
            $cntTrol = 0;
            $masTram = $analysedPosts[$i]["masTram"][0];
            $masTrol = $analysedPosts[$i]["masTrol"][0];
            
            if ($analysedPosts[$i]["status"] == false) { //движение прервано, нужно оповестить
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
            else { //движение не прервано, оповещать не надо
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
                
        //получаем id пользователей, которых надо оповестить
        if ($queryGetId != "") {
            $tmpNotifId = $mysqli->query($queryGetId);
            $var = mysqli_fetch_assoc($tmpNotifId);
            $notifId = array();
            for ($j = 0; $var != false; $j++) {
				$notifId[$j] = $var["idUser"];
				$var = mysqli_fetch_assoc($tmpNotifId); 
            }
                    
            $notifIdTrue = array_unique($notifId);
            //сообщение с оповещением - будет отправляться текст поста $analysedPosts[$i]["text"]
            foreach ($notifIdTrue as $idUs) {
                $vk->sendMessage($idUs, $analysedPosts[$i]["text"]);
            }
        }
        $queryGetId = "";
        $tmpNotifId = [];
        $notifId = [];
    }
}

//обновляем посты в базе данных
for($i = 0; $i < $countPost; $i++) {
    if ($analysedPosts[$i]["service"] == true) $stat = 10; //отдельный статус для служебных постов
    else $stat = $analysedPosts[$i]["status"];
    $mysqli->query("UPDATE `posts` SET `date` = '" . $analysedPosts[$i]["date"] . "', `text` = '" . $analysedPosts[$i]["text"] . "', 
                                        `Status` = '" . $stat . "' 
                                    WHERE `posts`.`id` = '" . ($i + 1) . "'");
}


$mysqli->close();
?>