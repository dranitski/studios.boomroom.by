<?php
	
	defined('access') or die ('no access');
	
?>

<?php include "menu.php"; ?>

<div id="tablehead">
	<table class="table rooms">
		<tr>
			<td></td>
			<?php 	foreach ($rooms as $room) { ?>
			<td><b><a href="<?php echo admin; ?>/table?rid=<?php echo $room->id; ?>&date=<?php echo $date; ?>"><?php echo $room->name; ?></a></b></td>
			<?php } ?>
		</tr>
	</table>
	<a class="left" href="<?php echo admin; ?>/tablerooms?date=<?php echo date('d.m.Y', strtotime("{$date} - 1 day")); ?>"></a>
	<a class="right" href="<?php echo admin; ?>/tablerooms?date=<?php echo date('d.m.Y', strtotime("{$date} + 1 day")); ?>"></a>
</div>

<div id="timetable">

	<?php
	
		$date = $sqlDate;
		
		$items = array();
		
		// Цикл комнат
		foreach ($rooms as $room) {
		
			$room->min = ($room->min == 0) ? '00' : $room->min;
		
			// Цикл часов
			for ($h = 9; $h != 24; $h++) {
			
				$content = "";
				
				// Шаблоны
				foreach ($templates as $template) {
					
					$start = $template->start;
					$end = $template->end;
					
					if ($template->rid == $room->id and $start == $h) {
					
						// if (strtotime($template->date) > strtotime($date)) continue;
						
						$dur = ($start < 23) ? $end - $start : 1;
						
						$onclick = (strtotime("{$date} {$start}:{$room->min}") > time()) ? "newEvent({$template->id}, '{$date}');" : "";
						$class = (strtotime("{$date} {$start}:{$room->min}") > time()) ? "item" : "item past";
						
						$content = "<div class=\"{$class}\" data-dur=\"{$dur}\" onclick=\"{$onclick}\">";
						$content .= "<div class=\"time\">{$start}:{$room->min} - {$end}:{$room->min}</div>";
						$content .= "</div>";
					}
				}
				
				// Постоянки
				if (strtotime("{$date} {$start}:{$room->min}") > time()) {
				foreach ($event_weekly as $event) {
					
					$start = $event->start;
					$end = $event->end;
					
					$nodates = explode(",", $event->nodates);
					
					if ($event->rid == $room->id and $start == $h and in_array($date, $nodates) == false) {
						
						$client = ($event->cid == 0) ? "Неопределенный" : loadResult("SELECT `name` FROM `clients` WHERE `id` = {$event->cid}");
						
						$debt = 0; $card = null;
						if ($event->cid > 0) {
							
							$debt = round(loadResult("SELECT SUM(`price` - `pay`) FROM `events` WHERE (`price` - `pay`) > 0 AND `cid` = {$event->cid} AND `state` > 1 AND `date` >= '2017-07-01'"), 2);
							
							if ($debt > 0) {
								
								$paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` IN (SELECT `id` FROM `events` WHERE `cid` = {$event->cid})");
								$debt -= $paid;
							}
							
							$count_events = loadResult("SELECT COUNT(*) FROM `events` WHERE `cid` = {$event->cid} AND `state` = 2");
							$level = getClientLevel($count_events);
							$card = loadResult("SELECT `card` FROM `clients` WHERE `id` = {$event->cid}");
							if ($card) { $client .= " [{$card}/{$level}]"; }
						}
						
						$dur = ($start < 23) ? $end - $start : 1;
						
						$class = (strtotime("{$date} {$start}:{$room->min}") > time()) ? "item weekly" : "item weekly past";
						
						$content = "<div class=\"{$class}\" data-dur=\"{$dur}\" onclick=\"getWeekly({$event->id}, '{$date}');\">";
						$content .= "<label class=\"price\">{$event->price}</label>";
						// $content .= "<label>Постоянка</label>";
						$content .= ($debt > 0) ? "<label class=\"debt\">Долг: <b>{$debt}</b></label>" : "";
						$content .= "<div class=\"time\">{$start}:{$room->min} - {$end}:{$room->min}</div>";
						$content .= "<div class=\"client\">{$client}</div>";
						$content .= "</div>";
					}
				}
				}
				
				// События
				foreach ($events as $event) {
					
					$start = $event->start;
					$end = $event->end;
					
					if ($event->rid == $room->id and $start == $h) {
						
						$dur = ($start < 23) ? $end - $start : 1;
					
						$client = ($event->cid == 0) ? "Неопределенный" : loadResult("SELECT `name` FROM `clients` WHERE `id` = {$event->cid}");
						
						$debt = 0; $event_paid = 0; $card = null;
						if ($event->cid > 0) {
							
							$debt = round(loadResult("SELECT SUM(`price` - `pay`) FROM `events` WHERE (`price` - `pay`) > 0 AND `cid` = {$event->cid} AND `state` > 1 AND `date` >= '2017-07-01'"), 2);
							
							if ($debt > 0) {
								
								$paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` IN (SELECT `id` FROM `events` WHERE `cid` = {$event->cid})");
								$debt -= $paid;
								
								$event_paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` = {$event->id}");
							}
							
							$count_events = loadResult("SELECT COUNT(*) FROM `events` WHERE `cid` = {$event->cid} AND `state` = 2");
							$level = getClientLevel($count_events);
							$card = loadResult("SELECT `card` FROM `clients` WHERE `id` = {$event->cid}");
							if ($card) { $client .= " [{$card}/{$level}]"; }
						}
						
						// Ночная репетиция
						$event_date = ($start == 23) ? date('Y-m-d', strtotime("{$date} + 1 day")) : $date;
						$class = (strtotime("{$event_date} {$end}:{$room->min}") > time()) ? "item have" : "item have past";
						
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
						$content .= "<div class=\"time\">{$start}:{$room->min} - {$end}:{$room->min}</div>";
						$content .= "<div class=\"client\">{$client}</div>";
						$content .= "</div>";
					}
				}
				
				$items[$h.$room->id] = $content;
			}
		}
		
	?>

	<table class="table rooms">
	<?php
		
		for ($h = 9; $h != 24; $h++) {
		
		?>
		<tr>
			<td><div class="h"><?php echo $h; ?>:00</div></td>
			<?php foreach ($rooms as $room) { ?><td><?php echo $items[$h.$room->id]; ?></td><?php } ?>
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