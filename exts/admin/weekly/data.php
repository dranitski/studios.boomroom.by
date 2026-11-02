<?php

	defined('access') or die ('no access');
	
	$rid = (getInt('rid', 'get')) ? getInt('rid', 'get') : 1;
	
	$date_start = '08.01.1990'; // Понедельник)
	$days = 7;
	
	// Шаблоны
	$templates = loadObjectList("SELECT * FROM `event_templates` WHERE `rid` = {$rid}");
	
	// События
	$events = loadObjectList("SELECT * FROM `event_weekly` WHERE `rid` = {$rid} AND `state` = 1");
	
	// Комната
	$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
	$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
	
	// Устанавливаем HEAD
	$title = "Постоянные репетиции";
	
	$tags['style'][] = "/tmpl/admin/css/table.css";
	
	$tags['script'][] = "/tmpl/admin/js/table.js";
	$tags['script'][] = "/tmpl/admin/js/table-weekly.js";
	
	
	$tags['script'][] = "/plugins/infowindow/script.js";
	$tags['style'][] = "/plugins/infowindow/style.css";
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	
?>