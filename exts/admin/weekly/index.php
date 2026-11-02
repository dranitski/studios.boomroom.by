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
				
				$txtDn = getDayWeek(date('w', strtotime("{$date_start} + {$d} days")), true);
				
				$w = date('w', strtotime("{$date_start} + {$d} days"));
				$holiday = false;
				if ($w == 0 or $w == 6) { $holiday = true; }
			?>
			<td <?php echo ($holiday) ? "class=\"holiday\"": ""; ?>><b><?php echo $txtDn; ?></b></td>
			<?php } ?>
		</tr>
	</table>
</div>

<div id="timetable">

	<?php
		
		$items = array();
		
		// Цикл дней
		for ($d = 0; $d < $days; $d++) {
		
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
					
					if ($template->day == $day and $template->date == '0000-00-00' and $start == $h) {
						
						$dur = ($start < 23) ? $end - $start : 1;
						
						$content = "<div class=\"item\" data-dur=\"{$dur}\" onclick=\"newWeekly({$template->id});\">";
						$content .= "<div class=\"time\">{$start}:{$min} - {$end}:{$min}</div>";
						$content .= "</div>";
					}
				}
				
				// События
				foreach ($events as $event) {
					
					$start = $event->start;
					$end = $event->end;
					
					$client = ($event->cid == 0) ? "Неопределенный" : clientField($event->cid, 'name');
					
					if ($event->day == $day and $start == $h) {
						
						$dur = ($start < 23) ? $end - $start : 1;
						
						$content = "<div class=\"item have\" data-dur=\"{$dur}\" onclick=\"getWeekly({$event->id});\">";
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
		
		for ($h =9; $h != 24; $h++) {
		
		?>
		<tr>
			<td><div class="h"><?php echo $h; ?>:<?php echo $min; ?></div></td>
			<?php for ($d = 0; $d < $days; $d++) {
			
				$w = date('w', strtotime("{$date_start} + {$d} days"));
				$holiday = false;
				if ($w == 0 or $w == 6) { $holiday = true; }
			
			?><td <?php echo ($holiday) ? "class=\"holiday\"": ""; ?>><?php echo $items[$h.$d]; ?></td><?php } ?>
		</tr>
		<?php
			
		}
	?>
		
	</table>

	<div id="iw" data-w="500" data-h="600">
		<div>
			<div id="event">
			</div>
		</div>
	</div>

</div>