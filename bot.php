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
$btn_delete = $vk->buttonText('Удалить', 'red', ['command' => 'btn_delete']);


$btn_back = $vk->buttonText('Назад', 'red', ['command' => 'btn_back']);

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
	switch($data->object->message->text)
	{
		case "Начать":
		{
			$vk->sendButton($peer_id, $HelloUser, [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		}break;
		
		case "Запись":
		{
			$vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back]]);
		}break;
		
		case "Состояние":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "Мои записи":
		{
		    $vk->sendButton($peer_id, "Выберите день недели", [[$btn_mon, $btn_tue, $btn_wed], [$btn_thu, $btn_fri], [$btn_sat, $btn_sun], [$btn_back]]);
		}break;
		
		case "Трамвай":
		{
			$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_2, $btn_path_3, $btn_path_4, $btn_path_5, $btn_path_6], [$btn_path_7, $btn_path_8, $btn_path_9, $btn_path_10, $btn_path_11], [$btn_back]]);
		}break;
		
		case "Троллейбус":
		{
			$vk->sendButton($peer_id, "Выберите путь", [[$btn_path_1, $btn_path_2, $btn_path_2a, $btn_path_3], [$btn_path_4, $btn_path_5, $btn_path_7, $btn_path_10], [$btn_path_11, $btn_path_15, $btn_path_16], [$btn_back]]);
		}break;
		
		case "Отписаться":
		{
			$vk->sendButton($peer_id, "Отписка", [[$btn_start]]);
		}break;
		
		
		case "Понедельник":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "Вторник":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "Среда":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "Четверг":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "Пятница":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "Суббота":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "Воскресенье":
		{
			$vk->sendButton($peer_id, "Выберите вид транспорта", [[$btn_trot], [$btn_trob], [$btn_back]]);
		}break;
		
		case "Назад":
		{
			$vk->sendButton($peer_id, "Выберите команду", [[$btn_sub], [$btn_status], [$btn_my_sub], [$btn_unsub]]);
		}break;
		
		
		case "1":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "2":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "2a":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "3":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "4":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "5":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "6":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "7":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "8":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "9":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "10":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "11":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "15":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
		}break;
		
		case "16":
		{
			$vk->sendMessage($peer_id, "Событие для этого номера");
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