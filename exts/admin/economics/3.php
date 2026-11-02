<?php
	
	defined('access') or die ('no access');
	
	$date_d1 = getVar('date_d1', 'get'); if ($date_d1 == 'null') { $date_d1 = null; }
	$date_d2 = getVar('date_d2', 'get'); if ($date_d2 == 'null') { $date_d2 = null; }
	
	if (!$date_d1 and !$date_d2) {
		
		$date_d1 = date('d.m.Y');
		$date_d2 = date('d.m.Y');
	}
	
	$sql_date_d1 = date('Y-m-d', strtotime($date_d1));
	$sql_date_d2 = date('Y-m-d', strtotime($date_d2));
	
	if ($date_d1 and $date_d2) { $where .= " AND `date` BETWEEN '{$sql_date_d1}' AND '{$sql_date_d2}'"; }
	if ($date_d1 and $date_d2 == null) { $where .= " AND `date` >= '{$sql_date_d1}'"; }
	if ($date_d2 and $date_d1 == null) { $where .= " AND `date` <= '{$sql_date_d2}'"; }
	
	if ($date_d1 and $date_d2) { $where_events .= " AND ((`date` BETWEEN '{$sql_date_d1}' AND '{$sql_date_d2}') OR (`pay_date` BETWEEN '{$sql_date_d1}' AND '{$sql_date_d2}'))"; }
	if ($date_d1 and $date_d2 == null) { $where_events .= " AND (`date` >= '{$sql_date_d1}' OR `pay_date` >= '{$sql_date_d1}')"; }
	if ($date_d2 and $date_d1 == null) { $where_events .= " AND (`date` <= '{$sql_date_d2}' OR `pay_date` <= '{$sql_date_d2}')"; }
	
	if ($sid = getInt('sid', 'get')) { $where_events .= " AND `rid` IN(SELECT `id` FROM `rooms` WHERE `sid` = {$sid})"; }
	
	$events = loadObjectList("SELECT *, (SELECT `name` FROM `studios` WHERE `id` = (SELECT `sid` FROM `rooms` WHERE `id` = `rid`)) AS `studio`, (SELECT `name` FROM `rooms` WHERE `id` = `rid`) AS `room`, (SELECT `min` FROM `rooms` WHERE `id` = `rid`) AS `min`, (SELECT `name` FROM `clients` WHERE `id` = `cid`) AS `client` FROM `events` WHERE `state` != -1 {$where_events} ORDER BY `date` ASC, `start` ASC");
	
	$where_incomes_expenses = "";
	if ($sid = getInt('sid', 'get')) { $where_incomes_expenses .= " AND `sid` = {$sid}"; }
	if ($aid = getInt('aid', 'get')) { $where_incomes_expenses .= " AND `aid` = {$aid}"; }
	
	// Доп. доходы
	$sql = "SELECT *," .
			" (SELECT `name` FROM `administrators` WHERE `id` = `aid`) AS `admin_name`".
			" FROM `incomes`".
			" WHERE `id` > 0{$where}{$where_incomes_expenses}".
			" ORDER BY `date` ASC";
	
	$incomes = loadObjectList($sql);
	
	// Расходы
	$sql = "SELECT *," .
			" (SELECT `name` FROM `administrators` WHERE `id` = `aid`) AS `admin_name`".
			" FROM `expenses`".
			" WHERE `id` > 0{$where}{$where_incomes_expenses}".
			" ORDER BY `date` ASC";
	
	$expenses = loadObjectList($sql);
	
	if (cnt($expenses) and $aid) {
		
		$admin_dates = array();
		foreach ($expenses as $expense) {
			
			if ($expense->type == 1) {
				
				$admin_dates[] = $expense->date;
			}
		}
	}
	
	$studios = loadObjectList("SELECT * FROM `studios`");
	$admins = loadObjectList("SELECT * FROM `administrators`");
	
	// Подсчет арендного оборудования
	// Исключение событий, если выбран админ
	if (cnt($events)) {
		
		$events2 = array();
		$equipments = array();
		
		foreach($events as $event) {
		
			if ($aid and !in_array($event->date, $admin_dates)) continue;
			
			$events2[] = $event;
		
			$h = ($event->start < $event->end) ? $event->end - $event->start : 7;
			
			$eqs = json_decode($event->eqs);
			
			$eqs_price_sum = 0;
			$event->rent_price = $event->price;
			
			if (cnt($eqs)){
				
				foreach($eqs as $eq) {
					
					$this_eq = loadObject("SELECT * FROM `equipments` WHERE `id` = {$eq->id}");
				
					$eqs_price = $this_eq->price * $h * $eq->count;
					
					$equipments[$eq->id]->name = $this_eq->name;
					$equipments[$eq->id]->client = $event->client;
					$equipments[$eq->id]->sid = $this_eq->sid;
					$equipments[$eq->id]->price += $eqs_price;
					// $equipments[$eq->id]->count += $eq->count;
					$equipments[$eq->id]->h += $h;
					
					$eqs_price_sum += $eqs_price;
				}
			}
			
			// Для старых событий с нулевой суммаой оборудования
			if ($event->price_eqs == 0) {
				
				$event->rent_price -= $eqs_price_sum;
			}
		}
		
		$events = $events2;
	}
	
