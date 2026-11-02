<?php
	
	defined('access') or die ('no access');
	
	$subtask = getVar('subtask', 'post');
	$option = getVar('option', 'post');
	
	switch ($subtask) {
		
		/*
		case 'savepay':
		
			$id = getInt('id', 'post');
			$pay = getVar('pay', 'post');
			
			runSql("UPDATE `events` SET `pay` = '{$pay}' WHERE `id` = {$id}");
			
			$event = loadObject("SELECT * FROM `events` WHERE `id` = {$id}");
			
			if ($pay == $event->price) {		
				
				$client = loadObject("SELECT * FROM `clients` WHERE `id` = {$event->cid}");
				$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$event->rid}");
				$studio = loadObject("SELECT * FROM `studios` WHERE `id` = {$room->sid}");
				
				$date = date('d.m.Y', strtotime($event->date));
				
				$name = "Группа: {$client->name}. Долг за {$date}, {$event->start}:00 - {$event->end}:00. {$studio->name}, {$room->name}";
				
				// Записываем в доп.доход
				runSql("INSERT INTO `incomes` (`sid`, `aid`, `name`, `sum`, `date`, `created`) VALUES (1, 1, '{$name}', '{$pay}', NOW(), NOW())"); $income_id = insertId();
			}
			
			echo getMsg("Платеж сохранен", "ok", "location.reload();");
		
		break;
		*/
	
		case 'savepay':
		
			$id = getInt('id', 'post');
			$pay = getVar('pay', 'post');
			
			if ((double) $pay <= 0) {
				
				echo getMsg("Введите сумму платежа", "error", "");
				exit;
			}
			
			$sql = "SELECT a.*," .
			" (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client_name`,".
			" (SELECT `sid` FROM `rooms` WHERE `id` = a.`rid`) AS `sid`,".
			" (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room`".
			" FROM `events` AS a".
			" WHERE `id` = {$id}";
	
			$event = loadObject($sql);
			
			$band_name = ($event->client_name) ? $event->client_name : $event->note;
			$studio = getStudioName($event->sid);
			$date = date('d.m.Y', strtotime($event->date));
			
			$string = "Погашение долга. Группа: {$band_name}. {$date}, {$event->start}:00 - {$event->end}:00. {$studio}, {$event->room}";
			
			// Для BOOM
			// if ($event->sid == 1) {
				
				runSql("INSERT INTO `incomes` (`sid`, `aid`, `eid`, `name`, `sum`, `date`, `created`) VALUES ({$event->sid}, 1, {$id}, '".dbEscape($string)."', '{$pay}', NOW(), NOW())");
				
				$id = insertId();
			// }
			// else {
				
				// $pay += $event->pay;
				// runSql("UPDATE `events` SET `pay` = '{$pay}' WHERE `id` = {$id}");
				
			// }
			
			// Журнал
			$log = $string;
			addLog($log);
			
			echo getMsg("Платеж сохранен.", "ok", "location.reload();");
		
		break;
	
		case 'delete':
			
			$id = getInt('id', 'post');
			
			$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
			
			runSql("DELETE FROM `events` WHERE `id` = {$id}");
			
			// Журнал
			$date = date('d.m.Y', strtotime($event->date));
			
			$log = "Удаление долга (события). ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
			addLog($log);
			
			echo getMsg("Долг удален", "ok", "location.reload();");
		
		break;
	}
	
	
?>