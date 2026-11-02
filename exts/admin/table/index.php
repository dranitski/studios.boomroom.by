<?php
	
	defined('access') or die ('no access');
	
?>

<?php include "menu.php"; ?>

<div id="tablehead">
	<table class="table">
		<tr>
			<td></td>
			<?php
			
			for ($d = 0; $d < $days; $d++) {
				
				$date_this = date('d.m', strtotime("{$date_start} + {$d} days"));
				$txtDn = getDayWeek(date('w', strtotime("{$date_start} + {$d} days")));
				
				$holiday = false;
				if (in_array(date('d.m.Y', strtotime("{$date_start} + {$d} days")), explode(",", $params->holidays))) { $holiday = true; }
			?>
			<td <?php echo ($holiday) ? "class=\"holiday\"": ""; ?>><a href="<?php echo admin; ?>/tablerooms?date=<?php echo date('d.m.Y', strtotime("{$date_start} + {$d} days")); ?>"><b><?php echo $txtDn; ?></b> <?php echo $date_this; ?></a></td>
			<?php } ?>
		</tr>
	</table>
	<a class="left" href="<?php echo admin; ?>/table?rid=<?php echo $rid; ?>&date=<?php echo date('d.m.Y', strtotime("{$date_start} - 7 days")); ?>"></a>
	<a class="right" href="<?php echo admin; ?>/table?rid=<?php echo $rid; ?>&date=<?php echo date('d.m.Y', strtotime("{$date_start} + 7 days")); ?>"></a>
</div>

