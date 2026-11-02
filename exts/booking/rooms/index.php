<?php
	
	defined('access') or die ('no access');
	
?>
<div id="booking">
	<!--<div class="title"><h1>Онлайн-бронирование студии</h1></div>-->
	
	<?php include "menu.php"; ?>
	
	<div class="head">
		<table>
			<tr class="studios">
				<td class="min"></td>
				<td colspan="4"><a href="#">Студия BooM</a></td>
				<td colspan="2"><a href="#">Студия Jam</a></td>
				<td colspan="2"><a href="#">Студия Точка</a></td>
				<td class="min"></td>
			</tr>
			<tr>
				<td class="min">
				<?php if (strtotime("{$date}") > time()) { ?>
					<a class="left" href="/booking-rooms?date=<?php echo date('d.m.Y', strtotime("{$date} - 1 day")); ?>#booking"></a>
				<?php } ?>
				</td>
				<?php 	foreach ($rooms as $room) { ?>
				<td><b><?php echo $room->name; ?></b></td>
				<?php } ?>
				<td class="min">
					<a class="right" href="/booking-rooms?date=<?php echo date('d.m.Y', strtotime("{$date} + 1 day")); ?>#booking"></a>
				</td>
			</tr>
		</table>
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
						
						$content = "<div class=\"{$class}\" data-dur=\"{$dur}\" onclick=\"{$onclick}\" data-tid=\"{$template->id}\">";
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
						
						$dur = ($start < 23) ? $end - $start : 1;
						
						$class = (strtotime("{$date} {$start}:{$room->min}") > time()) ? "item weekly" : "item weekly past";
						
						$content = "<div class=\"item blocked\" data-dur=\"{$dur}\">";
						$content .= "<div class=\"time\">{$start}:{$room->min} - {$end}:{$room->min}</div>";
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
					
						$class = (strtotime("{$date} {$end}:{$room->min}") > time()) ? "item have" : "item have past";
						
						$content = "<div class=\"item blocked\" data-dur=\"{$dur}\">";
						$content .= "<div class=\"time\">{$start}:{$room->min} - {$end}:{$room->min}</div>";
						$content .= "</div>";
					}
				}
					
				if (strtotime($date) >= strtotime('17.07.2017') and ($room->id == 10 or $room->id == 11)) {
					
					$content = "<div class=\"item past\" data-dur=\"{$dur}\">";
					$content .= "<div class=\"time\"></div>";
					$content .= "</div>";
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
			<td class="min"><div class="h"><?php echo $h; ?>:00</div></td>
			<?php foreach ($rooms as $room) { ?><td><?php echo $items[$h.$room->id]; ?></td><?php } ?>
			<td class="min"></td>
		</tr>
		<?php
			
		}
	?>
		
	</table>

		<div id="iw" data-w="550" data-h="600">
			<div>
				<div id="event">
				</div>
			</div>
		</div>

	</div>
</div>