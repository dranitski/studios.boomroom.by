<?php

	defined('access') or die ('no access');
	
	$id		= getInt('id', 'post');
	$start	= getVar('start', 'post');	$date_start = date('d.m.Y', strtotime($start));
	$end	= getVar('end', 'post');	$date_end = date('d.m.Y', strtotime($end));
	$group	= getInt('group', 'post');
	$ord	= getInt('ord', 'post');
	$sort	= getInt('sort', 'post');
	
	$sql_date_start = date('Y-m-d H:i:s', strtotime($date_start));
	$sql_date_end = date('Y-m-d H:i:s', strtotime("{$date_end} 23:59:59"));
	
	$WHERE = "WHERE `created` BETWEEN '{$sql_date_start}' AND '{$sql_date_end}'";
	$WHERE .= " AND `id` NOT IN (SELECT `iid` FROM `invoice_canceled` WHERE `iid` = `id` AND `type` = 0)";
	
	// Все счета
	$invoices = loadObjectList("SELECT *, (SELECT `type` FROM `invoice_canceled` WHERE `iid` = `id`) AS `no` FROM `invoices` {$WHERE} ORDER BY `created` ASC");
	
	$invoices_dates = array();
	
	// Количества по статусам
	foreach ($invoices as $invoice) {
		
		$date = date('d.m.Y', strtotime($invoice->created));
		$invoices_dates[] = $date;
		
		$invoice->state = ($invoice->no == 1) ? 6 : $invoice->state;
		
		switch ($invoice->state) {
			
			case 0: $invoices_state_0++; break;
			case 1: $invoices_state_1++; break;
			case 2: $invoices_state_2++; break;
			case 3: $invoices_state_3++; break;
			case 4: $invoices_state_4++; break;
			case 5: $invoices_state_5++; break;
			case 6: $invoices_state_6++; break;
		}
	}
	
	// Отказы
	$invoices_no = cnt($invoices) - $invoices_state_0 - $invoices_state_1 - $invoices_state_2 - $invoices_state_3 - $invoices_state_4 - $invoices_state_5;
	
	$invoices_dates = array_count_values($invoices_dates);
	
	$labels	= getLabelsCountsList($invoices_dates, $date_start, $date_end, $group, 1);
	$counts	= getLabelsCountsList($invoices_dates, $date_start, $date_end, $group, 2);

	?>
	<div class="chart" style="height: 200px;"></div>
	
	<h3>Доли статусов</h3>
	<div class="parts">
		<div class="pie" style="width: 50%;"></div>
		<div class="info" style="width: 50%;">
			
			<div class="part">
				<div class="label pl-a"></div>
				<div class="name">Создано</div>
				<div class="value"><?php echo $invoices_state_0; ?></div>
				<div class="pc"><?php echo round($invoices_state_0 / array_sum($counts) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-b"></div>
				<div class="name">Сформировано</div>
				<div class="value"><?php echo $invoices_state_1; ?></div>
				<div class="pc"><?php echo round($invoices_state_1 / array_sum($counts) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-c"></div>
				<div class="name">Отправлено</div>
				<div class="value"><?php echo $invoices_state_2; ?></div>
				<div class="pc"><?php echo round($invoices_state_2 / array_sum($counts) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-d"></div>
				<div class="name">Оплачено</div>
				<div class="value"><?php echo $invoices_state_4; ?></div>
				<div class="pc"><?php echo round($invoices_state_4 / array_sum($counts) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-e"></div>
				<div class="name">Оплачено частично</div>
				<div class="value"><?php echo $invoices_state_5; ?></div>
				<div class="pc"><?php echo round($invoices_state_5 / array_sum($counts) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-f"></div>
				<div class="name">Отказ</div>
				<div class="value"><?php echo $invoices_state_6; ?></div>
				<div class="pc"><?php echo round($invoices_state_6 / array_sum($counts) * 100); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label"></div>
				<div class="name">Всего</div>
				<div class="value"><?php echo cnt($invoices); ?></div>
				<div class="pc">100%</div>
				<div class="clear"></div>
			</div>
			
		</div>
	</div>
	
	<?php
	
		// Сортировка списков
		$labels_list = $labels;
		$counts_list = $counts;
		
		switch ($ord) {
			
			case 1:
			
				if ($sort == 2) { krsort($labels_list); }
				
				$list = array(); foreach ($labels_list as $n => $label) { $list[] = (object) array('label' => $label, 'count' => $counts_list[$n]); }
		
			break;
			
			case 2:
				
				$plist = array(); foreach ($labels_list as $n => $label) { $plist[$label] = $counts_list[$n]; }
			
				if ($sort == 1) { asort($plist); } else { arsort($plist); }
				
				$list = array(); foreach ($plist as $label => $count) { $list[] = (object) array('label' => $label, 'count' => $count); }
		
			break;
		}
		
	?>
	
	<div class="list">
	<?php if (cnt($list)) { ?>
		<div class="filter">
			<div class="w50">№</div>
			<div class="w200"><a href="javascript:void(0);" onclick="setOrd(<?php echo $id; ?>, 1);">Период</a></div>
			<div><a href="javascript:void(0);" onclick="setOrd(<?php echo $id; ?>, 2);">Количество</a></div>
			<p class="clear"></p>
		</div>
	<?php foreach ($list as $n => $item) { ?>
		<div class="item">
			<div class="num"><?php echo $n+1; ?></div>
			<div class="period"><?php echo $item->label; ?></div>
			<div class="count"><?php echo $item->count; ?></div>
			<div class="clear"></div>
		</div>
	<?php } ?>	
		<div class="result">
			<div class="label">Всего</div>
			<div class="date">с <?php echo $date_start; ?> по <?php echo $date_end; ?></div>
			<div class="sum"><?php echo array_sum($counts);?></div>
			<div class="clear"></div>
		</div>
	<?php } ?>	
	</div>
	
	<script type="text/javascript">
		new Chartist.Line('#ch<?php echo $id; ?> .chart', {
			labels: ['<?php echo implode("', '", $labels); ?>'],
			series: [
				[{
					meta: 'Счетов',
					value: <?php echo implode("},{meta: 'Счетов', value: ", $counts); ?>
				}]
			   // [<?php echo implode(", ", $counts); ?>]
			]
		}, {
			// high: 10,
			low: 0,
			showArea: true,
			height: 200,
			fullWidth: false,
			plugins: [
			    Chartist.plugins.tooltip()
			]
		});
		
		// Доли
		var data = {
			series: [<?php echo $invoices_state_0; ?>, <?php echo $invoices_state_1; ?>, <?php echo $invoices_state_2; ?>, <?php echo $invoices_state_4; ?>, <?php echo $invoices_state_5; ?>, <?php echo $invoices_state_6; ?>]
		};
		// var data = {
				// series: [{
			    // value: 20,
			    // name: 'Series 1',
			    // className: 'ct-series-d',
			    // meta: 'Meta One'
			  // }, {
			    // value: 10,
			    // name: 'Series 2',
			    // className: 'ct-series-b',
			    // meta: 'Meta Two'
			  // }, {
			    // value: 70,
			    // name: 'Series 3',
			    // className: 'ct-series-a',
			    // meta: 'Meta Three'
			  // }]
		// };

		var sum = function(a, b) { return a + b };

		new Chartist.Pie('#ch<?php echo $id; ?> .pie', data, {
			labelInterpolationFnc: function(value) {
				return Math.round(value / data.series.reduce(sum) * 100) + '%';
				// return value;
			}
		});
	</script>
	<?php
?>