<div id="timetable">

	<?php
		
		$items = array();
		
		// Цикл дней
		for ($d = 0; $d < $days; $d++) {
		
			$date		= date('Y-m-d', strtotime("{$date_start} + {$d} days"));
			$txtDate	= date('d.m.Y', strtotime("{$date_start} + {$d} days"));
			$day		= date('w', strtotime("{$date_start} + {$d} days"));
			$txtDn		= getDayWeek($day);
			
			// Цикл часов
			for ($h = 9; $h != 24; $h++) {
			
				$content = "";
				
				// Шаблоны
				foreach ($templates as $template) {
					
					$start = $template->start;
					$end = $template->end;
					
					if ($template->day == $day and $start == $h) {
					
						if (strtotime($template->date) > strtotime($date)) continue;
						
						$dur = ($start < 23) ? $end - $start : 1;
						
						$onclick = (strtotime("{$date} {$start}:{$min}") > time()) ? "newEvent({$template->id}, '{$date}');" : "";
						$class = (strtotime("{$date} {$start}:{$min}") > time()) ? "item" : "item past";
						
						/* ВРЕМЕННО */
						// $onclick = "newEvent({$template->id}, '{$date}');";
						// $class = (strtotime("{$date} {$start}:{$min}") > time()) ? "item" : "item past";
						/* ВРЕМЕННО */
						
						$content = "<div class=\"{$class}\" data-dur=\"{$dur}\" onclick=\"{$onclick}\">";
						$content .= "<div class=\"time\">{$start}:{$min} - {$end}:{$min}</div>";
						$content .= "</div>";
					}
				}
				
				// Постоянки
				if (strtotime("{$date} {$start}:{$min}") > time()) {
				foreach ($event_weekly as $event) {
					
					$start = $event->start;
					$end = $event->end;
					
					$nodates = explode(",", $event->nodates);
					
					if ($event->day == $day and $start == $h and in_array($date, $nodates) == false) {
						
						$client = ($event->cid == 0) ? "Неопределенный" : clientField($event->cid, 'name');
						
						$debt = 0; $card = null;
						if ($event->cid > 0) {
							
							$debt = round(clientDebt($event->cid), 2);
							
							if ($debt > 0) {
								
								$paid = clientPaid($event->cid);
								$debt -= $paid;
							}
							
							$count_events = clientEventsCount($event->cid);
							$level = getClientLevel($count_events);
							$card = clientField($event->cid, 'card');
							if ($card) { $client .= " [{$card}/{$level}]"; }
						}
						
						$dur = ($start < 23) ? $end - $start : 1;
						
						$class = (strtotime("{$date} {$start}:{$min}") > time()) ? "item weekly" : "item weekly past";
						
						$content = "<div class=\"{$class}\" data-dur=\"{$dur}\" onclick=\"getWeekly({$event->id}, '{$date}');\">";
						$content .= "<label class=\"price\">{$event->price}</label>";
						// $content .= "<label>Постоянка</label>";
						$content .= ($debt > 0) ? "<label class=\"debt\">Долг: <b>{$debt}</b></label>" : "";
						$content .= "<div class=\"time\">{$start}:{$min} - {$end}:{$min}</div>";
						$content .= "<div class=\"client\">{$client}</div>";
						$content .= "</div>";
					}
				}
				}
				
				// События
				foreach ($events as $event) {
					
					$start = $event->start;
					$end = $event->end;
					
					if ($event->date == $date and $start == $h) {
						
						$dur = ($start < 23) ? $end - $start : 1;
					
						$client = ($event->cid == 0) ? "Неопределенный" : clientField($event->cid, 'name');
						
						$debt = 0; $card = null;
						if ($event->cid > 0) {
							
							$debt = round(clientDebt($event->cid), 2);
							
							if ($debt > 0) {
								
								$paid = clientPaid($event->cid);
								$debt -= $paid;
								
								$event_paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` = {$event->id}");
							}
							
							$count_events = clientEventsCount($event->cid);
							$level = getClientLevel($count_events);
							$card = clientField($event->cid, 'card');
							if ($card) { $client .= " [{$card}/{$level}]"; }
						}
						
						// Ночная репетиция
						$event_date = ($start == 23) ? date('Y-m-d', strtotime("{$date} + 1 day")) : $date;
						$class = (strtotime("{$event_date} {$end}:{$min}") > time()) ? "item have" : "item have past";
						
						$content = "<div class=\"{$class}\" data-dur=\"{$dur}\" onclick=\"getEvent({$event->id});\">";
						$content .= ($event->state == 3) ? "<label>Требует подтверждения</label>" : "";
						$content .= ($event->state == 5) ? "<label class=\"price skipped\"><b>{$event->price}</b> [неявка]</label>" : "<label class=\"price\">{$event->price}</label>";
						// $content .= ($event->state == 1) ? "<label>Сейчас</label>" : "";
						if ($event->state == 2 and $event->pay < $event->price and $event->price <= $event_paid) {
							
							$content .= "<label class=\"pay paid\">Долг погашен</label>";
						}
						else {
							
							$content .= ($event->state == 2 and $event->pay == 0 and $event->price > 0) ? "<label class=\"pay no\" title=\"Неоплачено\">{$event->price}</label>" : "";
							$content .= ($event->state == 2 and $event->pay > 0 and $event->pay < $event->price) ? "<label class=\"pay part\" title=\"Неполная оплата\">{$event->pay}</label>" : "";
						}
						$content .= ($debt > 0) ? "<label class=\"debt\">Долг: <b>{$debt}</b></label>" : "";
						$content .= "<div class=\"time\">{$start}:{$min} - {$end}:{$min}</div>";
						$content .= "<div class=\"client\">{$client}</div>";
						$content .= "</div>";
					}
				}
				
				$items[$h.$d] = $content;
			}
		}
		
	?>

	<table class="table">
	<?php
		
		for ($h = 9; $h != 24; $h++) {
		
		?>
		<tr>
			<td><div class="h"><?php echo $h; ?>:<?php echo $min; ?></div></td>
			<?php for ($d = 0; $d < $days; $d++) {
				
				$date_this = date('d.m', strtotime("{$date_start} + {$d} days"));
				$txtDn = getDayWeek(date('w', strtotime("{$date_start} + {$d} days")));
				
				$holiday = false;
				if (in_array(date('d.m.Y', strtotime("{$date_start} + {$d} days")), explode(",", $params->holidays))) { $holiday = true; }
				
			?><td <?php echo ($holiday) ? "class=\"holiday\"": ""; ?>><?php echo $items[$h.$d]; ?></td><?php } ?>
		</tr>
		<?php
			
		}
	?>
		
	</table>

	<div id="iw" data-w="580" data-h="600">
		<div>
			<div id="event">
			</div>
		</div>
	</div>

</div>
<?php if ($eid = getInt('eid', 'get')) { ?>
<script>
	getEvent(<?php echo $eid; ?>);
</script>
<?php } ?>