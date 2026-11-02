<?php
	
	defined('access') or die ('no access');
	
	$orderby	= getVar('orderby', 'get');
	$sort		= getVar('sort', 'get');
	
	if (!$sort) { $sort =  'desc'; }
	if ($sort == 'asc') { $sort = 'desc'; } else { $sort = 'asc'; }
	
	switch ($orderby) {
		
		case 'id': $orderby = "a.`id` {$sort}"; break;
		case 'type': $orderby = "a.`type` {$sort}"; break;
		case 'sum': $orderby = "a.`sum` {$sort}"; break;
		case 'date': $orderby = "a.`date` {$sort}"; break;
		
		default: $orderby = "a.`created` DESC";
	}
	
	$date_d1 = getVar('date_d1', 'get'); if ($date_d1 == 'null') { $date_d1 = null; }
	$date_d2 = getVar('date_d2', 'get'); if ($date_d2 == 'null') { $date_d2 = null; }
	
	if (!$date_d1 and !$date_d2) {
		
		$date_d1 = date('d.m.Y');
		$date_d2 = date('d.m.Y');
	}
	
	$where = " WHERE `id` > 0";
	if ($sid = getInt('sid', 'get')) { $where .= " AND a.`sid` = {$sid}"; }
	if ($aid = getInt('aid', 'get')) { $where .= " AND a.`aid` = {$aid}"; }
	if ($_GET['this_type'] != null) { $this_type = getInt('this_type', 'get'); $where .= " AND a.`type` = {$this_type}"; }
	
	$sql_date_d1 = date('Y-m-d', strtotime($date_d1));
	$sql_date_d2 = date('Y-m-d', strtotime($date_d2));
	
	if ($date_d1 and $date_d2) { $where .= " AND a.`date` BETWEEN '{$sql_date_d1}' AND '{$sql_date_d2}'"; }
	if ($date_d1 and $date_d2 == null) { $where .= " AND a.`date` >= '{$sql_date_d1}'"; }
	if ($date_d2 and $date_d1 == null) { $where .= " AND a.`date` <= '{$sql_date_d2}'"; }
	
	$sql = "SELECT a.*," .
			" (SELECT `name` FROM `administrators` WHERE `id` = a.`aid`) AS `admin_name`".
			" FROM `expenses` AS a".
			"{$where}".
			" ORDER BY {$orderby}";
	
	$expenses = loadObjectList($sql);
	
	$studios = loadObjectList("SELECT * FROM `studios`");
	$admins = loadObjectList("SELECT * FROM `administrators`");
?>

<?php

if(getVar('act', 'get') == 'add' or getInt('edit', 'get')) {
		
	$id	 = getInt('edit', 'get');
	
	if ($id) {
		
		$eExpense	= loadObject("SELECT * FROM `expenses` WHERE `id` = {$id}");
		
		$eTitle = "Редактирование инфрмации о расходе";
		
		$subtask = "edit";
		
	} else {
		
		$eTitle = "Новая запись";
		
		$subtask = "add";
	}
?>

<div class="block Bl mt0" style="margin-top: 45px;">
						
	<div class="bHead">
		<div class="bName"><?php echo $eTitle; ?></div>
		<div class="bActions">
			<a class="apply" href="javascript:void(0);" onclick="applyForm(this);">Применить</a>
			<a class="save" href="javascript:void(0);" onclick="saveForm(this);">Сохранить</a>
			<a class="close" href="javascript:void(0);" onclick="closeForm();">Закрыть</a>
		</div>
	</div>
		
	<div class="clear"></div>
	
	<form id="expense" data-act="1">
		
		<div class="box">
			<div class="label">Студия</div>
			<div class="field">
				<select name="sid" class="w100" readonly >
					<option value="1"<?php echo ($eExpense->sid == 1) ? ' selected' : ''; ?>>BooM</option>
					<option value="3"<?php echo ($eExpense->sid == 3) ? ' selected' : ''; ?>>Jam</option>
				<?php /* foreach ($studios as $studio) { ?>
					<option value="<?php echo $studio->id; ?>"<?php echo ($eExpense->sid == $studio->id) ? " selected" : ""; ?>><?php echo $studio->name; ?></option>
				<?php } */ ?>
				</select>
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="box">
			<div class="label">Администратор</div>
			<div class="field">
				<select name="aid" class="w200">
					<option value="">Выбрать</option>
				<?php foreach ($admins as $admin) { ?>
					<option value="<?php echo $admin->id; ?>"<?php echo ($eExpense->aid == $admin->id) ? " selected" : ""; ?>><?php echo $admin->name; ?></option>
				<?php } ?>
				</select>
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="box">
			<div class="label">Тип</div>
			<div class="field">
				<select name="type" class="w200">
					<option value="0"<?php echo ($eExpense->type == 0) ? " selected" : ""; ?>>Прочие</option>
					<option value="1"<?php echo ($eExpense->type == 1) ? " selected" : ""; ?>>Заработная плата</option>
					<option value="2"<?php echo ($eExpense->type == 2) ? " selected" : ""; ?>>Мелко-бюджетный расход</option>
					<option value="3"<?php echo ($eExpense->type == 3) ? " selected" : ""; ?>>Барабанные расходы</option>
					<option value="4"<?php echo ($eExpense->type == 4) ? " selected" : ""; ?>>Транспортные расходы</option>
					<option value="5"<?php echo ($eExpense->type == 5) ? " selected" : ""; ?>>Строительные расходы</option>
					<option value="6"<?php echo ($eExpense->type == 6) ? " selected" : ""; ?>>Ремонты</option>
				</select>
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="box">
			<div class="label">Комментарий</div>
			<div class="field">
				<input name="comment" type="text" class="w400" value="<?php echo $eExpense->comment; ?>"/>
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="box">
			<div class="label">Сумма</div>
			<div class="field">
				<input name="sum" type="text" class="w100"value="<?php echo $eExpense->sum; ?>"/> руб.
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="box">
			<div class="label">Дата</div>
			<div class="field">
				<input id="date_" name="date" type="text" value="<?php echo ($eExpense->date) ? date('d.m.Y', strtotime($eExpense->date)) : date('d.m.Y'); ?>" readonly="readonly" style="cursor: pointer;" />
			</div>
			<div class="clear"></div>
		</div>
	</form>
</div>

<?php } ?>

