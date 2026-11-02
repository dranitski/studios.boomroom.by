<?php
	
	defined('access') or die ('no access');
		
?>

<?php

if(getVar('act', 'get') == 'add' or getInt('edit', 'get')) {
		
	$id	 = getInt('edit', 'get');
	
	if ($id) {
		
		$eQuipment	= loadObject("SELECT * FROM `equipments` WHERE `id` = {$id}");
		
		$eTitle = "Редактирование инфрмации об оборудовании";
		
		$subtask = "edit";
		
	} else {
		
		$eTitle = "Новое оборудование";
		
		$subtask = "add";
	}
?>

<div class="block Bl mt0">
						
	<div class="bHead">
		<div class="bName"><?php echo $eTitle; ?></div>
		<div class="bActions">
			<a class="apply" href="javascript:void(0);" onclick="applyForm(this);">Применить</a>
			<a class="save" href="javascript:void(0);" onclick="saveForm(this);">Сохранить</a>
			<a class="close" href="javascript:void(0);" onclick="closeForm();">Закрыть</a>
		</div>
	</div>
		
		<div class="clear"></div>
		
		<form id="equipment" data-act="1">
			
			<div class="box">
				<div class="label">Студия</div>
				<div class="field">
					<select name="sid" class="w100">
						<option value="1"<?php echo ($eQuipment->sid == 1) ? " selected" : ""; ?>>BooМ</option>
						<option value="3"<?php echo ($eQuipment->sid == 3) ? " selected" : ""; ?>>Jam</option>
						<option value="4"<?php echo ($eQuipment->sid == 4) ? " selected" : ""; ?>>Точка</option>
					</select>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Название</div>
				<div class="field">
					<input name="name" type="text" class="w400" value="<?php echo $eQuipment->name; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Стоимость аренды / час</div>
				<div class="field">
					<input name="price" type="text" class="w100"value="<?php echo $eQuipment->price; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Количество</div>
				<div class="field">
					<input name="count" type="text" class="w50" value="<?php echo $eQuipment->count; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
		
		</form>
</div>

<?php } ?>

<div class="block">
	
	<div class="bHead">
		<div class="bName">Оборудование</div>
		<div class="bActions">
			<a class="save" href="<?php echo admin; ?>/equipments?act=add" >Добавить</a>
		</div>
	</div>
<?php

	
	$orderLink = "/equipments?f=1";
	
	if ($type) { $orderLink .= "&type={$type}"; }
	
	$orderLink_id	= "{$orderLink}&orderby=id&sort={$sort}";
	$orderLink_name	= "{$orderLink}&orderby=name&sort={$sort}";

?>
	<div class="filter">
		<div class="w20"><a href="<?php echo $orderLink_id; ?>">Id</a></div>
		<div class="w200"><a href="<?php echo $orderLink_name; ?>">Название</a></div>
		<div class="w200">Стоимость</div>
		<div class="w200">Количество</div>
		<div style="float: right; margin: 1px 10px 0 0;">
			<select id="sid" onchange="filterSid(this);">
				<option value="">все студии</option>
				<option value="1"<?php echo ($sid == 1) ? " selected" : ""; ?>>BooМ</option>
				<option value="3"<?php echo ($sid == 3) ? " selected" : ""; ?>>Jam</option>
				<option value="4"<?php echo ($sid == 4) ? " selected" : ""; ?>>Точка</option>
			</select>
		</div>
		<p class="clear"></p>
	</div>
<?php
	
	foreach ($equipments as $n => $equipment) {
	
	?>
	<div class="item" id="item_<?php echo $equipment->id; ?>">
		
		<div class="id w20">
			<?php echo $equipment->id; ?>
		</div>
		
		<div class="name w200">
			<a title="Информация" href="javascript:void(0);" onclick="equipmentInfo(<?php echo $equipment->id; ?>);"><?php echo $equipment->name; ?></a>
			<br/>
			<span>
			<?php echo getStudioName($equipment->sid); ?>
			</span>
		</div>
		
		<div class="name w200"><?php echo $equipment->price; ?> руб.</div>
		
		<div class="name w200"><?php echo $equipment->count; ?> шт.</div>
		
		<div class="lActions">
			<a class="edit" href="javascript:void(0);" onclick="window.location.assign('<?php echo admin; ?>/equipments?edit=<?php echo $equipment->id; ?>');">Изменить</a>
			<a class="delete" href="javascript:void(0);" onclick="deleteData('<?php echo $equipment->id; ?>');">Удалить</a>
		</div>
		
		<div class="clear"></div>
	</div>
	<?php
	
	}
	
?>
</div>

<div id="infoWindow">
	<div class="infoWindowOverlay">
	</div>
	<div id="equipmentInfo" class="infoWindowContent">
		<div id="ci"></div>
	</div>
</div>

<script type="text/javascript">

function filterLink(){
		
		var sid = $('#sid').val();
		
		var link = '<?php echo admin; ?>/equipments?f=1';
		
		if (sid) { link += '&sid='+sid; }
		
		<?php if (getVar('orderby', 'get')) { ?>link += '&orderby=<?php echo getVar('orderby', 'get'); ?>';<?php } ?>
		<?php if (getVar('sort', 'get')) { ?>link += '&sort=<?php echo getVar('sort', 'get'); ?>';<?php } ?>
		
		return link;
	}
	
	function filterSid(e){
		
		window.location.assign(filterLink());
	}
	
	function closeForm(e){ window.location.assign('<?php echo admin; ?>/equipments'); }
	
	function applyForm(e) {
	
		$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'applyForm(this);') }, 1000);
		$('#equipment').data('act', 1); $('#equipment').submit();
	}
	
	function saveForm(e) {
		
		$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'saveForm(this);') }, 1000);
		$('#equipment').data('act', 2); $('#equipment').submit();
	}
	
	function deleteData(id) {
	
		if (confirm("Вы подтверждаете удаление?")) {
			
			$.ajax({
			
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'equipments',
					'subtask':	'delete',
					'id':			id
					
				},
				dataType: 'script'
				
			});
		}
	}
	
	$("#equipment").validate({
		errorClass: "error",
		rules: {
			name: {
				required: true
			},
			price: {
				required: true
			},
			count: {
				required: true
			}
		},
		submitHandler: function(form, event) {
			
			$.ajax({
				
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'equipments',
					'subtask': 	'<?php echo $subtask; ?>',
					'id':			'<?php echo $id; ?>',
					'data':		JSON.stringify($('#equipment').serializeArray()),
					'act':		$('#equipment').data('act')
				},
				dataType: 'script'
				// dataType: 'html'
			}).done(function(data){
			
				// $('#main').append('<div id="temp"></div>'); $('#temp').html(data);
			});
		}
	});
	
	function equipmentInfo(id) {
		
		// $('#infoWindow').fadeIn();
		
		// $.ajax({
				
			// url:		'/action.php',
			// type:	'post',
			// data: {
				
				// 'ext':		'user',
				// 'task':		'equipment',
				// 'subtask':	'info',
				// 'id':			id
			// },
			// dataType: 'html'
		// }).done(function(data){ $('#ci').html(data); });
	}
	
	<?php if ($vid = getInt('view', 'get')) { ?>
	equipmentInfo(<?php echo $vid; ?>);
	<?php } ?>
	
	$('.infoWindowOverlay').click(function(){ $('#infoWindow').fadeOut(); });
	
</script>