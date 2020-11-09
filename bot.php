<?php

require_once('access.php'); //доступ к БД и ВК, некоторые константы
require_once('simplevk-master/autoload.php'); // Подключение библиотеки


use DigitalStar\vk_api\VK_api as vk_api; // Основной класс
use DigitalStar\vk_api\VkApiException; // Обработка ошибок
$vk = vk_api::create(VK_KEY, VERSION)->setConfirm(CONFIRM_STR);
$data = json_decode(file_get_contents('php://input')); //Получает и декодирует JSON пришедший из ВК
$vk->sendOK(); //Говорим vk, что мы приняли callback

$peer_id = $data->object->message->peer_id;
$messag = $data->object->message->text; // Само сообщение от пользователя

//получаем текст с подписками пользователя
function getUserSubs($peer_id, $mysqli, $day, $daysOfWeek, $timesOfDay) {
    //получаем id времени подписок пользователя
	$response = $mysqli->query("SELECT `idTime` FROM `subscriptions` WHERE `idUser` = '" . $peer_id . "' ORDER BY `idTime`");
    $var = mysqli_fetch_assoc($response);
    $subs = array();
    for ($i = 0; $var != false; $i++) {
        $subs[$i] = $var["idTime"];
        $var = mysqli_fetch_assoc($response);
    }
    
    $i = 0;
    $textUserSubs = "";
    foreach ($subs as $sub) {
        //получаем развернутые данные о каждом id подписки
        $responses[$i] = $mysqli->query("SELECT * FROM `times` WHERE `id` = '" . $sub . "'");
        $var = mysqli_fetch_assoc($responses[$i]);
        //т.к. просматриваем подписки только на выбранный день недели
        if ($day == $var["day"]) {
            //номер подписки
            $textUserSubs .= ($i + 1) . ") ";
            //тип транспорта
            if ($var["type"] == "tram") $textUserSubs .= "Трамвай №";
            else $textUserSubs .= "Троллейбус №";
            //номер транспорта, день недели, время
            if ($var["number"] == 20) {
                $var["number"] = "2а";
            }
            $textUserSubs .= $var["number"] . " " . $daysOfWeek[$var["day"]] . " " . $timesOfDay[$var["time"]] . "\n";
            $i++;
        }
    }
    //если нет подписок на этот день
    if ($textUserSubs == "") $textUserSubs = "На " . $daysOfWeek[$day] . " подписок нет";
    //возвращаем строку с подписками
    return $textUserSubs;
}

//получаем текст с подписками пользователя
function getAllUserSubs($peer_id, $mysqli, $daysOfWeek, $timesOfDay) {
    //получаем id времени подписок пользователя
	$response = $mysqli->query("SELECT `idTime` FROM `subscriptions` WHERE `idUser` = '" . $peer_id . "' ORDER BY `idTime`");
    $var = mysqli_fetch_assoc($response);
    $subs = array();
    for ($i = 0; $var != false; $i++) {
        $subs[$i] = $var["idTime"];
        $var = mysqli_fetch_assoc($response);
    }
    
    $i = 0;
    $textUserSubs = "";
    foreach ($subs as $sub) {
        //получаем развернутые данные о каждом id подписки
        $responses[$i] = $mysqli->query("SELECT * FROM `times` WHERE `id` = '" . $sub . "'");
        $var = mysqli_fetch_assoc($responses[$i]);
        //т.к. просматриваем подписки только на выбранный день недели
        //if ($day == $var["day"]) {
            //номер подписки
            $textUserSubs .= ($i + 1) . ") ";
            //тип транспорта
            if ($var["type"] == "tram") $textUserSubs .= "Трамвай №";
            else $textUserSubs .= "Троллейбус №";
            //номер транспорта, день недели, время
            if ($var["number"] == 20) {
                $var["number"] = "2а";
            }
            $textUserSubs .= $var["number"] . " " . $daysOfWeek[$var["day"]] . " " . $timesOfDay[$var["time"]] . "\n";
            $i++;
        //}
    }
    //если нет подписок на этот день
    if ($textUserSubs == "") $textUserSubs = "У Вас нет подписок";
    //возвращаем строку с подписками
    return $textUserSubs;
}

