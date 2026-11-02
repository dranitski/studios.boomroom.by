<?php
	
	defined('access') or die ('no access');
	
	function getDayWeek($num, $full = 0) {
		
		if ($full) {
			switch ($num) {
			
				case 1:
					$v = 'Понедельник'; break;
				case 2:
					$v = 'Вторник'; break;
				case 3:
					$v = 'Среда'; break;
				case 4:
					$v = 'Четверг'; break;
				case 5:
					$v = 'Пятница'; break;
				case 6:
					$v = 'Суббота'; break;
				case 0: case 7:
					$v = 'Воскресенье'; break;
			}
		}
		else {
			switch ($num) {
			
				case 1:
					$v = 'Пн'; break;
				case 2:
					$v = 'Вт'; break;
				case 3:
					$v = 'Ср'; break;
				case 4:
					$v = 'Чт'; break;
				case 5:
					$v = 'Пт'; break;
				case 6:
					$v = 'Сб'; break;
				case 0: case 7:
					$v = 'Вс'; break;
			}
		}
		
		return $v;
	}
	
	function getMonth($num, $end = 1, $upper = 0) {
		
		switch ($num) {
			case 1:
				$first = "я"; $root = "нвар"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
			case 2:
				$first = "ф"; $root = "еврал"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
			case 3:
				$first = "м"; $root = "арт"; $end_1 = ""; $end_2 = "а"; $end_3 = "е"; break;
			case 4:
				$first = "а"; $root = "прел"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
			case 5:
				$first = "м"; $root = "а"; $end_1 = "й"; $end_2 = "я"; $end_3 = "е"; break;
			case 6:
				$first = "и"; $root = "юн"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
			case 7:
				$first = "и"; $root = "юл"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
			case 8:
				$first = "а"; $root = "вгуст"; $end_1 = ""; $end_2 = "а"; $end_3 = "е"; break;
			case 9:
				$first = "с"; $root = "ентябр"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
			case 10:
				$first = "о"; $root = "ктябр"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
			case 11:
				$first = "н"; $root = "оябр"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
			case 12:
				$first = "д"; $root = "екабр"; $end_1 = "ь"; $end_2 = "я"; $end_3 = "е"; break;
		}
		
		if ($end == 1) { $end = $end_1; } elseif ($end == 2) { $end = $end_2; } elseif ($end == 3) { $end = $end_3; } else { $end = $end_1; }
		if ($upper) { $first = mb_strtoupper($first, 'UTF-8'); }
		
		$name = $first.$root.$end;
		
		return $name;
	}
	
	function getTimesList($start = 5, $end = 24, $tag = 'option', $selected = null, $minut = '00') {
		
		$list = array();
		
		if ($tag == 'option') {
			
			for ($start; $start <= $end; $start++) {
				
				$selectedText = null;
				
				if ($start == $selected) {
					$selectedText = " selected";
				}
				
				if ($start == 24) {
					$value	= "00:{$minut}";
				}
				else {
					$value	= "{$start}:{$minut}";
				}
				
				$list[] = "<{$tag} value=\"{$start}\"{$selectedText}>{$value}</{$tag}>";
				
			}
			
		}
		else {
			
			for ($start; $start <= $end; $start++) {
				
				$list[] = "<{$tag}>{$start}:00</{$tag}>";
				
			}
			
		}
		
		$html = implode('', $list);
		
		return $html;
		
	}
	
?>