<?php
	
	defined('access') or die ('no access');
		
?>
<div id="booking">
	<div class="title"><h1>Онлайн-бронирование студии</h1></div>
	
	<?php include "menu.php"; ?>
	<?php include "about.php"; ?>
	
	<div class="head">
		<table>
			<tr>
				<td class="min">
				<?php if (strtotime("{$date_start} 23:59 - 7 days") > time()) { ?>
					<a class="left" href="/booking?sid=<?php echo $sid; ?>&rid=<?php echo $rid; ?>&date=<?php echo date('d.m.Y', strtotime("{$date_start} - 7 days")); ?>#booking"></a>
				<?php } ?>
				</td>
				<?php
				
				for ($d = 0; $d < $days; $d++) {
					
					$date_this = date('d.m', strtotime("{$date_start} + {$d} days"));
					$txtDn = getDayWeek(date('w', strtotime("{$date_start} + {$d} days")));
					
					$holiday = false;
					if (in_array(date('d.m.Y', strtotime("{$date_start} + {$d} days")), explode(",", $params->holidays))) { $holiday = true; }
					
				?>
				<td <?php echo ($holiday) ? "class=\"holiday\"": ""; ?>><b><?php echo $txtDn; ?></b> <?php echo $date_this; ?></td>
				<?php } ?>
				<td class="min">
					<a class="right" href="/booking?sid=<?php echo $sid; ?>&rid=<?php echo $rid; ?>&date=<?php echo date('d.m.Y', strtotime("{$date_start} + 7 days")); ?>#booking"></a>
				</td>
			</tr>
		</table>
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
				for ($h =9; $h != 24; $h++) {
				
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
							
							$content = "<div class=\"{$class}\" data-dur=\"{$dur}\" onclick=\"{$onclick}\" data-tid=\"{$template->id}\">";
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
							
							$dur = ($start < 23) ? $end - $start : 1;
							
							$content = "<div class=\"item blocked\" data-dur=\"{$dur}\">";
							$content .= "<div class=\"time\">{$start}:{$min} - {$end}:{$min}</div>";
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
							
							$content = "<div class=\"item blocked\" data-dur=\"{$dur}\">";
							$content .= "<div class=\"time\">{$start}:{$min} - {$end}:{$min}</div>";
							$content .= "</div>";
						}
					}
					
					$items[$h.$d] = $content;
				}
			}
			
		?>

		<table>
		<?php
			
			for ($h =9; $h != 24; $h++) {
			
			?>
			<tr>
				<td class="min"><div class="h"><?php echo $h; ?>:00</div></td>
				<?php for ($d = 0; $d < $days; $d++) {
					
					$holiday = false;
					if (in_array(date('d.m.Y', strtotime("{$date_start} + {$d} days")), explode(",", $params->holidays))) { $holiday = true; }
				
				?><td <?php echo ($holiday) ? "class=\"holiday\"": ""; ?>><?php echo $items[$h.$d]; ?></td><?php } ?>
				<td class="min"></td>
			<tr/>
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