<div class="block" style="margin-top: 45px;">
	
	<div class="bHead">
		<div class="bName">Расходы</div>
		<div class="bActions">
			<a class="save" href="<?php echo admin; ?>/economics?type=1&act=add" >Добавить</a>
		</div>
	</div>
<?php

	
	$orderLink = admin."/economics?type=1&f=1";
	
	if ($date_d1) { $orderLink .= "&date_d1={$date_d1}"; }
	if ($date_d2) { $orderLink .= "&date_d2={$date_d2}"; }
	
	$orderLink_id	= "{$orderLink}&orderby=id&sort={$sort}";
	$orderLink_name	= "{$orderLink}&orderby=type&sort={$sort}";
	$orderLink_sum	= "{$orderLink}&orderby=sum&sort={$sort}";
	$orderLink_date	= "{$orderLink}&orderby=date&sort={$sort}";

?>	
	<div class="filter">
		<div><span>Дата </span> 
		с <input id="date_d1" type="text" value="<?php echo $date_d1; ?>" readonly="readonly" style="cursor: pointer;"/>
		по <input id="date_d2" type="text" value="<?php echo $date_d2; ?>" readonly="readonly" style="cursor: pointer;"/>
		</div>
		<div>
		<input type="button" class="apply" value="применить" onclick="filter();"/>
		<input type="button" class="reset" value="сбросить фильтр" style="margin-left: 10px;" onclick="location.assign('<?php echo admin; ?>/economics?type=1&f=0');"/>
		</div>
		<div class="clear" style="float: none;"></div>
	</div>

	<div class="filter">
		<div class="w20"><a href="<?php echo $orderLink_id; ?>">Id</a></div>
		<div class="w200"><a href="<?php echo $orderLink_name; ?>">Тип расхода</a></div>
		<div class="w200"><a href="<?php echo $orderLink_sum; ?>">Сумма</a></div>
		<div class="w200"><a href="<?php echo $orderLink_date; ?>">Дата</a></div>
		<div class="w200">Администратор</div>
		<div class="w100">Студия</div>
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
				<option value="">все админы</option>
				<?php foreach ($admins as $admin) { ?>
					<option value="<?php echo $admin->id; ?>"<?php echo ($aid == $admin->id) ? " selected" : ""; ?>><?php echo $admin->name; ?></option>
				<?php } ?>
			</select>
		</div>
		<div style="float: right; margin: 1px 10px 0 0;">
			<select id="this_type" onchange="filter();">
				<option value="">все типы</option>
				<option value="0"<?php echo ($_GET['this_type'] != null and $this_type == 0) ? " selected" : ""; ?>>Прочие</option>
				<option value="1"<?php echo ($this_type == 1) ? " selected" : ""; ?>>Заработная плата</option>
				<option value="2"<?php echo ($this_type == 2) ? " selected" : ""; ?>>Мелко-бюджетный расход</option>
				<option value="3"<?php echo ($this_type == 3) ? " selected" : ""; ?>>Барабанные расходы</option>
				<option value="4"<?php echo ($this_type == 4) ? " selected" : ""; ?>>Транспортные расходы</option>
				<option value="5"<?php echo ($this_type == 5) ? " selected" : ""; ?>>Строительные расходы</option>
				<option value="6"<?php echo ($this_type == 6) ? " selected" : ""; ?>>Ремонты</option>
			</select>
		</div>
		<p class="clear"></p>
	</div>
