<?php

	defined('access') or die ('no access');
	
	// Параметры
	$params = loadObject("SELECT * FROM `params`");
	
	$date = (getVar('date', 'get')) ? date('d.m.Y', strtotime(getVar('date', 'get'))) : date('d.m.Y');
	$day = date('w', strtotime($date));
	$sqlDate = date('Y-m-d', strtotime($date));
	
	$rooms = loadObjectList("SELECT * FROM `rooms` ORDER BY `id` ASC");
	
	// Шаблоны
	$templates = loadObjectList("SELECT * FROM `event_templates` WHERE `day` = {$day}");
	
	// События
	$events = loadObjectList("SELECT * FROM `events` WHERE `date` = '{$sqlDate}' AND `state` < 4");
	
	// Постоянки
	$event_weekly = loadObjectList("SELECT * FROM `event_weekly` WHERE `day` = {$day} AND `state` = 1");
	
	// Устанавливаем HEAD
	$title = "Онлайн-бронирование студии";
	
	$tags['style'][] = "/exts/booking/inc/style.css?v=2";
	$tags['style'][] = "/exts/booking/inc/style-rooms.css";
	$tags['script'][] = "/exts/booking/inc/script.js?v=2";
	
	
	$tags['script'][] = "/plugins/infowindow/script.js";
	$tags['style'][] = "/plugins/infowindow/style.css";
	
	$tags['script'][] = "/plugins/datepick/datepick.js";
	$tags['script'][] = "/plugins/datepick/datepick-ru.js";
	$tags['style'][] = "/plugins/datepick/datepick.css";
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	
?>