<?php
	
	defined('access') or die ('no access');
	
	$orderby	= getVar('orderby', 'get');
	$sort		= getVar('sort', 'get');
	
	if (!$sort) { $sort =  'desc'; }
	if ($sort == 'asc') { $sort = 'desc'; } else { $sort = 'asc'; }
	
	switch ($orderby) {
		
		case 'id': $orderby = "a.`id` {$sort}"; break;
		case 'name': $orderby = "`client_name` {$sort}"; break;
		case 'price': $orderby = "a.`price` {$sort}"; break;
		case 'pay': $orderby = "a.`pay` {$sort}"; break;
		case 'debt': $orderby = "`debt` {$sort}"; break;
		case 'date': $orderby = "a.`date` {$sort}"; break;
		
		default: $orderby = "a.`date` DESC";
	}
	
	$date_d1 = getVar('date_d1', 'get'); if ($date_d1 == 'null') { $date_d1 = null; }
	$date_d2 = getVar('date_d2', 'get'); if ($date_d2 == 'null') { $date_d2 = null; }
	
	// if (!$date_d1 and !$date_d2) {
		
		// $date_d1 = date('d.m.Y');
		// $date_d2 = date('d.m.Y');
	// }
	
	$where = " WHERE `id` > 0 AND a.`price` > a.`pay` AND `state` >= 2 AND `state` != 3";
	// if ($sid = getInt('sid', 'get')) { $where .= " AND a.`sid` = {$sid}"; }
	if ($sid = getInt('sid', 'get')) { $where .= " AND a.`rid` IN(SELECT `id` FROM `rooms` WHERE `sid` = {$sid})"; }
	// if ($aid = getInt('aid', 'get')) { $where .= " AND a.`aid` = {$aid}"; }
	
	$sql_date_d1 = date('Y-m-d', strtotime($date_d1));
	$sql_date_d2 = date('Y-m-d', strtotime($date_d2));
	
	if ($date_d1 and $date_d2) { $where .= " AND a.`date` BETWEEN '{$sql_date_d1}' AND '{$sql_date_d2}'"; }
	if ($date_d1 and $date_d2 == null) { $where .= " AND a.`date` >= '{$sql_date_d1}'"; }
	if ($date_d2 and $date_d1 == null) { $where .= " AND a.`date` <= '{$sql_date_d2}'"; }
	
	$sql = "SELECT a.*," .
			" (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client_name`,".
			" (SELECT `sid` FROM `rooms` WHERE `id` = a.`rid`) AS `sid`,".
			" `price` - `pay` AS `debt`".
			" FROM `events` AS a".
			"{$where}".
			" ORDER BY {$orderby}, a.`start` ASC";
	
	$events = loadObjectList($sql);
	
	$studios = loadObjectList("SELECT * FROM `studios`");
	// $admins = loadObjectList("SELECT * FROM `administrators`");
	
	$equipments = loadObjectList("SELECT * FROM `equipments`");
?>

<div class="block">
	
	<div class="bHead">
		<div class="bName">Должники</div>
		<div class="bActions" style="display: none;">
			<a class="save" href="<?php echo admin; ?>/debtors?act=add" >Добавить</a>
		</div>
	</div>
<?php

	
	$orderLink = admin."/debtors?f=1";
	
	if ($date_d1) { $orderLink .= "&date_d1={$date_d1}"; }
	if ($date_d2) { $orderLink .= "&date_d2={$date_d2}"; }
	
	$orderLink_id	= "{$orderLink}&orderby=id&sort={$sort}";
	$orderLink_name	= "{$orderLink}&orderby=name&sort={$sort}";
	$orderLink_price	= "{$orderLink}&orderby=price&sort={$sort}";
	$orderLink_pay	= "{$orderLink}&orderby=pay&sort={$sort}";
	$orderLink_debt	= "{$orderLink}&orderby=debt&sort={$sort}";
	$orderLink_date	= "{$orderLink}&orderby=date&sort={$sort}";

?>	
	<div class="filter">
		<div><span>Дата </span> 
		с <input id="date_d1" type="text" value="<?php echo $date_d1; ?>" readonly="readonly" style="cursor: pointer;"/>
		по <input id="date_d2" type="text" value="<?php echo $date_d2; ?>" readonly="readonly" style="cursor: pointer;"/>
		</div>
		<div>
		<input type="button" class="apply" value="применить" onclick="filter();"/>
		<input type="button" class="reset" value="сбросить фильтр" style="margin-left: 10px;" onclick="location.assign('<?php echo admin; ?>/debtors?f=0');"/>
		</div>
		<div class="clear" style="float: none;"></div>
	</div>

	<div class="filter">
		<div class="w50">№</div>
		<div class="w200"><a href="<?php echo $orderLink_name; ?>">Клиент</a></div>
		<div class="w100"><a href="<?php echo $orderLink_price; ?>">Сумма</a></div>
		<div class="w100"><a href="<?php echo $orderLink_pay; ?>">Оплачено</a></div>
		<div class="w100"><a href="<?php echo $orderLink_debt; ?>">Долг</a></div>
		<div class="w100"><a href="<?php echo $orderLink_debt; ?>">Погашено</a></div>
		<div class="w200"><a href="<?php echo $orderLink_date; ?>">Дата</a></div>
		<div class="w200">Место</div>
		<div style="float: right; margin: 1px 10px 0 0;">
			<select id="sid" onchange="filter();">
				<option value="">все студии</option>
				<?php foreach ($studios as $studio) { ?>
					<option value="<?php echo $studio->id; ?>"<?php echo ($sid == $studio->id) ? " selected" : ""; ?>><?php echo $studio->name; ?></option>
				<?php } ?>
			</select>
		</div>
		<p class="clear"></p>
	</div>
