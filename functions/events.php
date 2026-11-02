<?php
	
	defined('access') or die ('no access');
	
	// Массив свободных количества свободных инструментов
	function getFreeEqs($equipments, $sid, $date, $start, $end, $weekly = false) {
	
		// часы вне суток или конец раньше начала дают бесконечный цикл ниже
		$start = (int) $start; $end = (int) $end;
		if ($start < 0 or $start > 23 or $end < 0 or $end > 24 or ($start < 23 and $end <= $start)) {
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
				
				while($event->start != $event->end) {
				
					$h = $event->start;
					foreach ($event_eqs as $id => $count) { $eqs_rent[$h][$id] += $count; }
					$event->start++;
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
			
			while($start != $end) {
					
				$h = $start;
				
				foreach($equipments as $eq) {
				
					$ost = $eq->count - $eqs_rent[$h][$eq->id];
					
					$eqs_free_all[$eq->id][] = $ost;
				}
				
				$start++;
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
		
		$hours = array();
		if ($event->start < 23) {
			
			for($h = $event->start; $h != $event->end; $h++) { $hours[] = $h; }
		}
		else {
			
			$hours = array(23, 0, 1, 2, 3, 4, 5);
		}
		
		$WHERE = "(".implode(' BETWEEN `start` AND (`end` -1) OR ', $hours)." BETWEEN `start` AND (`end` -1))";
		
		return (loadResult("SELECT `id` FROM `event_weekly` WHERE `state` = 1 AND `rid` = {$event->rid} AND `day` = {$day} AND {$WHERE}")) ? true : false;
	}
?>