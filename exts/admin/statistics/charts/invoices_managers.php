<?php

	defined('access') or die ('no access');
	
	$id		= getInt('id', 'post');
	$start	= getVar('start', 'post');	$date_start = date('d.m.Y', strtotime($start));
	$end	= getVar('end', 'post');	$date_end = date('d.m.Y', strtotime($end));
	$mid	= getInt('mid', 'post');
	$group	= getInt('group', 'post');
	$ord	= getInt('ord', 'post');
	$sort	= getInt('sort', 'post');
	
	$sql_date_start = date('Y-m-d H:i:s', strtotime($date_start));
	$sql_date_end = date('Y-m-d H:i:s', strtotime("{$date_end} 23:59:59"));
	
	$WHERE = "WHERE `created` BETWEEN '{$sql_date_start}' AND '{$sql_date_end}'";
	$WHERE .= " AND `id` NOT IN (SELECT `iid` FROM `invoice_canceled` WHERE `iid` = `id` AND `type` = 0)";
	
	// Все счета
	$invoices = loadObjectList("SELECT *, (SELECT `type` FROM `invoice_canceled` WHERE `iid` = `id`) AS `no` FROM `invoices` {$WHERE} ORDER BY `created` ASC");
	
	// Список менеджеров
	$managers = loadObjectList("SELECT * FROM `users` WHERE `type` = 0");
	
	foreach ($managers as $manager) {
	
		$invoices_dates[$manager->id] = array();
		
		// Количества по статусам
		foreach ($invoices as $invoice) {
			
			if ($invoice->uid == $manager->id) {
				
				$date = date('d.m.Y', strtotime($invoice->created));
				$invoices_dates[$manager->id][] = $date;
				
				$invoice->state = ($invoice->no == 1) ? 6 : $invoice->state;
		
				switch ($invoice->state) {
					
					case 0: $invoices_state_0[$manager->id]++; break;
					case 1: $invoices_state_1[$manager->id]++; break;
					case 2: $invoices_state_2[$manager->id]++; break;
					case 3: $invoices_state_3[$manager->id]++; break;
					case 4: $invoices_state_4[$manager->id]++; break;
					case 5: $invoices_state_5[$manager->id]++; break;
					case 6: $invoices_state_6[$manager->id]++; break;
				}
			}
		}
		
		$invoices_dates[$manager->id] = array_count_values($invoices_dates[$manager->id]);
		
		$labels[$manager->id]	= getLabelsCountsList($invoices_dates[$manager->id], $date_start, $date_end, $group, 1);
		$counts[$manager->id]	= getLabelsCountsList($invoices_dates[$manager->id], $date_start, $date_end, $group, 2);
		
		$invoices_state_0[$manager->id] = ($invoices_state_0[$manager->id]) ? $invoices_state_0[$manager->id] : 0;
		$invoices_state_1[$manager->id] = ($invoices_state_1[$manager->id]) ? $invoices_state_1[$manager->id] : 0;
		$invoices_state_2[$manager->id] = ($invoices_state_2[$manager->id]) ? $invoices_state_2[$manager->id] : 0;
		$invoices_state_3[$manager->id] = ($invoices_state_3[$manager->id]) ? $invoices_state_3[$manager->id] : 0;
		$invoices_state_4[$manager->id] = ($invoices_state_4[$manager->id]) ? $invoices_state_4[$manager->id] : 0;
		$invoices_state_5[$manager->id] = ($invoices_state_5[$manager->id]) ? $invoices_state_5[$manager->id] : 0;
		$invoices_state_6[$manager->id] = ($invoices_state_6[$manager->id]) ? $invoices_state_6[$manager->id] : 0;
	}			
	
	?>
	<div class="chart" style="height: 200px;"></div>
	
	<h3>Доли менеджеров</h3>
	<div class="parts">
		<div class="pie" style="width: 50%;"></div>
		<div class="info" style="width: 50%;">
			
		<?php
		
			foreach ($managers as $n => $manager) {
			
			switch ($n) { case 0: $char = 'a'; break; case 1: $char = 'b'; break; case 2: $char = 'c'; break; case 3: $char = 'd'; break; case 4: $char = 'e'; break; }
		?>
			<div class="part">
				<div class="label pl-<?php echo $char; ?>"></div>
				<div class="name"><?php echo $manager->name; ?></div>
				<div class="value"><?php echo array_sum($counts[$manager->id]); ?></div>
				<div class="pc"><?php echo round(array_sum($counts[$manager->id]) / count($invoices) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
		<?php } ?>
			
			<div class="part">
				<div class="label"></div>
				<div class="name">Всего</div>
				<div class="value"><?php echo count($invoices); ?></div>
				<div class="pc">100%</div>
				<div class="clear"></div>
			</div>
			
		</div>
	</div>
	
	<?php
	
	$labels_list = $labels[$managers[0]->id];
	
	switch ($ord) {
		
		case 1:
		
			if ($sort == 2) { krsort($labels_list); }
	
		break;
	}
	
	?>
	
	<div class="list">
		<div class="filter">
			<div class="w50">№</div>
			<div class="w200"><a href="javascript:void(0);" onclick="setOrd(<?php echo $id; ?>, 1);">Период</a></div>
			<?php foreach ($managers as $manager) { ?>
			<div class="w200"><?php echo $manager->name; ?></div>
			<?php } ?>
			<p class="clear"></p>
		</div>
	<?php foreach ($labels_list as $n => $label) { ?>
		<div class="item">
			<div class="num"><?php echo $n+1; ?></div>
			<div class="period"><?php echo $label; ?></div>
			<?php foreach ($managers as $manager) { ?>
			<div class="count w200"><?php echo $counts[$manager->id][$n]; ?></div>
			<?php } ?>
			<div class="clear"></div>
		</div>
	<?php } ?>	
		<div class="result">
			<div class="label">Всего</div>
			<div class="date">с <?php echo $date_start; ?> по <?php echo $date_end; ?></div>
			<?php foreach ($managers as $manager) { ?>
			<div class="sum w200"><?php echo array_sum($counts[$manager->id]); ?></div>
			<?php } ?>
			<div class="clear"></div>
		</div>
	</div>
	
	<?php foreach ($managers as $manager) { ?>
	<h3>Доли статусов <?php echo $manager->name; ?></h3>
	<div class="parts">
		<div class="pie pie<?php echo $manager->id; ?>" style="width: 50%;"></div>
		<div class="info" style="width: 50%;">
			
			<div class="part">
				<div class="label pl-a"></div>
				<div class="name">Создано</div>
				<div class="value"><?php echo $invoices_state_0[$manager->id]; ?></div>
				<div class="pc"><?php echo round($invoices_state_0[$manager->id] / array_sum($counts[$manager->id]) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-b"></div>
				<div class="name">Сформировано</div>
				<div class="value"><?php echo $invoices_state_1[$manager->id]; ?></div>
				<div class="pc"><?php echo round($invoices_state_1[$manager->id] / array_sum($counts[$manager->id]) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-c"></div>
				<div class="name">Отправлено</div>
				<div class="value"><?php echo $invoices_state_2[$manager->id]; ?></div>
				<div class="pc"><?php echo round($invoices_state_2[$manager->id] / array_sum($counts[$manager->id]) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-d"></div>
				<div class="name">Оплачено</div>
				<div class="value"><?php echo $invoices_state_4[$manager->id]; ?></div>
				<div class="pc"><?php echo round($invoices_state_4[$manager->id] / array_sum($counts[$manager->id]) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-e"></div>
				<div class="name">Оплачено частично</div>
				<div class="value"><?php echo $invoices_state_5[$manager->id]; ?></div>
				<div class="pc"><?php echo round($invoices_state_5[$manager->id] / array_sum($counts[$manager->id]) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-f"></div>
				<div class="name">Отказ</div>
				<div class="value"><?php echo $invoices_state_6[$manager->id]; ?></div>
				<div class="pc"><?php echo round($invoices_state_6[$manager->id] / array_sum($counts[$manager->id]) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label"></div>
				<div class="name">Всего</div>
				<div class="value"><?php echo array_sum($counts[$manager->id]); ?></div>
				<div class="pc">100%</div>
				<div class="clear"></div>
			</div>
			
		</div>
	</div>
	<?php } ?>
	
	<script type="text/javascript">
		new Chartist.Line('#ch<?php echo $id; ?> .chart', {
			labels: ['<?php echo implode("', '", $labels[$managers[0]->id]); ?>'],
			series: [
			<?php foreach ($managers as $n => $manager) { if ($n != 0) { echo ", "; } if ($mid and $mid != $manager->id) { continue; } ?>
				 [{meta: '<?php echo $manager->name; ?>', value: <?php echo implode("},{meta: '{$manager->name}', value: ", $counts[$manager->id]); ?>}]
			<?php } ?>
			]
		}, {
			// high: 10,
			low: 0,
			showArea: false,
			height: 200,
			fullWidth: false,
			plugins: [
			    Chartist.plugins.tooltip()
			]
		});
		
		// Доли
		var data = {
			series: [<?php foreach ($managers as $n => $manager) { if ($n != 0) { echo ", "; } ?>
				<?php echo array_sum($counts[$manager->id]); ?>
			<?php } ?>]
		};

		var sum = function(a, b) { return a + b };

		new Chartist.Pie('#ch<?php echo $id; ?> .pie', data, {
			labelInterpolationFnc: function(value) {
				return Math.round(value / <?php echo count($invoices); ?> * 100) + '%';
			}
		});
		
		<?php foreach ($managers as $manager) { ?>
		// Доли
		var data = {
			series: [<?php echo $invoices_state_0[$manager->id]; ?>, <?php echo $invoices_state_1[$manager->id]; ?>, <?php echo $invoices_state_2[$manager->id]; ?>, <?php echo $invoices_state_4[$manager->id]; ?>, <?php echo $invoices_state_5[$manager->id]; ?>, <?php echo $invoices_state_6[$manager->id]; ?>]
		};

		new Chartist.Pie('#ch<?php echo $id; ?> .pie<?php echo $manager->id?>', data, {
			labelInterpolationFnc: function(value) {
				return Math.round(value / <?php echo array_sum($counts[$manager->id]); ?> * 100) + '%';
				// return value;
			}
		});
		<?php } ?>
	</script>
	<?php
?>