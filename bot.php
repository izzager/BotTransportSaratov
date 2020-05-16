<?php

require_once('simplevk-master/autoload.php'); // Подключение библиотеки


use DigitalStar\vk_api\VK_api as vk_api; // Основной класс
use DigitalStar\vk_api\VkApiException; // Обработка ошибок
$vk = vk_api::create(VK_KEY, VERSION)->setConfirm(CONFIRM_STR);
$data = json_decode(file_get_contents('php://input')); //Получает и декодирует JSON пришедший из ВК
$vk->sendOK(); //Говорим vk, что мы приняли callback

$peer_id = $data->object->message->peer_id;
$messag = $data->object->message->text; // Само сообщение от пользователя


//$vk->initVars($id, $mess, $payload, $user_id); //инициализация переменных
//$vk->sendMessage($peer_id, "Работаю!");
$btn_start = $vk->buttonText('Начать', 'green', ['command' => 'start']);
$btn_sub = $vk->buttonText('Запись', 'green', ['command' => 'btn_sub']);
$btn_status = $vk->buttonText('Состояние', 'blue', ['command' => 'btn_status']);
$btn_unsub = $vk->buttonText('Отписаться', 'red', ['command' => 'btn_unsub']);
$btn_my_sub = $vk->buttonText('Мои записи','white',['command' => 'btn_my_sub']);
$btn_trot = $vk->buttonText('Трамвай','white',['command' => 'btn_trot']);
$btn_trob = $vk->buttonText('Троллейбус','white',['command' => 'btn_trob']);
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
	
	switch($payload)
	{
		case "start":
		{
			$vk->sendButton($peer_id, $HelloUser, [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		}break;
		
		case "btn_sub":
		{
			$vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_sub]]);
		}break;
		
		case "btn_status":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_status]]);
		}break;
		
		case "btn_my_sub":
		{
		    $vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_my_sub]]);
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
		}break;
		
		case "btn_unsub":
		{
		    $vk->sendButton($peer_id, "Отписка", [[$btn_start]]);
		}break;
		
		
		case "btn_mon":
		{
			switch($action)
			{
				case "my_sub":
				{
				    $vk->sendMessage($peer_id, "Событие для этого дня");
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
		}break;
		
		case "btn_tue":
		{
			switch($action)
			{
				case "my_sub":
				{
				    $vk->sendMessage($peer_id, "Событие для этого дня");
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
		}break;
		
		case "btn_wed":
		{
			switch($action)
			{
				case "my_sub":
				{
				    $vk->sendMessage($peer_id, "Событие для этого дня");
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
		}break;
		
		case "btn_thu":
		{
			switch($action)
			{
				case "my_sub":
				{
				    $vk->sendMessage($peer_id, "Событие для этого дня");
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
		}break;
		
		case "btn_fri":
		{
			switch($action)
			{
				case "my_sub":
				{
				    $vk->sendMessage($peer_id, "Событие для этого дня");
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
		}break;
		
		case "btn_sat":
		{
			switch($action)
			{
				case "my_sub":
				{
				    $vk->sendMessage($peer_id, "Событие для этого дня");
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
		}break;
		
		case "btn_sun":
		{
			switch($action)
			{
				case "my_sub":
				{
				    $vk->sendMessage($peer_id, "Событие для этого дня");
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
		}break;
		
		case "btn_back_sub":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		}break;
		
		case "btn_back_day_sub":
		{
			$vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back_sub]]);
		}break;
		
		case "btn_back_trob_sub":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "btn_back_trot_sub":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_day_sub]]);
		}break;
		
		case "btn_back_status":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		}break;
		//ъеъ
		case "btn_back_trob_status":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_status]]);
		}break;
		
		case "btn_back_trot_status":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back_status]]);
		}break;
		
		case "btn_back_my_sub":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_show_me], [$btn_delete], [$btn_back_blue_sub]]);
		}break;
		
		case "btn_back":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		}break;
		
		
		case "btn_path_1":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, "Событие для этого номера");
				}break;
				case "status":
				{
				    $vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						    $vk->sendMessage($peer_id, "Состояние для этого номера");
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "2-й треллейбус");
						}break;
					}
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "2 номер транспорта");
				}break;
			}
		}break;
		
		case "btn_path_2a":
		{
			switch ($action)
			{
				case "subscribe":
				{
					$vk->sendMessage($peer_id, "Событие для этого номера");
				}break;
				case "status":
				{
					$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
					$vk->sendMessage($peer_id, "Событие для этого номера");
				}break;
				case "status":
				{
					$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
					$vk->sendMessage($peer_id, "Событие для этого номера");
				}break;
				case "status":
				{
					$vk->sendMessage($peer_id, "Состояние для этого номера");
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
					$vk->sendMessage($peer_id, "Событие для этого номера");
				}break;
				case "status":
				{
					$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
						}break;
						default:
						{
							$vk->sendMessage($peer_id, "10-й треллейбус");
						}break;
				}break;
				default:
				{
				    $vk->sendMessage($peer_id, "10-й номер транспорта");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
							
							$vk->sendMessage($peer_id, "Событие для этого номера");
						}break;
						case "status":
						{
						   
							$vk->sendMessage($peer_id, "Состояние для этого номера");
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
					$vk->sendMessage($peer_id, "Событие для этого номера");
				}break;
				case "status":
				{
					$vk->sendMessage($peer_id, "Состояние для этого номера");
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
					$vk->sendMessage($peer_id, "Событие для этого номера");
				}break;
				case "status":
				{
					$vk->sendMessage($peer_id, "Состояние для этого номера");
				}break;
				default:
				{
					$vk->sendMessage($peer_id, "16-й треллейбус");
				}break;
			}
		}break;
		
	    case "Ага":
		{
			$btn_check = $vk->buttonText('10:00 - 11:30','white',['command' => 'btn_path_2']);
			$vk->sendButton($peer_id, "as", [[$btn_check, $btn_check], [$btn_check, $btn_check], [$btn_check, $btn_check], [$btn_check, $btn_check], [$btn_check, $btn_check]], true);
		}break;
		
		default : 
		{
		    
		}break;
	}
}

?>