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

$action;
$path;
$day;
$number;

if ($data->type == 'message_new') {
	
    $payload = $data->object->message->payload;
    if (isset($payload))
    {
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
		case "start":
		{
			$response = $mysqli->query("SELECT `id` FROM `users` WHERE `idUser` = '" . $peer_id . "'");
			$isSub = mysqli_fetch_assoc($response);
			if ($isSub == NULL) {
				$mysqli->query("INSERT INTO `users` (`id`, `idUser`, `action`, `day`, `path`, `number`)
								VALUES ('NULL', '" . $peer_id . "', 'start', '', '', '')");
			}
			$vk->sendButton($peer_id, $HelloUser, [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		}break;
		
		case "btn_sub":
		{
			$vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_sub]]);
			$mysqli->query("UPDATE `users` SET `action` = 'subscribe' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_status":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_status]]);
			$mysqli->query("UPDATE `users` SET `action` = 'status' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_my_sub":
		{
		    $vk->sendButton($peer_id, "Выберите действие", [[$btn_show_me], [$btn_delete], [$btn_back_blue_sub]]);
		}break;
		
		case "btn_choose_day" :
		{
		    $vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_show]]);
			$mysqli->query("UPDATE `users` SET `action` = 'my_sub' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
	
		case "btn_trot":
		{
			switch($action)
			{
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], [$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], [$btn_back_trot_sub]]);
				}break;
				case "status":
				{
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], [$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], [$btn_back_trot_status]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], [$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `path` = 'trot' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_trob":
		{
			switch($action)
			{
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], [$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], [$btn_path_11, $btn_path_15, $btn_path_16], [$btn_back_trob_sub]]);
				}break;
				case "status":
				{
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], [$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], [$btn_path_11, $btn_path_15, $btn_path_16], [$btn_back_trob_status]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], [$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], [$btn_path_11, $btn_path_15, $btn_path_16], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `path` = 'trob' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		
		case "btn_mon":
		{
			switch($action)
			{
				case "my_sub":
				{
                    $textUserSubs = getUserSubs($peer_id, $mysqli, 1, $daysOfWeek, $timesOfDay);
                	$vk->sendMessage($peer_id, $textUserSubs);
				}break;
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `day` = '1' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_tue":
		{
			switch($action)
			{
				case "my_sub":
				{
					$textUserSubs = getUserSubs($peer_id, $mysqli, 2, $daysOfWeek, $timesOfDay);
                	$vk->sendMessage($peer_id, $textUserSubs);
				}break;
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `day` = '2' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_wed":
		{
			switch($action)
			{
				case "my_sub":
				{
				    $textUserSubs = getUserSubs($peer_id, $mysqli, 3, $daysOfWeek, $timesOfDay);
                	$vk->sendMessage($peer_id, $textUserSubs);
				}break;
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `day` = '3' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_thu":
		{
			switch($action)
			{
				case "my_sub":
				{
					$textUserSubs = getUserSubs($peer_id, $mysqli, 4, $daysOfWeek, $timesOfDay);
                	$vk->sendMessage($peer_id, $textUserSubs);
				}break;
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `day` = '4' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_fri":
		{
			switch($action)
			{
				case "my_sub":
				{
					$textUserSubs = getUserSubs($peer_id, $mysqli, 5, $daysOfWeek, $timesOfDay);
                	$vk->sendMessage($peer_id, $textUserSubs);
				}break;
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `day` = '5' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_sat":
		{
			switch($action)
			{
				case "my_sub":
				{
					$textUserSubs = getUserSubs($peer_id, $mysqli, 6, $daysOfWeek, $timesOfDay);
                	$vk->sendMessage($peer_id, $textUserSubs);
				}break;
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `day` = '6' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_sun":
		{
			switch($action)
			{
				case "my_sub":
				{
					$textUserSubs = getUserSubs($peer_id, $mysqli, 7, $daysOfWeek, $timesOfDay);
                	$vk->sendMessage($peer_id, $textUserSubs);
				}break;
				case "subscribe":
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
				}break;
				default:
				{
					$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
				}break;
			}
			$mysqli->query("UPDATE `users` SET `day` = '7' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back_sub":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
			$mysqli->query("UPDATE `users` SET `action` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back_day_sub":
		{
			$vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_sub]]);
			$mysqli->query("UPDATE `users` SET `day` = '', `number` = '0' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back_trob_sub":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
			$mysqli->query("UPDATE `users` SET `number` = '0' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back_trot_sub":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
			$mysqli->query("UPDATE `users` SET `number` = '0' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back_status":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
			$mysqli->query("UPDATE `users` SET `action` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		//ъеъ
		case "btn_back_trob_status":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_status]]);
			$mysqli->query("UPDATE `users` SET `path` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back_trot_status":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_status]]);
			$mysqli->query("UPDATE `users` SET `path` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back_my_sub":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_show_me], [$btn_delete], [$btn_back_blue_sub]]);
			$mysqli->query("UPDATE `users` SET `action` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back_show" :
		{
		    $vk->sendButton($peer_id, "Выберите команду", [[$btn_show_all], [$btn_choose_day], [$btn_back_my_sub]]);
			$mysqli->query("UPDATE `users` SET `action` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		case "btn_back":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
			$mysqli->query("UPDATE `users` SET `action` = '', `path` = '', `day` = '', `number` = '' WHERE `users`.`idUser` = '".$peer_id."'");
		}break;
		
		
		case "btn_path_1":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, $timeUser);
                    $mysqli->query("UPDATE `users` SET `number` = '1' WHERE `users`.`idUser` = '".$peer_id."'");
				}break;
				case "status":
				{
				    //получаем статус - 1 если ходит и 0 если не ходит
				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '1'");
				    $statusTransport = mysqli_fetch_assoc($response);
				    $statusTransport = $statusTransport["Status"];
				    
				    //текст поста, если транспорт ходит, то выводить текст не надо
				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '1'");
                    $textTransport = mysqli_fetch_assoc($response);
                    $textTransport = $textTransport["TextPost"];
                    
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "1-й треллейбус");
				}break;
			}
		}break;
		
		case "btn_path_2":
		{
			switch($path)
			{
				case "trot":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '2' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '2'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '2'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "2-й трамвай");
						}break;
					}
				}break;
				case "trob":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '2' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '2'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '2'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
							
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "2-й треллейбус");
						}break;
					}
				}break;
				default:
				{
					$vk->sendMessage($peer_id, $action." ".$path);
				}break;
			}
		}break;
		
		case "btn_path_2a":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, $timeUser);
                    $mysqli->query("UPDATE `users` SET `number` = '20' WHERE `users`.`idUser` = '".$peer_id."'");
				}break;
				case "status":
				{
				    //получаем статус - 1 если ходит и 0 если не ходит
				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '20'");
				    $statusTransport = mysqli_fetch_assoc($response);
				    $statusTransport = $statusTransport["Status"];
				    
				    //текст поста, если транспорт ходит, то выводить текст не надо
				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '20'");
                    $textTransport = mysqli_fetch_assoc($response);
                    $textTransport = $textTransport["TextPost"];
                    
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "2а-й треллейбус");
				}break;
			}
		}break;
		
		case "btn_path_3":
		{
			switch($path)
			{
				case "trot":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '3' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '3'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '3'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "3-й трамвай");
						}break;
					}
				}break;
				case "trob":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '3' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '3'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '3'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "3-й треллейбус");
						}break;
					}
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "3-й номер транспорта");
				}break;
			}
		}break;
		
		case "btn_path_4":
		{
			switch($path)
			{
				case "trot":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '4' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '4'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '4'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "4-й трамвай");
						}break;
					}
				}break;
				case "trob":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '4' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '4'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '4'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "4-й треллейбус");
						}break;
					}
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "4-й номер транспорта");
				}break;
			}
		}break;
		
		case "btn_path_5":
		{
			switch($path)
			{
				case "trot":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '5' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '5'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '5'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "5-й трамвай");
						}break;
					}
				}break;
				case "trob":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '5' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '5'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '5'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "5-й треллейбус");
						}break;
					}
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "5-й номер транспорта");
				}break;
			}
		}break;
		
		case "btn_path_6":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, $timeUser);
                    $mysqli->query("UPDATE `users` SET `number` = '6' WHERE `users`.`idUser` = '".$peer_id."'");
				}break;
				case "status":
				{
				    //получаем статус - 1 если ходит и 0 если не ходит
				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '6'");
				    $statusTransport = mysqli_fetch_assoc($response);
				    $statusTransport = $statusTransport["Status"];
				    
				    //текст поста, если транспорт ходит, то выводить текст не надо
				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '6'");
                    $textTransport = mysqli_fetch_assoc($response);
                    $textTransport = $textTransport["TextPost"];
                    
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "6-й трамвай");
				}break;
			}
		}break;
		
		case "btn_path_7":
		{
			switch($path)
			{
				case "trot":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '7' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '7'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '7'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "7-й трамвай");
						}break;
					}
				}break;
				case "trob":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '7' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '7'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '7'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "7-й треллейбус");
						}break;
					}
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "7-й номер транспорта");
				}break;
			}
		}break;
		
		case "btn_path_8":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, $timeUser);
                    $mysqli->query("UPDATE `users` SET `number` = '8' WHERE `users`.`idUser` = '".$peer_id."'");
				}break;
				case "status":
				{
				    //получаем статус - 1 если ходит и 0 если не ходит
				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '8'");
				    $statusTransport = mysqli_fetch_assoc($response);
				    $statusTransport = $statusTransport["Status"];
				    
				    //текст поста, если транспорт ходит, то выводить текст не надо
				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '8'");
                    $textTransport = mysqli_fetch_assoc($response);
                    $textTransport = $textTransport["TextPost"];
                    
				}break;
				default:
				{
				    $vk->sendMessage($peer_id, "8-й трамвай");
				}break;
			}
		}break;
		
		case "btn_path_9":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, $timeUser);
                    $mysqli->query("UPDATE `users` SET `number` = '9' WHERE `users`.`idUser` = '".$peer_id."'");
				}break;
				case "status":
				{
				    //получаем статус - 1 если ходит и 0 если не ходит
				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '9'");
				    $statusTransport = mysqli_fetch_assoc($response);
				    $statusTransport = $statusTransport["Status"];
				    
				    //текст поста, если транспорт ходит, то выводить текст не надо
				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '9'");
                    $textTransport = mysqli_fetch_assoc($response);
                    $textTransport = $textTransport["TextPost"];
                    
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "9-й трамвай");
				}break;
			}
		}break;
		
		case "btn_path_10":
		{
			switch($path)
			{
				case "trot":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '10' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '10'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '10'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
							
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "10-й трамвай");
						}break;
					}
				}break;
				case "trob":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '10' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '10'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '10'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
						    
						}
					}
				}break;
				default:
				{
				    $vk->sendMessage($peer_id, "10-й треллейбус");
				}break;
			}
		}break;
		
		case "btn_path_11":
		{
			switch($path)
			{
				case "trot":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '11' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '11'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Tram' AND `NumberTransport` = '11'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "11-й трамвай");
						}break;
					}
				}break;
				case "trob":
				{
					switch ($action)
					{
						case "subscribe":
						{
							$vk->sendMessage($peer_id, $timeUser);
                            $mysqli->query("UPDATE `users` SET `number` = '11' WHERE `users`.`idUser` = '".$peer_id."'");
						}break;
						case "status":
						{
						    //получаем статус - 1 если ходит и 0 если не ходит
        				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '11'");
        				    $statusTransport = mysqli_fetch_assoc($response);
        				    $statusTransport = $statusTransport["Status"];
        				    
        				    //текст поста, если транспорт ходит, то выводить текст не надо
        				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '11'");
                            $textTransport = mysqli_fetch_assoc($response);
                            $textTransport = $textTransport["TextPost"];
                            
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "11-й треллейбус");
						}break;
					}
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "11-й номер транспорта");
				}break;
			}
		}break;
		
		case "btn_path_15":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, $timeUser);
                    $mysqli->query("UPDATE `users` SET `number` = '15' WHERE `users`.`idUser` = '".$peer_id."'");
				}break;
				case "status":
				{
				    //получаем статус - 1 если ходит и 0 если не ходит
				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '15'");
				    $statusTransport = mysqli_fetch_assoc($response);
				    $statusTransport = $statusTransport["Status"];
				    
				    //текст поста, если транспорт ходит, то выводить текст не надо
				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '15'");
                    $textTransport = mysqli_fetch_assoc($response);
                    $textTransport = $textTransport["TextPost"];
                    
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "15-й треллейбус");
				}break;
			}
		}break;
		
		case "btn_path_16":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, $timeUser);
                    $mysqli->query("UPDATE `users` SET `number` = '16' WHERE `users`.`idUser` = '".$peer_id."'");
				}break;
				case "status":
				{
				    //получаем статус - 1 если ходит и 0 если не ходит
				    $response = $mysqli->query("SELECT `Status` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '16'");
				    $statusTransport = mysqli_fetch_assoc($response);
				    $statusTransport = $statusTransport["Status"];
				    
				    //текст поста, если транспорт ходит, то выводить текст не надо
				    $response = $mysqli->query("SELECT `TextPost` FROM `transport` WHERE `TypeTransport` = 'Trolley' AND `NumberTransport` = '16'");
                    $textTransport = mysqli_fetch_assoc($response);
                    $textTransport = $textTransport["TextPost"];
                    
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "16-й треллейбус");
				}break;
			}
		}break;
		
	    
		default : 
		{
		    
		}break;
	}
}

$mysqli->close();
?>