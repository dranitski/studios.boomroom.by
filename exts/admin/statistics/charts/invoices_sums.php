<?php

	defined('access') or die ('no access');
	
	$id		= getInt('id', 'post');
	$start	= getVar('start', 'post');	$date_start = date('d.m.Y', strtotime($start));
	$end	= getVar('end', 'post');	$date_end = date('d.m.Y', strtotime($end));
	$mid	= getInt('mid', 'post');
	
	$sql_date_start = date('Y-m-d H:i:s', strtotime($date_start));
	$sql_date_end = date('Y-m-d H:i:s', strtotime("{$date_end} 23:59:59"));
	
	$WHERE = "WHERE `created` BETWEEN '{$sql_date_start}' AND '{$sql_date_end}'";
	$WHERE .= " AND `id` NOT IN (SELECT `iid` FROM `invoice_canceled` WHERE `iid` = `id` AND `type` = 0)";
	
	// Все счета
	$invoices = loadObjectList("SELECT *, (SELECT `type` FROM `invoice_canceled` WHERE `iid` = `id`) AS `no` FROM `invoices` {$WHERE} ORDER BY `created` ASC");
	
	$invoices_sum_paid = 0;
	$invoices_sum = 0;
	$invoices_sum_0 = 0;
	$invoices_sum_1 = 0;
	$invoices_sum_2 = 0;
	$invoices_sum_3 = 0;
	$invoices_sum_4 = 0;
	$invoices_sum_5 = 0;
	$invoices_sum_6 = 0;
	
	// Суммы по статусам
	foreach ($invoices as $invoice) {
	
		// Все оплаченные и оплаченные частично
		if ($invoice->state == 4 or $invoice->state == 5) { $invoices_sum_paid += $invoice->sum; }
		
		if ($mid and $mid != $invoice->uid) { continue; }
		
		$invoice->state = ($invoice->no == 1) ? 6 : $invoice->state;
		
		switch ($invoice->state) {
			
			case 0: $invoices_sum_0 += $invoice->sum; break;
			case 1: $invoices_sum_1 += $invoice->sum; break;
			case 2: $invoices_sum_2 += $invoice->sum; break;
			case 3: $invoices_sum_3 += $invoice->sum; break;
			case 4: $invoices_sum_4 += $invoice->sum; break;
			case 5: $invoices_sum_5 += $invoice->sum; break;
			case 6: $invoices_sum_6 += $invoice->sum; break;
		}
		
		$invoices_sum += $invoice->sum;
	}
	
	if ($invoices_sum) {
	?>
	
	<div class="parts">
		<div class="pie" style="width: 50%;"></div>
		<div class="info" style="width: 50%;">
			
			<div class="part">
				<div class="label pl-a"></div>
				<div class="name">Создано</div>
				<div class="value w100"><?php echo getPrice($invoices_sum_0); ?></div>
				<div class="pc"><?php echo round($invoices_sum_0 / $invoices_sum * 100, 2); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-b"></div>
				<div class="name">Сформировано</div>
				<div class="value w100"><?php echo getPrice($invoices_sum_1); ?></div>
				<div class="pc"><?php echo round($invoices_sum_1 / $invoices_sum * 100, 2); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-c"></div>
				<div class="name">Отправлено</div>
				<div class="value w100"><?php echo getPrice($invoices_sum_2); ?></div>
				<div class="pc"><?php echo round($invoices_sum_2 / $invoices_sum * 100, 2); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-d"></div>
				<div class="name">Оплачено</div>
				<div class="value w100"><?php echo getPrice($invoices_sum_4); ?></div>
				<div class="pc"><?php echo round($invoices_sum_4 / $invoices_sum * 100, 2); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-e"></div>
				<div class="name">Оплачено частично</div>
				<div class="value w100"><?php echo getPrice($invoices_sum_5); ?></div>
				<div class="pc"><?php echo round($invoices_sum_5 / $invoices_sum * 100, 2); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label pl-f"></div>
				<div class="name">Отказ</div>
				<div class="value w100"><?php echo getPrice($invoices_sum_6); ?></div>
				<div class="pc"><?php echo round($invoices_sum_6 / $invoices_sum * 100, 2); ?>%</div>
				<div class="clear"></div>
			</div>
			
			<div class="part">
				<div class="label"></div>
				<div class="name">Всего</div>
				<div class="value w100"><?php echo getPrice($invoices_sum); ?></div>
				<div class="pc">100%</div>
				<div class="clear"></div>
			</div>
			
		</div>
	</div>
	<script type="text/javascript">
		
		
		// Доли
		var data = {
			series: [<?php echo $invoices_sum_0; ?>, <?php echo $invoices_sum_1; ?>, <?php echo $invoices_sum_2; ?>, <?php echo $invoices_sum_4; ?>, <?php echo $invoices_sum_5; ?>, <?php echo $invoices_sum_6; ?>]
		};

		new Chartist.Pie('#ch<?php echo $id; ?> .pie', data, {
			labelInterpolationFnc: function(value) {
				return Math.round(value / <?php echo $invoices_sum; ?> * 100) + '%';
				// return (value / <?php echo $invoices_sum; ?> * 100).toFixed(2) + '%';
				// return value;
			}
		});
		
	</script>
	<?php
	}
	
	if ($invoices_sum_paid) {
	
		// Список менеджеров
		$managers = loadObjectList("SELECT * FROM `users` WHERE `type` = 0");
		
		foreach ($managers as $manager) {
			
			// Суммы по статусам
			foreach ($invoices as $invoice) {
				
				if ($invoice->uid == $manager->id) {
					
					// Все оплаченные и оплаченные частично
					if ($invoice->state == 4 or $invoice->state == 5) { $invoices_sum_paids[$manager->id] += $invoice->sum; }
				}
			}
		}
		
	?>
	<h3>Доля менеджера в сумме оплаченых и частично оплаченых</h3>
	<div class="parts">
		<div class="pie pie2" style="width: 50%;"></div>
		<div class="info" style="width: 50%;">
			
			<?php
			foreach ($managers as $n => $manager) {
				
				switch ($n) { case 0: $char = 'a'; break; case 1: $char = 'b'; break; case 2: $char = 'c'; break; case 3: $char = 'd'; break; case 4: $char = 'e'; break; }
			?>
			<div class="part">
				<div class="label pl-<?php echo $char; ?>"></div>
				<div class="name"><?php echo $manager->name; ?></div>
				<div class="value w100"><?php echo getPrice($invoices_sum_paids[$manager->id]); ?></div>
				<div class="pc"><?php echo round($invoices_sum_paids[$manager->id] / $invoices_sum_paid * 100, 2); ?>%</div>
				<div class="clear"></div>
			</div>
			<?php } ?>
			
			<div class="part">
				<div class="label"></div>
				<div class="name">Всего</div>
				<div class="value w100"><?php echo getPrice($invoices_sum_paid); ?></div>
				<div class="pc">100%</div>
				<div class="clear"></div>
			</div>
			
		</div>
	</div>
	<script type="text/javascript">
		
		// Доли
		var data = {
			series: [<?php foreach ($managers as $n => $manager) { if ($n != 0) { echo ", "; } ?>
				<?php echo $invoices_sum_paids[$manager->id]; ?>
			<?php } ?>]
		};

		new Chartist.Pie('#ch<?php echo $id; ?> .pie2', data, {
			labelInterpolationFnc: function(value) {
				return Math.round(value / <?php echo $invoices_sum_paid; ?> * 100) + '%';
			}
		});
	</script>
	<?php
	}
?>