<?php
	
	$total = 0;
	
	foreach ($expenses as $n => $eExpense) {
	
		$total += $eExpense->sum;
	
	?>
	<div class="item" id="item_<?php echo $eExpense->id; ?>">
		
		<div class="id w20">
			<?php echo $eExpense->id; ?>
		</div>
		
		<div class="name w200">
			<a title="Информация" href="<?php echo admin; ?>/economics?type=1&edit=<?php echo $eExpense->id; ?>"><?php echo getExpenseTypeName($eExpense->type); ?></a>
			<br/>
			<span><?php echo $eExpense->comment; ?></span>
		</div>
		
		<div class="name w200"><?php echo getPrice($eExpense->sum); ?> руб.</div>
		
		<div class="name w200"><?php echo date('d.m.Y', strtotime($eExpense->date)); ?></div>
		<div class="name w200"><?php echo $eExpense->admin_name; ?></div>
		<div class="name w100"><?php echo getStudioName($eExpense->sid); ?></div>
		
		<div class="lActions">
			<a class="edit" href="<?php echo admin; ?>/economics?type=1&edit=<?php echo $eExpense->id; ?>">Изменить</a>
			<a class="delete" href="javascript:void(0);" onclick="deleteData('<?php echo $eExpense->id; ?>');">Удалить</a>
		</div>
		
		<div class="clear"></div>
	</div>
	<?php
	
	}
	
?>
	<div class="total">Итого: <b><?php echo getPrice($total); ?></b> руб.</div>
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
		// minDate: '<?php echo date('d.m.Y', strtotime('now - 12 hour'));?>',
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
		var this_type = $('#this_type').val();
		
		var link = '<?php echo admin; ?>/economics?type=1&f=1';
		
		if (date_d1) { link += '&date_d1='+date_d1+'&date_d2='+date_d2; }
		
		if (sid) { link += '&sid='+sid; }
		if (aid) { link += '&aid='+aid; }
		if (this_type) { link += '&this_type='+this_type; }
		
		<?php if (getVar('orderby', 'get')) { ?>link += '&orderby=<?php echo getVar('orderby', 'get'); ?>';<?php } ?>
		<?php if (getVar('sort', 'get')) { ?>link += '&sort=<?php echo getVar('sort', 'get'); ?>';<?php } ?>
		
		location.assign(link);
	}
	
	function closeForm(e){ window.location.assign('<?php echo admin; ?>/economics'); }
	
	function applyForm(e) {
	
		$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'applyForm(this);') }, 1000);
		$('#expense').data('act', 1); $('#expense').submit();
	}
	
	function saveForm(e) {
		
		$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'saveForm(this);') }, 1000);
		$('#expense').data('act', 2); $('#expense').submit();
	}
	
	function deleteData(id) {
	
		if (confirm("Вы подтверждаете удаление?")) {
			
			$.ajax({
			
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'economics',
					'subtask': 	'expenses',
					'option':	'delete',
					'id':			id
					
				},
				dataType: 'script'
				
			});
		}
	}
	
	$("#expense").validate({
		errorClass: "error",
		rules: {
			aid: { required: true },
			name: { required: true },
			sum: { required: true },
			date: { required: true }
		},
		submitHandler: function(form, event) {
			
			$.ajax({
				
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'economics',
					'subtask': 	'expenses',
					'option': 	'<?php echo ($id) ? "edit" : "add"; ?>',
					'id':			'<?php echo ($id) ? $id : 0; ?>',
					'data':		JSON.stringify($('#expense').serializeArray()),
					'act':		$('#expense').data('act')
				},
				dataType: 'script'
				// dataType: 'html'
			}).done(function(data){
			
				// $('#main').append('<div id="temp"></div>'); $('#temp').html(data);
			});
		}
	});
	
	$('.infoWindowOverlay').click(function(){ $('#infoWindow').fadeOut(); });
	
</script>