<?php
	
	$total = 0;
	
	foreach ($events as $n => $event) {
		
		// Проверка на погашенные
		$paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` = {$event->id}");
		
		if ($paid >= $event->price) continue;
	
		$total += $eIncome->sum;
		
		switch($event->state) {
			
			case 4: $reason = "Отмена"; break;
			case 5: $reason = "Не пришли"; break;
			default: $reason = "";
		}
		
		// Аренда оборудования
		$eqs_price = 0;
		$eqs = json_decode($event->eqs);
		if (count($eqs)) {
			
			$hours = ($event->start < 23) ? $event->end - $event->start : 7;
			
			foreach ($eqs as $eq) {
				
				$price = 0;
				foreach ($equipments as $equipment) { if ($eq->id == $equipment->id) { $price = $equipment->price; break; } }
				
				$eqs_price += $price * $eq->count * $hours;
			}
		}
		
		
	
	?>
	<div class="item" id="item_<?php echo $event->id; ?>">
		
		<div class="id w50">
			<?php echo $n+1; ?>
		</div>
		
		<div class="name w200">
			<a title="Информация" href="<?php echo admin; ?>/table?rid=<?php echo $event->rid; ?>&date=<?php echo $event->date; ?>"><?php echo ($event->cid) ? $event->client_name : "Неопределенный"; ?><?php echo ($event->cid and $event->client_name == '') ? "Удаленный клиент [id:{$event->cid}]" : ""; ?></a>
			<br/>
			<span></span>
		</div>
		
		<div class="name w100"><?php echo getPrice($event->price - $eqs_price); ?></div>
		<div class="name w100"><?php echo $event->pay; ?></div>
		<!--
		<div class="name w100 edited">
			<input type="text" name="pay" value="<?php echo $event->pay; ?>">
		</div>
		-->
		<div class="name w100"><?php echo getPrice($event->debt - $eqs_price); ?></div>
		<div class="name w100"><?php echo $paid; ?></div>
		
		<div class="name w200"><?php echo date('d.m.Y', strtotime($event->date)); ?>, <?php echo $event->start; ?>:00 - <?php echo $event->end; ?>:00</div>
		<div class="name w200"><?php echo getStudioName($event->sid); ?></div>
		<div class="name w200"><?php echo $reason; ?></div>
		
		<div class="lActions">
			<input type="text" name="pay" value="" style="width: 50px; outline: 1px solid #ddd;" placeholder="0"/> руб
			<a class="edit" href="javascript:void(0);" onclick="savePay('<?php echo $event->id; ?>');">Внести</a>
			<!--<a class="delete" href="javascript:void(0);" onclick="deleteData('<?php echo $event->id; ?>');">Удалить</a>-->
		</div>
		
		<div class="clear"></div>
	</div>
	<?php
	
	}
	
?>
	<div class="total" style="display: none;">Итого: <b><?php echo getPrice($total); ?></b> руб.</div>
</div>

<div id="infoWindow">
	<div class="infoWindowOverlay">
	</div>
	<div id="equipmentInfo" class="infoWindowContent">
		<div id="ci"></div>
	</div>
</div>

<script type="text/javascript">

	$('#date').datepick($.extend({ 
		
		showAnim: 'slideDown',
		minDate: '<?php echo date('d.m.Y', strtotime('now - 1 month'));?>',
		maxDate: '<?php echo date('d.m.Y', strtotime('now'));?>'
	
	}, $.datepick.regional['ru']));
	
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
		
		var link = '<?php echo admin; ?>/debtors?f=1';
		
		if (date_d1) { link += '&date_d1='+date_d1+'&date_d2='+date_d2; }
		
		if (sid) { link += '&sid='+sid; }
		if (aid) { link += '&aid='+aid; }
		
		<?php if (getVar('orderby', 'get')) { ?>link += '&orderby=<?php echo getVar('orderby', 'get'); ?>';<?php } ?>
		<?php if (getVar('sort', 'get')) { ?>link += '&sort=<?php echo getVar('sort', 'get'); ?>';<?php } ?>
		
		location.assign(link);
	}
	
	function savePay(id) {
	
		if (confirm("Вы подтверждаете внесение платежа?")) {
			
			$.ajax({
				
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'debtors',
					'subtask': 	'savepay',
					'pay': 	$('#item_'+id+' input[name="pay"]').val(),
					'id':			id
					
				},
				dataType: 'script'
				
			});
		}
	}
	
	function deleteData(id) {
	
		if (confirm("Вы подтверждаете удаление?")) {
			
			$.ajax({
			
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'debtors',
					'subtask': 	'delete',
					'id':			id
					
				},
				dataType: 'script'
				
			});
		}
	}
	
</script>