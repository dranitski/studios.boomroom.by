<?php

	defined('access') or die ('no access');
	
	include_once path."/functions/date.php";
	
	$id		= getInt('id', 'post');
	$start	= getVar('start', 'post');	$date_start = date('d.m.Y', strtotime($start));
	$end	= getVar('end', 'post');	$date_end = date('d.m.Y', strtotime($end));
	$plan	= getInt('plan', 'post');
	$mid	= getInt('mid', 'post');
	$group	= getInt('group', 'post');
	$ord	= getInt('ord', 'post');
	$sort	= getInt('sort', 'post');
	
	$sql_date_start = date('Y-m-d H:i:s', strtotime($date_start));
	$sql_date_end = date('Y-m-d H:i:s', strtotime("{$date_end} 23:59:59"));
	
	$WHERE = "WHERE `paid` BETWEEN '{$sql_date_start}' AND '{$sql_date_end}' AND (`state` = 4 OR `state` = 5)";
	
	// Все счета
	$invoices = loadObjectList("SELECT * FROM `invoices` {$WHERE} ORDER BY `paid` ASC");
	
	// Отмененные
	$cdids = loadResultArray("SELECT `iid` FROM `invoice_canceled`");
	
	// Убираем отмененные
	foreach ($invoices as $key => $invoice) {
		
		if (in_array($invoice->id, $cdids)) { unset($invoices[$key]); }
	}
	
	// Список менеджеров
	$managers = loadObjectList("SELECT * FROM `users` WHERE `type` = 0");
	
	// Количества
	foreach ($invoices as $invoice) {
		
		if ($mid and $invoice->uid != $mid) { continue; }
		
		$date = date('d.m.Y', strtotime($invoice->paid));
		$invoices_dates[] = $date;
		
		if (strtotime($invoice->date) < strtotime(date('Y-m-d', strtotime($invoice->paid)))) {
		
			$invoices_dates_exp[] = $date;
		}
	}
	
	$invoices_dates = array_count_values($invoices_dates);
	if (cnt($invoices_dates_exp)) { $invoices_dates_exp = array_count_values($invoices_dates_exp); }
	
	$labels	= getLabelsCountsList($invoices_dates, $date_start, $date_end, $group, 1);
	$counts	= getLabelsCountsList($invoices_dates, $date_start, $date_end, $group, 2);
	
	$counts_exp = getLabelsCountsList($invoices_dates_exp, $date_start, $date_end, $group, 2);	
	
	// Суммы по месяцам
	$monthSums = getMonthSums($invoices, $mid);
	
	?>
	<div class="chart" style="height: 200px;"></div>
	
	<h3>Сумма счетов по месяцам</h3>
	<div class="list">
		<div class="filter">
			<div class="w50">№</div>
			<div class="w200">Месяц</div>
			<div class="w200">Сумма</div>
			<?php if ($mid) { ?>
			<div class="w200">КВП, %</div>
			<?php } ?>
			<p class="clear"></p>
		</div>
	<?php foreach ($monthSums as $m => $value) { ?>
		<div class="item">
			<div class="num"><?php echo $m; ?></div>
			<div class="period"><?php echo getMonth($m); ?></div>
			<div class="period"><?php echo ($mid) ? getPrice($value->sum_kr) : getPrice($value->sum); ?></div>
			<?php if ($mid) { ?>
			<div class="period"><?php echo round($value->sum_kr / $plan * 100); ?></div>
			<?php } ?>
			<div class="clear"></div>
		</div>
	<?php } ?>	
	</div>
	
	<script type="text/javascript">
		
		var data = {
			labels: ['<?php echo implode("', '", $labels); ?>'],
			series: [
				[{
					meta: 'Всего',
					value: <?php echo implode("},{meta: 'Всего', value: ", $counts); ?>
				}],
				[{
					meta: 'Просрочено',
					value: <?php echo implode("},{meta: 'Просрочено', value: ", $counts_exp); ?>
				}]
			]
		};

		var options = {
			seriesBarDistance: 10,
			plugins: [
			    Chartist.plugins.tooltip()
			]
		};
		/*
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
		*/
		
		new Chartist.Bar('#ch<?php echo $id; ?> .chart', data, options);
		
	</script>
	<?php
?>