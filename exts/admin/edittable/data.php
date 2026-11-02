<?php

	defined('access') or die ('no access');
	
	if (!$user->director) {
		
		// redirect('/admin/table', "Доступ запрещен", "error");
	}
	
	// Параметры
	$params = loadObject("SELECT * FROM `params`");
	
	$rid = (getInt('rid', 'get')) ? getInt('rid', 'get') : 1;
	
	$date_start = (getVar('date', 'get')) ? getVar('date', 'get') : date('d.m.Y');
	$days = 7;
	
	$templates = loadObjectList("SELECT * FROM `event_templates` WHERE `rid` = {$rid}");
	
	// Комната
	$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
	$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
	
	// Устанавливаем HEAD
	$title = "Настройка расписания и цен";
	
	$tags['style'][] = "/tmpl/admin/css/table.css";
	
	$tags['script'][] = "/tmpl/admin/js/table.js";
	$tags['script'][] = "/tmpl/admin/js/table-edit.js";
	
	
	$tags['script'][] = "/plugins/infowindow/script.js";
	$tags['style'][] = "/plugins/infowindow/style.css";
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	
?>