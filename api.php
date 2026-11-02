<?php

	// Only the server's own cron (curl via nginx on 127.0.0.1) may call the API
	if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
		header('HTTP/1.1 403 Forbidden');
		exit;
	}
	
	set_time_limit(0);
	
	define('access', 1);
	
	// Настройки
	include 'config.php';
	include path.'/functions/main.php';
	include path.'/functions/db.php';
	
	$YmdHis	= date('Y-m-d H:i:s');
	$Ymd	= date('Y-m-d');
	$His	= date('H:i:s');
	$w		= date('w');
	$G		= date('G');
	
	switch(getVar('task', 'get')) {
		
		case 'createEventsToday':
			
			$event_weekly = loadObjectList("SELECT * FROM `event_weekly` WHERE `day` = {$w} AND `rid` != 10 AND `rid` != 11");
			
			if (cnt($event_weekly)) {
				
				$sync = false;
				foreach ($event_weekly as $event) {
					
					// Исключение этой даты
					$nodates = explode(",", $event->nodates);
					if(in_array($Ymd, $nodates)) continue;
					
					// Исключение если есть событие
					if ($have = loadResult("SELECT * FROM `events` WHERE `rid` = {$event->rid} AND `date` = '{$Ymd}' AND `start` = {$event->start}")) continue;
					
					// Создание события
					$fields = array();
					
					foreach ($event as $name => $value) {
								
						if (in_array($name, array('id', 'day', 'nodates', 'state'))) continue;
						
						$fields[$name] = $value;
					}
						
					// $sql = "INSERT INTO `events` (`date`, `state`";
					$sql = "INSERT INTO `events` (`date`";
					foreach ($fields as $name => $value) { $sql .= ", `{$name}`"; }
					// $sql .= ") VALUES ('{$Ymd}', 2";
					$sql .= ") VALUES ('{$Ymd}'";
					foreach ($fields as $name => $value) { $sql .= ", '".dbEscape($value)."'"; }
					$sql .= ")";
					
					runSql($sql); $id = insertId();
					
					// Журнал
					$event_new = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					$date = date('d.m.Y', strtotime($event_new->date));
					$log = "Добавление события из постоянки. ID:{$id}. {$event_new->room}. {$date}, {$event_new->start}:00 - {$event_new->end}:00. Клиент: {$event_new->client} [id:{$event_new->cid}]";
					addLog($log);
					
					$sync = true;
				}
				
				// if ($sync) { toSync(1); }
			}
			
		break;
		
		case 'stateEvents':
			
			// Обновить прошедшие события
			runSql("UPDATE `events` SET `state` = 2 WHERE `state` < 2 AND `date` < '{$Ymd}' AND `start` != 23");
			
			// Прошедшие ночные события
			$night_events = loadObjectList("SELECT `id`, `date`, `start`, `end`, (SELECT `min` FROM `rooms` WHERE `id` = `rid`) AS `min`  FROM `events` WHERE `date` < '{$Ymd}' AND `start` = 23 AND `state` < 2");
			
			if (cnt($night_events)) {
				
				foreach ($night_events as $event) {
					
					$min = str_pad($event->min, 2, '0', STR_PAD_LEFT); 
					$end_time = strtotime("{$event->date} {$event->start}:{$min} + 7 hours");
					if ($end_time < time()){
						
						runSql("UPDATE `events` SET `state` = 2 WHERE `id` = {$event->id}");
					}
				}
			}
			
			// Начавшиеся и прошедшие сегодняшние события
			$events = loadObjectList("SELECT *, (SELECT `min` FROM `rooms` WHERE `id` = `rid`) AS `min`  FROM `events` WHERE `date` = '{$Ymd}' AND `start` <= {$G} AND `state` < 2");
			
			if (cnt($events)) {
				
				foreach($events as $event) {
					
					$min = str_pad($event->min, 2, '0', STR_PAD_LEFT); 
					
					$start_time = strtotime("{$event->date} {$event->start}:{$min}");
					$end_time = strtotime("{$event->date} {$event->end}:{$min}");
					
					// $state = ($start_time <= time() and $end_time > time()) ? 1 : 2;
					$state = 0;
					if ($start_time <= time() and $end_time <= time()) { $state = 2; }
					if ($start_time <= time() and $end_time > time()) { $state = 1; }
					
					runSql("UPDATE `events` SET `state` = {$state} WHERE `id` = {$event->id}");
					
					// echo "{$event->start}:{$min} - {$event->end}:{$min} | {$state}<br/>";
				}
				
				// toSync(1);
			}
			
		break;
		
	}
	
?>