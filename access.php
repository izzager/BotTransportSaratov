<?php
/////////////////////////////СЛУЖЕБНОЕ ДЯ БАЗЫ ДАННЫХ/////////////////////////////////////////////////////////
$mysqli = new mysqli("***", "***", "***", "***");
$mysqli->query("SET NAMES 'utf8'");
$TRAMS = [2 => 0,3 => 1,4 => 2,5 => 3,6 => 4,7 => 5,8 => 6,9 => 7,10 => 8,11 => 9];
$TROLLEYS = [1 => 0,20 => 1,2 => 2,3 => 3,4 => 4,5 => 5,7 => 6,10 => 7,11 => 8,15 => 9,16 => 10];
$HOURS = [5 => 1,6 => 2,7 => 3,8 => 4,9 => 5,10 => 6,11 => 7,12 => 8,13 => 9,14 => 10,
          15 => 11,16 => 12,17 => 13,18 => 14,19 => 15,20 => 16,21 => 17,22 => 18,23 => 19];
$COUNTHOURS = count($HOURS);
$ONETRANSPORT = $COUNTHOURS * 7; //кол-во рабочих часов на 7 дней
$daysOfWeek = [1 => "пн", 2 => "вт", 3 => "ср", 4 => "чт", 5 => "пт", 6 => "сб", 7 => "вс"];
$timesOfDay = [5 => "05.00 - 06.00", 6 => "06.00 - 07.00", 7 => "07.00 - 08.00", 8 => "08.00 - 09.00", 9 => "09.00 - 10.00", 10 => "10.00 - 11.00", 
                11 => "11.00 - 12.00", 12 => "12.00 - 13.00", 13 => "13.00 - 14.00", 14 => "14.00 - 15.00", 15 => "15.00 - 16.00", 16 => "16.00 - 17.00",
                17 => "17.00 - 18.00", 18 => "18.00 - 19.00", 19 => "19.00 - 20.00", 20 => "20.00 - 21.00", 21 => "21.00 - 22.00", 22 => "22.00 - 23.00",
                23 => "23.00 - 00.00"];
$daysOfWeekPayload = ["btn_mon" => 1, "btn_tue" => 2, "btn_wed" => 3, "btn_thu" => 4, "btn_fri" => 5, "btn_sat" => 6, "btn_sun" => 7];
$pathPayload = ["btn_path_1" => 1, "btn_path_2a" => 20, "btn_path_2" => 2, "btn_path_3" => 3, "btn_path_4" => 4, "btn_path_5" => 5, "btn_path_6" => 6, "btn_path_7" => 7,
				"btn_path_8" => 8, "btn_path_9" => 9, "btn_path_10" => 10, "btn_path_11" => 11, "btn_path_15" => 15, "btn_path_16" => 16];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

const VK_KEY = "***";  // Токен сообщества
const OLEG_KEY = "***";
const CONFIRM_STR = "***";  // Тот самый ключ из сообщества
const VERSION = "5.124"; // Версия API VK 