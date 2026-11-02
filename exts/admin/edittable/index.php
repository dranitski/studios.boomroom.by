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
			<td <?php echo ($holiday) ? "class=\"holiday\"": ""; ?>><b><?php echo $txtDn; ?></b> <?php echo $date_this; ?></td>
			<?php } ?>
		</tr>
	</table>
	<a class="left" href="<?php echo admin; ?>/edittable?rid=<?php echo $rid; ?>&date=<?php echo date('d.m.Y', strtotime("{$date_start} - 7 days")); ?>"></a>
	<a class="right" href="<?php echo admin; ?>/edittable?rid=<?php echo $rid; ?>&date=<?php echo date('d.m.Y', strtotime("{$date_start} + 7 days")); ?>"></a>
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
			
				$content = "<div class=\"item set\" onclick=\"getTemplate(0, {$rid}, '{$date}', {$h});\">";
				$content .= "<div class=\"time\">{$txtDn}, {$h}:{$min}</div>";
				$content .= "</div>";
				
				foreach ($templates as $template) {
					
					$start = $template->start;
					$end = $template->end;
			
					$class = ($template->date == $date) ? "item this" : "item";
					
					if ($template->day == $day and $start == $h) {
					
						if (strtotime($template->date) > strtotime($date)) continue;
						
						$dur = ($start < 23) ? $end - $start : 1;
						
						$content = "<div class=\"{$class}\" data-dur=\"{$dur}\" onclick=\"getTemplate({$template->id}, {$rid}, '{$date}', {$h});\">";
						$content .= "<div class=\"time\">{$start}:{$min} - {$end}:{$min}</div>";
						$content .= ($template->date == $date) ? "<div class=\"date\">{$txtDate}</div>" : "";
						$content .= "</div>";
					}
				}
				
				$items[$h.$d] = $content;
			}
		}
		
	?>

	<table class="table edit">
	<?php
		
		for ($h =9; $h != 24; $h++) {
		
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

	<div id="iw" data-w="500" data-h="400">
		<div>
			<div id="template">
			</div>
		</div>
	</div>

</div>