$btn_start = $vk->buttonText('Начать', 'green', ['command' => 'start']);
$btn_sub = $vk->buttonText('Запись', 'green', ['command' => 'btn_sub']);
$btn_status = $vk->buttonText('Состояние', 'blue', ['command' => 'btn_status']);
$btn_unsub = $vk->buttonText('Отписаться', 'red', ['command' => 'btn_unsub']);
$btn_my_sub = $vk->buttonText('Мои записи','white',['command' => 'btn_my_sub']);
$btn_trot = $vk->buttonText('Трамвай','white',['command' => 'btn_trot']);
$btn_trob = $vk->buttonText('Троллейбус','white',['command' => 'btn_trob']);
$btn_yes = $vk->buttonText('Отписаться', 'red', ['command' => 'btn_yes']);
$btn_no = $vk->buttonText('Вернуться назад', 'green', ['command' => 'btn_no']);
$btn_show_me = $vk->buttonText('Показать', 'white', ['command' => 'btn_show_me']);
$btn_delete = $vk->buttonText('Удалить', 'red', ['command' => 'btn_delete']);
$btn_show_all = $vk->buttonText('Показать все', 'white', ['command' => 'btn_show_all']);
$btn_choose_day = $vk->buttonText('Выбрать день недели', 'white', ['command' => 'btn_choose_day']);

$btn_back_sub = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_sub']);
$btn_back_day_sub = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_day_sub']);
$btn_back_trot_sub = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_trot_sub']);
$btn_back_trob_sub = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_trob_sub']);

$btn_back = $vk->buttonText('Назад', 'red', ['command' => 'btn_back']);
$btn_back_status = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_status']);
$btn_back_trot_status = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_trot_status']);
$btn_back_trob_status = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_trob_status']);

$btn_back_my_sub = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_my_sub']);

$btn_back_show = $vk->buttonText('Назад', 'red', ['command' => 'btn_back_show']);
$btn_back_blue_sub = $vk->buttonText('Назад', 'blue', ['command' => 'btn_back_blue_sub']);

$btn_path_1 = $vk->buttonText('1','white',['command' => 'btn_path_1']);
$btn_path_2 = $vk->buttonText('2','white',['command' => 'btn_path_2']);
$btn_path_2a = $vk->buttonText('2a','white',['command' => 'btn_path_2a']);
$btn_path_3 = $vk->buttonText('3','white',['command' => 'btn_path_3']);
$btn_path_4 = $vk->buttonText('4','white',['command' => 'btn_path_4']);
$btn_path_5 = $vk->buttonText('5','white',['command' => 'btn_path_5']);
$btn_path_6 = $vk->buttonText('6','white',['command' => 'btn_path_6']);
$btn_path_7 = $vk->buttonText('7','white',['command' => 'btn_path_7']);
$btn_path_8 = $vk->buttonText('8','white',['command' => 'btn_path_8']);
$btn_path_9 = $vk->buttonText('9','white',['command' => 'btn_path_9']);
$btn_path_10 = $vk->buttonText('10','white',['command' => 'btn_path_10']);
$btn_path_11 = $vk->buttonText('11','white',['command' => 'btn_path_11']);
$btn_path_15 = $vk->buttonText('15','white',['command' => 'btn_path_15']);
$btn_path_16 = $vk->buttonText('16','white',['command' => 'btn_path_16']);

$btn_mon = $vk->buttonText('Понедельник','white',['command' => 'btn_mon']);
$btn_tue = $vk->buttonText('Вторник','white',['command' => 'btn_tue']);
$btn_wed = $vk->buttonText('Среда','white',['command' => 'btn_wed']);
$btn_thu = $vk->buttonText('Четверг','white',['command' => 'btn_thu']);
$btn_fri = $vk->buttonText('Пятница','white',['command' => 'btn_fri']);
$btn_sat = $vk->buttonText('Суббота','white',['command' => 'btn_sat']);
$btn_sun = $vk->buttonText('Воскресенье','white',['command' => 'btn_sun']);

