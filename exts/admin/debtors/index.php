<?php
	
	defined('access') or die ('no access');
	
	if($id = getInt('id', 'get')) {
		
		include "events.php";
	}
	else {
	
	$orderby	= getVar('orderby', 'get');
	$sort		= getVar('sort', 'get');
	
	if (!$sort) { $sort =  'desc'; }
	if ($sort == 'asc') { $sort = 'desc'; } else { $sort = 'asc'; }
	
	switch ($orderby) {
		
		case 'id': $orderby = "c.`id` {$sort}"; break;
		case 'name': $orderby = "`name` {$sort}"; break;
		case 'debt': $orderby = "`debt` {$sort}"; break;
		default: $orderby = "`debt` DESC";
	}
	
	$date_d1 = getVar('date_d1', 'get'); if ($date_d1 == 'null') { $date_d1 = null; }
	$date_d2 = getVar('date_d2', 'get'); if ($date_d2 == 'null') { $date_d2 = null; }
	
	// ВРЕМЕННО | 03.08.2017 | удали все долги за исключением - июля месяца, а так же коллективов UsedFrendly, theburner
	// $TEMPWHERE = " AND (`date` >= '2017-07-01' OR `cid` = 12 OR `cid` = 21)";
	$TEMPWHERE = " AND `date` >= '2017-07-01'";
	
	$where = " WHERE c.`id` IN(SELECT `cid` FROM `events` WHERE `price` > `pay` AND `state` >= 2{$TEMPWHERE})";
	
	$where_events = " WHERE (`price` - `pay`) > 0 AND `cid` = c.`id` AND `state` > 1 {$TEMPWHERE}";
	
	$sql_date_d1 = date('Y-m-d', strtotime($date_d1));
	$sql_date_d2 = date('Y-m-d', strtotime($date_d2));
	
	if ($date_d1 and $date_d2) { $where_events .= " AND `date` BETWEEN '{$sql_date_d1}' AND '{$sql_date_d2}'"; }
	if ($date_d1 and $date_d2 == null) { $where_events .= " AND `date` >= '{$sql_date_d1}'"; }
	if ($date_d2 and $date_d1 == null) { $where_events .= " AND `date` <= '{$sql_date_d2}'"; }
	
	$sql = "SELECT c.*," .
			" (SELECT SUM(`price` - `pay`) FROM `events` {$where_events}) AS `debt`".
			// " `price` - `pay` AS `debt`".
			" FROM `clients` AS c".
			"{$where}".
			" ORDER BY {$orderby}";
	
	$debtors = loadObjectList($sql);
	
	// Групировка по клиентам
	// $debtors = array();
?>

<div class="block">
	
	<div class="bHead">
		<div class="bName">Должники</div>
	</div>
<?php

	
	$orderLink = admin."/debtors?f=1";
	
	$orderLink_id	= "{$orderLink}&orderby=id&sort={$sort}";
	$orderLink_name	= "{$orderLink}&orderby=name&sort={$sort}";
	$orderLink_debt	= "{$orderLink}&orderby=debt&sort={$sort}";

?>	
<div class="filter">
		<div><span>Дата </span> 
		с <input id="date_d1" type="text" value="<?php echo $date_d1; ?>" readonly="readonly" style="cursor: pointer;"/>
		по <input id="date_d2" type="text" value="<?php echo $date_d2; ?>" readonly="readonly" style="cursor: pointer;"/>
		</div>
		<div>
		<input type="button" class="apply" value="применить" onclick="filter();"/>
		<input type="button" class="reset" value="сбросить фильтр" style="margin-left: 10px;" onclick="location.assign('<?php echo admin; ?>/debtors');"/>
		</div>
		<div class="clear" style="float: none;"></div>
	</div>
	<div class="filter">
		<div class="w50">№</div>
		<div class="w300"><a href="<?php echo $orderLink_name; ?>">Клиент</a></div>
		<div class="w100"><a href="<?php echo $orderLink_debt; ?>">Сумма</a></div>
		<p class="clear"></p>
	</div>
<?php
	
	$total = 0;
	
if (cnt($debtors)) {
	
	$n = 1;
	foreach ($debtors as $debtor) {
		
		$paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` IN (SELECT `id` FROM `events` WHERE `cid` = {$debtor->id})");
		$debtor->debt -= $paid;
		
		if ($debtor->debt <= 0) continue;
		
		$total += round($debtor->debt, 2);
		
	?>
	<div class="item">
		
		<div class="id w50">
			<?php echo $n; ?>
		</div>
		
		<div class="name w300">
			<!--<a href="<?php echo admin; ?>/clients?edit=<?php echo $debtor->id; ?>" title="Информация о клиенте"><?php echo $debtor->name; ?></a>-->
			<a href="<?php echo admin; ?>/debtors?id=<?php echo $debtor->id; ?>"><?php echo $debtor->name; ?></a>
		</div>
		
		<div class="name w100">
			<?php echo round($debtor->debt, 2); ?>
		</div>
		
		<div class="lActions">
			<a class="edit" href="<?php echo admin; ?>/debtors?id=<?php echo $debtor->id; ?>">Подробнее</a>
		</div>
		
		<div class="clear"></div>
	</div>
	<?php
	
		$n++;
	}
}
?>
	<div class="item total" style="height: 10px; line-height: 10px; padding: 10px 0px; font-size: 16px;">Итого: <b><?php echo round($total, 2); ?></b> руб.</div>
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
<?php } ?>