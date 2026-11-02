<?php
	
	defined('access') or die ('no access');
	
	// Часы события: ночное (с 23) - 23..5, иначе от start до end, с переходом через полночь (22 -> 6)
	function eventHours($start, $end) {
		
		$start = (int) $start; $end = (int) $end;
		
		if ($start >= 23) {
			return array(23, 0, 1, 2, 3, 4, 5);
		}
		
		$hours = array();
		for ($h = $start, $n = 0; $h % 24 != $end % 24 and $n < 24; $h++, $n++) {
			$hours[] = $h % 24;
		}
		
		return $hours;
	}
	
	// Массив свободных количества свободных инструментов
	function getFreeEqs($equipments, $sid, $date, $start, $end, $weekly = false) {
	
		$start = (int) $start; $end = (int) $end;
		if ($start < 0 or $start > 23 or $end < 0 or $end > 24) {
			return array();
		}
	
		if ($weekly == false) {
			
			$events = loadObjectList("SELECT * FROM `events` WHERE `date` = '{$date}' AND `rid` IN (SELECT `id` FROM `rooms` WHERE `sid` = {$sid} ORDER BY `start` ASC)");
		}
		else {
			
			$events = loadObjectList("SELECT * FROM `event_weekly` WHERE `day` = {$date} AND `rid` IN (SELECT `id` FROM `rooms` WHERE `sid` = {$sid} ORDER BY `start` ASC) AND `state` = 1");
		}
		
		// Забронированное оборудование по часам
		$eqs_rent = array();
		foreach ($events as $event) {
			
			// Массив количеств инструментов в этом событии
			$eqs = json_decode($event->eqs);
			$event_eqs = array();
			if (cnt($eqs)) { foreach ($eqs as $eq) { $event_eqs[$eq->id] = $eq->count; } }
			
			// Дневная
			if ($event->start < 23) {
				
				foreach (eventHours($event->start, $event->end) as $h) {
					
					foreach ($event_eqs as $id => $count) { $eqs_rent[$h][$id] += $count; }
				}
			}
			// Ночная
			else {
				
				foreach ($event_eqs as $id => $count) { $eqs_rent[23][$id] += $count; }
				
				for ($h = 0; $h != 6; $h++) { foreach ($event_eqs as $id => $count) { $eqs_rent[$h][$id] += $count; } }
			}
		}
		
		ksort($eqs_rent);
		
		// Считаем свободное по часам
		$eqs_free_all = array();
		if ($start < 23) {
			
			foreach (eventHours($start, $end) as $h) {
				
				foreach($equipments as $eq) {
				
					$ost = $eq->count - $eqs_rent[$h][$eq->id];
					
					$eqs_free_all[$eq->id][] = $ost;
				}
			}
		}
		else {
			
			foreach($equipments as $eq) { $ost = $eq->count - $eqs_rent[23][$eq->id]; $eqs_free_all[$eq->id][] = $ost; }
				
			for ($h = 0; $h != 6; $h++) { foreach($equipments as $eq) { $ost = $eq->count - $eqs_rent[$h][$eq->id]; $eqs_free_all[$eq->id][] = $ost; } }
		}
		
		// Записываем минимальное свободное количество
		$eqs_free = array();
		foreach($eqs_free_all as $id => $array) {
			
			$eqs_free[$id] = min($array);
		}
		
		return $eqs_free;
	}
	
	function isHaveWeekly($event) {
	
		$day = date('w', strtotime($event->date));
		
		$hours = eventHours($event->start, $event->end);
		if (!$hours) return false;
		
		$WHERE = "(".implode(' BETWEEN `start` AND (`end` -1) OR ', $hours)." BETWEEN `start` AND (`end` -1))";
		
		return (loadResult("SELECT `id` FROM `event_weekly` WHERE `state` = 1 AND `rid` = {$event->rid} AND `day` = {$day} AND {$WHERE}")) ? true : false;
	}
?>