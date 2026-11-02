<?php
	
	defined('access') or die ('no access');
	
	function getLabelsCountsList($array, $date_start, $date_end, $group, $return) {
		
		$labels = array();
		$counts = array();
		
		// цикл ниже идет по дням до $date_end: при обратном или некорректном периоде он бесконечен
		if (!strtotime($date_start) or !strtotime($date_end) or strtotime($date_start) > strtotime($date_end)) {
			return array();
		}
		
		while ($date_this != $date_end) {
			
			$date_this = ($date_this) ? date('d.m.Y', strtotime("{$date_this} + 1 day")) : $date_start;
			
			$t = strtotime($date_this);
			
			// Количесиво
			$count = (int) $array[$date_this];
			
			// Под дням
			$day = date('d.m', $t);
			$labels_day[] = $day;
			$counts_day[] = $count;
			
			// По неделям
			if (date('w', $t) == 1) {
				
				$wcounts = $count;
			}
			else {
				
				$wcounts += $count;
			}
			
			if (date('w', $t) == 1 or $date_start == $date_this) {
				
				$week = date('d.m', $t);
			}
			
			if (date('w', $t) == 0 or $date_end == $date_this) {
				
				$week .= "-".date('d.m', $t);
				
				$labels_week[] =  $week;
				$counts_week[] = $wcounts;
			}
			
			// По месяцам
			if (date('j', $t) == 1) {
				
				$mcounts = $count;
			}
			else {
				
				$mcounts += $count;
			}
			
			if (date('j', $t) == 1 or $date_start == $date_this) {
				
				$month = date('d.m', $t);
			}
			
			if (date('n', $t) != date('n', $t+86400) or $date_end == $date_this) {
				
				$month .= "-".date('d.m', $t);
				
				$labels_month[] = $month;
				$counts_month[] = $mcounts;
			}
		}
		
		switch ($group) {
								
			case 1:
				
				$labels = $labels_month;
				$counts = $counts_month;
				
			break;
			
			case 2:
				
				$labels = $labels_week;
				$counts = $counts_week;
				
			break;
			
			case 3:
				
				$labels = $labels_day;
				$counts = $counts_day;
				
			break;
			
		}
		
		return ($return == 1) ? $labels : $counts;
	}
	
	
	function getMonthSums($invoices, $uid = 0) {
	
		$sums = array();
		
		if (cnt($invoices)) {
		
			foreach ($invoices as $n=> $invoice) {
			
				if ($uid and $uid != $invoice->uid) { continue; }
				
				$month = date('n', strtotime($invoice->paid));
				$month_prev = date('n', strtotime($invoices[$n-1]->paid));
				
				if ($month == $month_prev or $n == 0) {
					
					$sum = ($invoice->state == 5) ? $invoice->pp : $invoice->sum;
					
					// Сумма
					$sums[$month] += $sum;
					
					// Коэффициент выполнения плана
					if ($uid and $invoice->state == 4) {
						
						$sum_kr = $sum;
						
						$items = loadObjectList("SELECT * FROM `invoice_items` WHERE `inid` = {$invoice->id}");
						
						if (cnt($items)) { 
							
							$sum_kr = 0;
							
							foreach ($items as $item) {
								
								// Коэффициент рентабельности 
								$kr = 1;
								
								if ($item->dis > 0) {
									
									// $kr = $kr - ceil($item->dis / 5) * 0.05;
									$kr = $kr - ($item->dis / 100);
								}
								
								if ($item->dis < 0) {
									
									// $kr = $kr - floor($item->dis / 5) * 0.05;
									$kr = $kr - ($item->dis / 100);
								}
								
								// Стоимость 1 товара
								$item_sum = round($item->price - ($item->price / 100 * $item->dis)) * $kr * $item->count;
								
								// Сумма всех товаров
								$sum_kr += $item_sum;
							}
						}
						
						$sums_kr[$month] += $sum_kr;
					}
				}
			}
		}
		
		// Объеденяем массивы
		foreach ($sums as $m => $sum) {
			
			if ($uid) {
				
				$all_sums[$m] = (object) array('sum' => $sum, 'sum_kr' => $sums_kr[$m]);
			}
			else {
				
				$all_sums[$m] = (object) array('sum' => $sum);
			}
		}
		
		return $all_sums;
	}
	
?>