?>
<div id="report">

	<div class="filter">
		<div><span>Дата </span> 
		с <input id="date_d1" type="text" value="<?php echo $date_d1; ?>" readonly="readonly" style="cursor: pointer;"/>
		по <input id="date_d2" type="text" value="<?php echo $date_d2; ?>" readonly="readonly" style="cursor: pointer;"/>
		</div>
		<div>
		<input type="button" class="apply" value="применить" onclick="filter();"/>
		<input type="button" class="reset" value="сбросить фильтр" style="margin-left: 10px;" onclick="location.assign('<?php echo admin; ?>/economics?type=3&f=0');"/>
		</div>
		<div style="float: right; margin: 1px 10px 0 0;">
			<select id="sid" onchange="filter();">
				<option value="">все студии</option>
				<?php foreach ($studios as $studio) { ?>
					<option value="<?php echo $studio->id; ?>"<?php echo ($sid == $studio->id) ? " selected" : ""; ?>><?php echo $studio->name; ?></option>
				<?php } ?>
			</select>
		</div>
		<div style="float: right; margin: 1px 10px 0 0;">
			<select id="aid" onchange="filter();">
				<option value="">все администраторы</option>
				<?php foreach ($admins as $admin) { ?>
					<option value="<?php echo $admin->id; ?>"<?php echo ($aid == $admin->id) ? " selected" : ""; ?>><?php echo $admin->name; ?></option>
				<?php } ?>
			</select>
		</div>
		<p class="clear"></p>
	</div>
	
	<div class="block w50p">
		<div class="bHead"><div class="bName">Репетиции</div><span class="showhide" data-n="1">Показать список</span></div>
		<div class="list" data-n="1" data-s="0">
		<?php if (cnt($events)) { ?>
			<div class="item head">
				<div class="num">#</div>
				<div class="date">Дата</div>
				<div class="time">Время</div>
				<div class="place">Место</div>
				<div class="client">Клиент</div>
				<div class="prices">
					<span>Скидка, %</span>
					<!--<span class="brd">Ст-ть общ с доп. доходом</span>-->
					<span class="brd">Ст-ть репетиц.</span>
					<span class="brd">Оплачено</span>
				</div>
				<p class="clear"></p>
			</div>
		<?php
			
			foreach($events as $n => $event) {
				
				$is_pred = false;
				$is_pro = false;
				$is_free = false;
				
				// Предоплата
				if ($event->pay_date != '0000-00-00' and strtotime($event->pay_date) < strtotime($event->date) and strtotime($event->pay_date) < strtotime($date_d1)) {
					
					$is_pred = true;
				}
				
				// Просрочка
				if ($event->pay_date != '0000-00-00' and strtotime($event->pay_date) > strtotime($event->date) and strtotime($event->pay_date) > strtotime($date_d1)) {
					
					$is_pro = true;
				}
				
				// Бесплатная
				if ($event->free == 1) {
					
					$is_free == true;
				}
				
				// Оплачено только за репетицию
				if ($event->pay > $event->rent_price) {
					
					$event->pay = $event->rent_price;
				}
				
				// Округления
				$event->rent_price = round($event->rent_price, 1);
				$event->price = round($event->price, 1);
				$event->pay = round($event->pay, 1);
			
				$evenst_sum_rent_price += $event->rent_price;
				$evenst_sum_price += $event->price;
				$evenst_sum_pay += ($event->pay_type == 1 and $is_pred == false and $is_pro == false and $is_free == false) ? $event->pay : 0;
				
				if ($is_pro and $date_d1 != $date_d2 and $event->pay > 0) {
					
					$evenst_sum_pay += $event->pay;
				}
				
				switch ($event->nm) {
					
					case 0: $peoples = "> 2"; break;
					case 1: $peoples = "1"; break;
					case 2: $peoples = "2"; break;
				}
				
				$min = ($event->min == 0) ? '00' : $event->min;
			
		?>
			<div class="item">
				<div class="num"><?php echo $n+1; ?></div>
				<div class="date"><?php echo date('d.m.Y', strtotime($event->date)); ?></div>
				<div class="time"><?php echo $event->start; ?>:<?php echo $min; ?> - <?php echo $event->end; ?>:<?php echo $min; ?></div>
				<div class="place"><b><?php echo $event->studio; ?></b> | <?php echo $event->room; ?></div>
				<div class="client"><?php echo ($event->cid != 0) ? $event->client : "Неопределенный клиент"; ?> (<?php echo $peoples; ?>)</div>
				<div class="prices">
					<span><?php echo $event->dis; ?></span>
					<!--<span class="brd"><?php echo $event->price; ?></span>-->
					<span class="brd"><?php echo $event->rent_price; ?></span>
					<?php if ($is_pred) { ?>
					<span class="brd" style="font-size: 10px;">предоплата от <?php echo date('d.m.Y', strtotime($event->pay_date)); ?></span>
					<?php } elseif ($is_pro) { ?>
					<span class="brd" style="font-size: 10px;">оплачено <?php echo date('d.m.Y', strtotime($event->pay_date)); ?></span>
					<?php } elseif ($is_free) { ?>
					<span class="brd" style="font-size: 10px;">бесплатно</span>
					<?php } else { ?>
					<span class="brd">
						<?php echo $event->pay; ?><?php echo ($event->pay_type == 2) ? " безн" : ""; ?>
						<?php echo ($event->state == 4) ? " [отмена]" : "";?>
						<?php echo ($event->state == 5) ? " [неявка]" : "";?>
					</span>
					<?php } ?>
				</div>
				<p class="clear"></p>
				<?php// if ($event->p == 1) { ?>
				<?php if ($event->note) { ?>
					<p style="padding: 5px 0 0 0; font-size: 10px; text-align: left;"><i><?php echo $event->note; ?></i></p>
				<?php } ?>
			</div>
		<?php } ?>
			<div class="total">
			Итого:
			<div class="prices">
				<span><?php echo getPrice($evenst_sum_rent_price); ?></span>
				<!--<span class="brd"><?php echo getPrice($evenst_sum_price); ?></span>-->
				<span class="brd"><?php echo getPrice($evenst_sum_pay); ?></span>
			</div>
			</div>
		<?php } else { ?>
			<div class="item">Нет событий</div>
		<?php } ?>
		</div>
	</div>
	
	<div class="block w50p">
		<div class="bHead"><div class="bName">Аренда оборудования</div><span class="showhide" data-n="2">Показать список</span></div>
		<div class="list" data-n="2" data-s="0">
		<?php if (cnt($equipments)) { ?>
			<div class="item">
				<div class="num">#</div>
				<div class="w200">Наименование</div>
				<div class="w200">Группа</div>
				<div class="place">Место</div>
				<!--<div class="w50">Кол.</div>-->
				<div class="w50">Час.</div>
				<div class="price">Общая стоимость</div>
				<p class="clear"></p>
			</div>
		<?php
		
			$n = 1;
			foreach($equipments as $equipment) {
				
				$equipment->price = round($equipment->price, 1);
			
				$equipments_sum_price += $equipment->price;
			
		?>
			<div class="item">
				<div class="num"><?php echo $n; ?></div>
				<div class="w200"><?php echo $equipment->name; ?></div>
				<div class="w200"><?php echo $equipment->client; ?></div>
				<div class="place"><?php echo getStudioName($equipment->sid); ?></div>
				<!--<div class="w50"><?php echo $equipment->count; ?></div>-->
				<div class="w50"><?php echo $equipment->h; ?></div>
				<div class="price"><?php echo $equipment->price; ?></div>
				<p class="clear"></p>
			</div>
		<?php $n++; } ?>
			<div class="total">Итого: <b><?php echo getPrice($equipments_sum_price); ?></b> руб.</div>
		<?php } else { ?>
			<div class="item">Нет записей</div>
		<?php } ?>
		</div>
	</div>
	
	<div class="clear"></div>
	
	<div class="block w50p">
		<div class="bHead"><div class="bName">Дополнительные доходы</div><span class="showhide" data-n="3">Показать список</span></div>
		<div class="list" data-n="3" data-s="0">
		<?php if (cnt($incomes)) { ?>
			<div class="item">
				<div class="num">#</div>
				<div class="date">Дата</div>
				<div class="w200">Наименование</div>
				<div class="place">Место</div>
				<div class="w100">Администратор</div>
				<div class="price">Стоимость</div>
				<p class="clear"></p>
			</div>
		<?php
			
			$n = 1;
			foreach($incomes as $income) {
				
				$income->sum = round($income->sum, 1);
				
				$incomes_sum_price += $income->sum;
			
		?>
			<div class="item">
				<div class="num"><?php echo $n; ?></div>
				<div class="date"><?php echo date('d.m.Y', strtotime($income->date)); ?></div>
				<div class="w200"><?php echo $income->name; ?></div>
				<div class="place"><?php echo getStudioName($income->sid); ?></div>
				<div class="w100"><?php echo $income->admin_name; ?></div>
				<div class="price"><?php echo $income->sum; ?></div>
				<p class="clear"></p>
			</div>
		<?php $n++; } ?>
			<div class="total">Итого: <b><?php echo getPrice($incomes_sum_price); ?></b> руб.</div>
		<?php } else { ?>
			<div class="item">Нет записей</div>
		<?php } ?>
		</div>
	</div>
	
	<div class="block w50p">
		<div class="bHead"><div class="bName">Расходы</div><span class="showhide" data-n="4">Показать список</span></div>
		<div class="list" data-n="4" data-s="0">
		<?php if (cnt($expenses)) { ?>
			<div class="item">
				<div class="num">#</div>
				<div class="date">Дата</div>
				<div class="w200">Наименование</div>
				<div class="place">Место</div>
				<div class="w100">Администратор</div>
				<div class="price">Стоимость</div>
				<p class="clear"></p>
			</div>
		<?php
		
			$n = 1;
			foreach($expenses as $expense) {
				
				$expense->sum = round($expense->sum, 1);
			
				$expenses_sum_price += $expense->sum;
				
				if ($expense->type == 1) {
					
					$expenses_sum_to_admin += $expense->sum;
				}
		?>
			<div class="item">
				<div class="num"><?php echo $n; ?></div>
				<div class="date"><?php echo date('d.m.Y', strtotime($expense->date)); ?></div>
				<div class="w200">
					<i><small><?php echo getExpenseTypeName($expense->type); ?></small></i>
					<br/>
					<?php echo $expense->comment; ?>
				</div>
				<div class="place"><?php echo getStudioName($expense->sid); ?></div>
				<div class="w100"><?php echo $expense->admin_name; ?></div>
				<div class="price"><?php echo $expense->sum; ?></div>
				<p class="clear"></p>
			</div>
		<?php $n++; } ?>
			<div class="total">Итого: <b><?php echo getPrice($expenses_sum_price); ?></b> руб.</div>
		<?php } else { ?>
			<div class="item">Нет записей</div>
		<?php } ?>
		</div>
	</div>
	
	<?php 
		
		$val = $evenst_sum_pay + $incomes_sum_price;
		$zarplata = $expenses_sum_to_admin + $equipments_sum_price;
		// $pribyl = $val - $expenses_sum_price - $equipments_sum_price;
		$pribyl = $val - $expenses_sum_price;
		
	?>
	
	<div class="block w100p">
		<div class="bHead"><div class="bName">Общая информация</div></div>
		<div class="total">Валовая выручка: <b><?php echo getPrice($val); ?></b> руб.</div>
		<!--<div class="total">Зарплата админа: <b><?php echo getPrice($zarplata); ?></b> руб.</div>-->
		<div class="total">Расходы: <b><?php echo getPrice($expenses_sum_price); ?></b> руб.</div>
		<div class="total">Прибыль: <b><?php echo getPrice($pribyl); ?></b> руб.</div>
	</div>
	<div class="clear"></div>
