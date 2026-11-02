<?php
	
	defined('access') or die ('no access');
		
?>
<div id="repaby">
	<div class="head">
		<table>
			<tr>
				<td class="min"></td>
				<?php
				
				for ($d = 0; $d < $days; $d++) {
					
					$date_this = date('d.m', strtotime("{$date_start} + {$d} days"));
					$txtDn = getDayWeek(date('w', strtotime("{$date_start} + {$d} days")));
					
					$holiday = false;
					if (in_array(date('d.m.Y', strtotime("{$date_start} + {$d} days")), explode(",", $params->holidays))) { $holiday = true; }
					
				?>
				<td <?php echo ($holiday) ? "class=\"holiday\"": ""; ?>><b><?php echo $txtDn; ?></b> <?php echo $date_this; ?></td>
				<?php } ?>
			</tr>
		</table>
	</div>
	<?php if (strtotime("{$date_start} 23:59 - 7 days") > time()) { ?>
	<a class="left" href="/repaby?sid=<?php echo $sid; ?>&rid=<?php echo $rid; ?>&date=<?php echo date('d.m.Y', strtotime("{$date_start} - 7 days")); ?>#repaby"></a>
<?php } ?>
	<a class="right" href="/repaby?sid=<?php echo $sid; ?>&rid=<?php echo $rid; ?>&date=<?php echo date('d.m.Y', strtotime("{$date_start} + 7 days")); ?>#repaby"></a>

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
					
					if (strtotime($date) >= strtotime('17.07.2017') and $sid == 4) {
						
						$content = "<div class=\"item past\" data-dur=\"{$dur}\">";
						$content .= "<div class=\"time\"></div>";
						$content .= "</div>";
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
			<tr/>
			<?php
				
			}
		?>
		</table>

		<div id="iw">
			<div id="event"></div>
		</div>

	</div>
</div>