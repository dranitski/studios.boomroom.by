<?php

	defined('access') or die ('no access');
	
	// Параметры
	$params = loadObject("SELECT * FROM `params`");
	
	$rid = (getInt('rid', 'get')) ? getInt('rid', 'get') : 1;
	
	$date_start = (getVar('date', 'get')) ? getVar('date', 'get') : date('d.m.Y');
	$days = 7;
	
	// Шаблоны
	$templates = loadObjectList("SELECT * FROM `event_templates` WHERE `rid` = {$rid}");
	
	// События
	$date_from = date('Y-m-d', strtotime($date_start));
	$date_to = date('Y-m-d', strtotime("{$date_start} + 7 days"));
	$events = loadObjectList("SELECT * FROM `events` WHERE `rid` = {$rid} AND `date` >= '{$date_from}' AND `date` <= '{$date_to}' AND `state` != 4");
	
	// Постоянки
	$event_weekly = loadObjectList("SELECT * FROM `event_weekly` WHERE `rid` = {$rid} AND `state` = 1");
	
	// Комната
	$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
	$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
	
	// Устанавливаем HEAD
	$title = "Расписание";
	
	$tags['style'][] = "/tmpl/admin/css/table.css";
	
	$tags['script'][] = "/tmpl/admin/js/table.js";
	$tags['script'][] = "/tmpl/admin/js/table-edit.js";
	
	
	$tags['script'][] = "/plugins/infowindow/script.js";
	$tags['style'][] = "/plugins/infowindow/style.css";
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	
?>