$btn_time_5 = $vk->buttonText('5-6','white',['command' => 'btn_time_5']);
$btn_time_6 = $vk->buttonText('6-7','white',['command' => 'btn_time_6']);
$btn_time_7 = $vk->buttonText('7-8','white',['command' => 'btn_time_7']);
$btn_time_8 = $vk->buttonText('8-9','white',['command' => 'btn_time_8']);
$btn_time_9 = $vk->buttonText('9-10','white',['command' => 'btn_time_9']);
$btn_time_10 = $vk->buttonText('10-11','white',['command' => 'btn_time_10']);
$btn_time_11 = $vk->buttonText('11-12','white',['command' => 'btn_time_11']);
$btn_time_12 = $vk->buttonText('12-13','white',['command' => 'btn_time_12']);
$btn_time_13 = $vk->buttonText('13-14','white',['command' => 'btn_time_13']);
$btn_time_14 = $vk->buttonText('14-15','white',['command' => 'btn_time_14']);
$btn_time_15 = $vk->buttonText('15-16','white',['command' => 'btn_time_15']);
$btn_time_16 = $vk->buttonText('16-17','white',['command' => 'btn_time_16']);
$btn_time_17 = $vk->buttonText('17-18','white',['command' => 'btn_time_17']);
$btn_time_18 = $vk->buttonText('18-19','white',['command' => 'btn_time_18']);
$btn_time_19 = $vk->buttonText('19-20','white',['command' => 'btn_time_19']);
$btn_time_20 = $vk->buttonText('20-21','white',['command' => 'btn_time_20']);
$btn_time_21 = $vk->buttonText('21-22','white',['command' => 'btn_time_21']);
$btn_time_22 = $vk->buttonText('22-23','white',['command' => 'btn_time_22']);
$btn_time_23 = $vk->buttonText('23-24','white',['command' => 'btn_time_23']);
$btns_back_path_trol = [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], 
						[$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], 
						[$btn_path_11, $btn_path_15, $btn_path_16], 
						[$btn_back_trob_sub] ];
$btns_back_path_tram = [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], 
						[$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], 
						[$btn_back_trot_sub] ];
$btn_back_trol_sub_num = $vk->buttonText('Назад', 'red',['command' => 'btn_back_trol_sub_num']);
$btn_back_tram_sub_num = $vk->buttonText('Назад', 'red',['command' => 'btn_back_tram_sub_num']);
$btns_times_trol = [[$btn_time_5, $btn_time_6, $btn_time_7, $btn_time_8, $btn_time_9],
				[$btn_time_10, $btn_time_11, $btn_time_12, $btn_time_13, $btn_time_14],
				[$btn_time_15, $btn_time_16, $btn_time_17, $btn_time_18, $btn_time_19],
				[$btn_time_20, $btn_time_21, $btn_time_22, $btn_time_23],
				[$btn_back_trol_sub_num]];
$btns_times_tram = [[$btn_time_5, $btn_time_6, $btn_time_7, $btn_time_8, $btn_time_9],
				[$btn_time_10, $btn_time_11, $btn_time_12, $btn_time_13, $btn_time_14],
				[$btn_time_15, $btn_time_16, $btn_time_17, $btn_time_18, $btn_time_19],
				[$btn_time_20, $btn_time_21, $btn_time_22, $btn_time_23],
				[$btn_back_tram_sub_num]];
				

$timeUser = "Выберите время, в течение которого хотите получать уведомления
1 -- 05.00 - 06.00
2 -- 06.00 - 07.00
3 -- 07.00 - 08.00
4 -- 08.00 - 09.00
5 -- 09.00 - 10.00
6 -- 10.00 - 11.00
7 -- 11.00 - 12.00
8 -- 12.00 - 13.00
9 -- 13.00 - 14.00
10 -- 14.00 - 15.00
11 -- 15.00 - 16.00
12 -- 16.00 - 17.00
13 -- 17.00 - 18.00
14 -- 18.00 - 19.00
15 -- 19.00 - 20.00
16 -- 20.00 - 21.00                                                        
17 -- 21.00 - 22.00
18 -- 22.00 - 23.00
19 -- 23.00 - 00.00";

$HelloUser = "Возможности бота:

----------ДОБАВЛЕНИЕ----------
Если Вы хотите получать информацию о интересующем Вас маршруте, нажмите кнопку Запись и выберите данные о необходимом маршруте

----------МОИ ЗАПИСИ----------
Если Вы хотите увидеть свои подписки или удалить маршрут, нажмите кнопку Мои записи, после чего нажмите на кнопку Показать, чтобы получить Ваши маршруты; кнопку Удалить, чтобы удалить запись

----------СОСТОЯНИЕ-----------
Если Вы хотите получить информацию о состоянии транспорта, нажмите кнопку Состояние

----------ОТПИСАТЬСЯ----------
Если Вы хотите отписаться от всех уведомлений, нажмите кнопку Отписаться

Выберите команду";

$action;
$path;
$day;
$number;

