<?php

	defined('access') or die ('no access');
	
	// Параметры
	$params = loadObject("SELECT * FROM `params`");
	
	$studios = loadObjectList("SELECT * FROM `studios`");
	$rooms = loadObjectList("SELECT * FROM `rooms`");
	
	$sid = (getInt('sid', 'get')) ? getInt('sid', 'get') : 1;
	
	if (getInt('rid', 'get')) {
		
		$rid = getInt('rid', 'get');
	}
	else {
	
		foreach ($rooms as $room) { if ($sid == $room->sid) { $rid = $room->id; break; } }
	}
	
	$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
	$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
	
	$date_start = (getVar('date', 'get')) ? getVar('date', 'get') : date('d.m.Y');
	$days = 7;
	
	// Шаблоны
	$templates = loadObjectList("SELECT * FROM `event_templates` WHERE `rid` = {$rid}");
	
	// События
	$date_from = date('Y-m-d', strtotime($date_start));
	$date_to = date('Y-m-d', strtotime("{$date_start} + 7 days"));
	$events = loadObjectList("SELECT * FROM `events` WHERE `rid` = {$rid} AND `date` >= '{$date_from}' AND `date` <= '{$date_to}'");
	
	// Постоянки
	$event_weekly = loadObjectList("SELECT * FROM `event_weekly` WHERE `rid` = {$rid} AND `state` = 1");
	
	// Устанавливаем HEAD
	$title = "Онлайн-бронирование студии";
	
	$tags['style'][] = "/exts/booking/inc/repaby.css?v=2";
	$tags['script'][] = "/exts/booking/inc/repaby.js?v=2";
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	
?>