</div>

<script type="text/javascript">

	// $('#date').datepick($.extend({ 
		
		// showAnim: 'slideDown',
		// minDate: '<?php echo date('d.m.Y', strtotime('now - 1 month'));?>',
		// maxDate: '<?php echo date('d.m.Y', strtotime('now'));?>'
	
	// }, $.datepick.regional['ru']));
	
	$('#date_d1').datepick($.extend({ 
		
		showAnim: 'slideDown',
		onSelect: function() {
			
			var thisDate = new Date($('#date_d1').datepick('getDate'));
			var minDate = thisDate.getDate()+'.'+(thisDate.getMonth() + 1)+'.'+thisDate.getFullYear();
			$('#date_d2').datepick('option', 'minDate', minDate);
		}
	
	}, $.datepick.regional['ru']));

	$('#date_d2').datepick($.extend({ 
		
		showAnim: 'slideDown'
	
	}, $.datepick.regional['ru']));

	function filter(){
	
		var date_d1 = ($('#date_d1').val()) ? $('#date_d1').val() : 'null';
		var date_d2 = ($('#date_d2').val()) ? $('#date_d2').val() : 'null';
		
		var sid = $('#sid').val();
		var aid = $('#aid').val();
		
		var link = '<?php echo admin; ?>/economics?type=3&f=1';
		
		if (date_d1) { link += '&date_d1='+date_d1+'&date_d2='+date_d2; }
		
		if (sid) { link += '&sid='+sid; }
		if (aid) { link += '&aid='+aid; }
		
		location.assign(link);
	}
	
	$('.list[data-n]').each(function(){
		
		var n = $(this).data('n');
		
		if (localStorage['list'+n] == 1) {
		
			$(this).attr('data-s', 1);
			$('.showhide[data-n="'+n+'"]').text('Скрыть список');
		}
	});
	
	$('.showhide').on('click', function(){
		
		var n = $(this).data('n');
		var list = $('.list[data-n="'+n+'"]');
		
		if (list.attr('data-s') == 0) {
			
			list.attr('data-s', 1);
			localStorage['list'+n] = 1;
			$(this).text('Скрыть список');
		}
		else {
			
			list.attr('data-s', 0);
			localStorage['list'+n] = 0;
			$(this).text('Показать список');
		}
	});
	
</script>