if ($data->type == 'message_new') {
	
    $payload = $data->object->message->payload;
    if (isset($payload)) {
        $payload = json_decode($payload, True);
    }
	$payload = $payload['command'];
	
	$response = $mysqli->query("SELECT `action` FROM `users` WHERE `users`.`idUser` = '".$peer_id."'");
	$action = mysqli_fetch_assoc($response);
	$action = $action["action"];
	$response = $mysqli->query("SELECT `path` FROM `users` WHERE `users`.`idUser` = '".$peer_id."'");
	$path = mysqli_fetch_assoc($response);
	$path = $path["path"];
	$response = $mysqli->query("SELECT `day` FROM `users` WHERE `users`.`idUser` = '".$peer_id."'");
	$day = mysqli_fetch_assoc($response);
	$day = $day["day"];
	$response = $mysqli->query("SELECT `number` FROM `users` WHERE `users`.`idUser` = '".$peer_id."'");
	$number = mysqli_fetch_assoc($response);
	$number = $number["number"];
	
	
	switch($payload)
	{
		case "start": {
			$response = $mysqli->query("SELECT `id` FROM `users` WHERE `idUser` = '" . $peer_id . "'");
			$isSub = mysqli_fetch_assoc($response);
			if ($isSub == NULL) {
				$mysqli->query("INSERT INTO `users` (`id`, `idUser`, `action`, `day`, `path`, `number`)
								VALUES ('NULL', '" . $peer_id . "', 'start', '', '', '')");
			}
			$vk->sendButton($peer_id, $HelloUser, [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		} break;
		
		case "btn_sub": {
			$vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_sub]]);
			$mysqli->query("UPDATE `users` SET `action` = 'subscribe' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_status": {
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_status]]);
			$mysqli->query("UPDATE `users` SET `action` = 'status' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_my_sub": {
		    $vk->sendButton($peer_id, "Выберите действие", [[$btn_show_me], [$btn_delete], [$btn_back_blue_sub]]);
		} break;
		
		
		case "btn_show_me": {
		    $vk->sendButton($peer_id, "Выберите действие", [[$btn_show_all], [$btn_choose_day], [$btn_back_my_sub]]);
		} break;
		
		case "btn_show_all": {
            $textUserSubs = getAllUserSubs($peer_id, $mysqli, $daysOfWeek, $timesOfDay);
            $vk->sendMessage($peer_id, $textUserSubs);
		} break;
		
		case "btn_choose_day": {
		    $vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_show]]);
			$mysqli->query("UPDATE `users` SET `action` = 'my_sub' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_delete": {
		    $textUserSubs = "Выберите номер подписки: \n" . getAllUserSubs($peer_id, $mysqli, $daysOfWeek, $timesOfDay);
            $vk->sendMessage($peer_id, $textUserSubs);
		    $mysqli->query("UPDATE `users` SET `action` = 'delete' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_trot": {
			switch ($action) {
				case "subscribe": {
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], [$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], [$btn_back_trot_sub]]);
				} break;
				case "status": {
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], [$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], [$btn_back_trot_status]]);
				} break;
				default: {
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], [$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], [$btn_back]]);
				} break;
			}
			$mysqli->query("UPDATE `users` SET `path` = 'trot' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_trob": {
			switch ($action) {
				case "subscribe": {
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], [$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], [$btn_path_11, $btn_path_15, $btn_path_16], [$btn_back_trob_sub]]);
				} break;
				case "status": {
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], [$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], [$btn_path_11, $btn_path_15, $btn_path_16], [$btn_back_trob_status]]);
				} break;
				default: {
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], [$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], [$btn_path_11, $btn_path_15, $btn_path_16], [$btn_back]]);
				} break;
			}
			$mysqli->query("UPDATE `users` SET `path` = 'trob' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_unsub": {
		    $vk->sendButton($peer_id, "Вы уверены?", [[$btn_no], [$btn_yes]]);
		} break;
		
		case "btn_yes": {
		    $mysqli->query("DELETE FROM `users` WHERE `users`.`idUser` = " . $peer_id);
			$mysqli->query("DELETE FROM `subscriptions` WHERE `subscriptions`.`idUser` = " . $peer_id);
			$vk->sendButton($peer_id,"Для начала работы нажмите начать", [[$btn_start]]);
		} break;
		
		case "btn_no": {
		   $vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		   $mysqli->query("UPDATE `users` SET `action` = '', `path` = '', `day` = '', `number` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		
		case "btn_mon": 
		case "btn_tue": 
		case "btn_wed": 
		case "btn_thu": 
		case "btn_fri": 
		case "btn_sat": 
		case "btn_sun": {
			$numDay = $daysOfWeekPayload[$payload];
			switch($action) {
				case "my_sub": {
					$textUserSubs = getUserSubs($peer_id, $mysqli, $numDay, $daysOfWeek, $timesOfDay);
                	$vk->sendMessage($peer_id, $textUserSubs);
				} break;
				case "subscribe": {
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
				} break;
				default: {
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
				} break;
			}
			$mysqli->query("UPDATE `users` SET `day` = '" . $numDay . "' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_sub": 
		case "btn_back_blue_sub": {
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
			$mysqli->query("UPDATE `users` SET `action` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_day_sub": {
			$vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_sub]]);
			$mysqli->query("UPDATE `users` SET `day` = '', `number` = '0' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_trob_sub": {
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
			$mysqli->query("UPDATE `users` SET `number` = '0' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_trot_sub": {
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
			$mysqli->query("UPDATE `users` SET `number` = '0' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_trol_sub_num": {
			$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], [$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], [$btn_path_11, $btn_path_15, $btn_path_16], [$btn_back_trob_sub]]);
			$mysqli->query("UPDATE `users` SET `number` = '0' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_tram_sub_num": {
			$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], [$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], [$btn_back_trot_sub]]);
			$mysqli->query("UPDATE `users` SET `number` = '0' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_status": {
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
			$mysqli->query("UPDATE `users` SET `action` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_trob_status": 
		case "btn_back_trot_status": {
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_status]]);
			$mysqli->query("UPDATE `users` SET `path` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_my_sub": {
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_show_me], [$btn_delete], [$btn_back_blue_sub]]);
			$mysqli->query("UPDATE `users` SET `action` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back_show": {
		    $vk->sendButton($peer_id, "Выберите команду", [[$btn_show_all], [$btn_choose_day], [$btn_back_my_sub]]);
			$mysqli->query("UPDATE `users` SET `action` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		case "btn_back": {
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
			$mysqli->query("UPDATE `users` SET `action` = '', `path` = '', `day` = '', `number` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		} break;
		
		
		case "btn_path_1":
		case "btn_path_2": 
		case "btn_path_2a":
		case "btn_path_3":
		case "btn_path_4":
		case "btn_path_5":
		case "btn_path_6":
		case "btn_path_7":
		case "btn_path_8":
		case "btn_path_9":
		case "btn_path_10":
		case "btn_path_11": 
		case "btn_path_15": 
		case "btn_path_16": {
			$pathNum = $pathPayload[$payload];
			switch($path) {
				case "trot": {
					switch ($action) {
						case "subscribe": {
							$vk->sendButton($peer_id, "Выберите время", $btns_times_tram);
                            $mysqli->query("UPDATE `users` SET `number` = '" . $pathNum . "' WHERE `users`.`idUser` = '".$peer_id."'");
						} break;
						case "status": {
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '" . $pathNum . "'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
							//выводим информацию пользователю
                            if ($statusTransport == 1) {
                                $vk->sendMessage($peer_id, "Движение трамвая №" . $pathNum . " не прервано");
                            }
                            else {
								//текст поста, если транспорт ходит, то выводить текст не надо
								$response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '" . $pathNum . "'");
								$textTransport = mysqli_fetch_assoc($response);
								$textTransport = $textTransport["TextPost"];
                                $vk->sendMessage($peer_id, $textTransport);
                            }
						} break;
						default: {
							$vk->sendMessage($peer_id, $pathNum . "-й трамвай");
						} break;
					}
				} break;
				case "trob": {
					switch ($action) {
						case "subscribe": {
							$vk->sendButton($peer_id, "Выберите время", $btns_times_trol);
                            $mysqli->query("UPDATE `users` SET `number` = '" . $pathNum . "' WHERE `users`.`idUser` = '".$peer_id."'");
						} break;
						case "status": {
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '" . $pathNum . "'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
							//выводим информацию пользователю
                            if ($statusTransport == 1) {
                                if ($pathNum == 20) { $pathNum = "2а"; }
                                $vk->sendMessage($peer_id, "Движение троллейбуса №" . $pathNum . " не прервано");
                            }
                            else {
								//текст поста, если транспорт ходит, то выводить текст не надо
								$response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '" . $pathNum . "'");
								$textTransport = mysqli_fetch_assoc($response);
								$textTransport = $textTransport["TextPost"];
                                $vk->sendMessage($peer_id, $textTransport);
                            }
						} break;
						default: {
						    if ($pathNum == 20) { $pathNum = "2а"; }
							$vk->sendMessage($peer_id, $pathNum . "-й треллейбус");
						} break;
					}
				} break;
				default: {
					$vk->sendMessage($peer_id, $action." ".$path);
				} break;
			}
		} break;
		
		default: {
		    switch($action) {
		    case "subscribe":{
				$messag = substr($messag, 0, 2) - 4;
                if($messag >=1 && $messag <= 19) {
    		        $hour = $messag;
    		        switch($path){
                        case "trot": {
                            $idtime = $TRAMS[$number] * $ONETRANSPORT + ($day - 1) * $COUNTHOURS + $hour;
                        } break;
                        case "trob": {
                            $idtime = ($TROLLEYS[$number] + count($TRAMS)) * $ONETRANSPORT + ($day - 1) * $COUNTHOURS + $hour;
                        } break;
                    }
                   
                    $response = $mysqli->query("SELECT `id` FROM `subscriptions` WHERE `idUser` = '" . $peer_id . "' AND `idTime` = '" . $idtime . "'");
                    $havePost = mysqli_fetch_assoc($response);
                    $havePost = $havePost["id"];
                    if (!$havePost) {
                        $Ido = $mysqli->query("INSERT INTO `subscriptions` (`id`, `idTime`, `idUser`)
                                        VALUES ('NULL', '" . $idtime . "', '" . $peer_id . "')");
                        if ($Ido) {
                            $vk->sendMessage($peer_id, "&#10004; Изменения сохранены ");
                        }
                        else {
                            $vk->sendMessage($peer_id, "&#10060; Ошибка! Изменения не сохранены ");
                        }
                    }
                    else {
                        $vk->sendMessage($peer_id, "&#10060; Ошибка! Вы уже подписаны на уведомления в данное время ");
                    } 
                }
                else {
                    $vk->sendMessage($peer_id, "&#10060; Ошибка! Введены некорректные данные ");
                }
    		} break;
    		
    		case "delete": {
    		    //получаем id времени подписок пользователя
            	$response = $mysqli->query("SELECT `idTime` FROM `subscriptions` WHERE `idUser` = '" . $peer_id . "' ORDER BY `idTime`");
                $var = mysqli_fetch_assoc($response);
                $subs = array();
                for ($i = 0; $var != false; $i++) {
                    $subs[$i] = $var["idTime"];
                    $var = mysqli_fetch_assoc($response);
                }
                
                if ($messag >= 1 && $messag <= $i) {
                    $response = $mysqli->query("SELECT * FROM `times` WHERE `id` = '" . $subs[$messag - 1] . "'");
                    $var = mysqli_fetch_assoc($response);
                    $flagDelete = $mysqli->query("DELETE FROM `subscriptions` WHERE `subscriptions`.`idUser` = '" . $peer_id . "' AND `idTime` = '" . $var["id"] . "'");
                    if ($flagDelete) {
                        $txtUnsub = "&#10004; Подпиcка на уведомления по маршруту ";
                        if ($var["type"] == "tram") $txtUnsub .= "Трамвай №";
                        else $txtUnsub .= "Троллейбус №";
                        $txtUnsub .= $var["number"] . " " . $daysOfWeek[$var["day"]] . " " . $timesOfDay[$var["time"]];
                        $txtUnsub .= " успено отменена";
                        $vk->sendMessage($peer_id, $txtUnsub);
                        $txtUserSubs = "Ваши текущие подписки: \n" . getAllUserSubs($peer_id, $mysqli, $daysOfWeek, $timesOfDay);
                        $vk->sendMessage($peer_id, $txtUserSubs);
                    }
                    else {
                        $vk->sendMessage($peer_id, "&#10060; Ошибка! Изменения не сохранены ");
                    }
                }
                else {
                    $vk->sendMessage($peer_id, "&#10060; Ошибка! Введены некорректные данные ");
                }
    		} break;
    		
    		default: {
    		    $vk->sendMessage($peer_id, "&#10060; Ошибка! Введены некорректные данные ");
    		} break;
		    }
		} break;
	}
}

$mysqli->close();
?>