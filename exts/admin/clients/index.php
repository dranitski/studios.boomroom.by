<?php
	
	defined('access') or die ('no access');
		
?>

<?php

if(getVar('act', 'get') == 'add' or getInt('edit', 'get')) {
		
	$id	 = getInt('edit', 'get');
	
	if ($id) {
		
		$sql = "SELECT c.*" .
			" FROM `clients` AS c".
			" WHERE c.`id` = {$id}";
			
		$eClient	= loadObject($sql);
		
		$sql = "SELECT c.*" .
			" FROM `client_contacts` AS c".
			" WHERE c.`cid` = {$eClient->id}";
			
		$contacts = loadObjectList($sql);
		
		$eTitle = "Редактирование инфрмации о клиенте";
		
		$subtask = "edit";
		
		$count_events = loadResult("SELECT COUNT(*) FROM `events` WHERE `cid` = {$eClient->id} AND `state` = 2");
		
	} else {
		
		$eTitle = "Новый клиент";
		
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
		
		<form id="client" data-act="1">
			
			<div class="box">
				<div class="label">Тип клиента</div>
				<div class="field">
					<select name="type" class="w200">
						<option value="1"<?php echo ($eClient->type == 1) ? " selected" : ""; ?>>Группа</option>
						<option value="2"<?php echo ($eClient->type == 2) ? " selected" : ""; ?>>Музыкант</option>
						<option value="3"<?php echo ($eClient->type == 3) ? " selected" : ""; ?>>Админ</option>
					</select>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Название / Имя</div>
				<div class="field">
					<input name="name" type="text" class="w400" value="<?php echo $eClient->name; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Примечание</div>
				<div class="field">
					<textarea name="note" style="width: 400px; height: 100px;"><?php echo $eClient->note; ?></textarea>
				</div>
				<div class="clear"></div>
			</div>
			
			<?php for ($n = 0; $n < 5; $n++) { ?>
			<div class="box">
				<div class="label">Контактное лицо <?php echo $n+1; ?></div>
				<div class="field">
					<input name="contact_id[]" type="hidden" value="<?php echo $contacts[$n]->id; ?>" />
					<input name="contact_name[]" type="text" class="w200" value="<?php echo $contacts[$n]->name; ?>" placeholder="Имя"/>
					<input name="contact_phone[]" type="text" class="w200" value="<?php echo $contacts[$n]->phone; ?>" placeholder="Телефон"/>
					<input name="contact_email[]" type="text" class="w200" value="<?php echo $contacts[$n]->email; ?>" placeholder="Email"/>
					<input name="contact_vk_id[]" type="text" class="w200" value="<?php echo $contacts[$n]->vk_id; ?>" placeholder="VK id"/>
					<input name="contact_vk_addr[]" type="text" class="w200" value="<?php echo $contacts[$n]->vk_addr; ?>" placeholder="VK адрес"/>
				</div>
				<div class="clear"></div>
			</div>
			<?php } ?>
			
			<div class="box">
				<div class="label">Номер скидочной карты</div>
				<div class="field">
					<input name="card" type="text" class="w100" value="<?php echo $eClient->card; ?>"/>
				</div>
				<?php if ($eClient->id) { ?>
				<div class="level">
					<div class="step">Уровень: <b><?php echo getClientLevel($count_events); ?></b> <span>-<?php echo getClientLevel($count_events, 1); ?>%</span></div>
					<div class="events">
						Репетиций: <b><?php echo $count_events; ?></b><br/>
						До след. уровня: <b><?php echo getClientLevel($count_events, 2); ?></b>
					</div>
				</div>
				<?php } ?>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Персональная скидка</div>
				<div class="field">
					<input name="dis" type="text" class="w50" value="<?php echo $eClient->dis; ?>"/> %
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Пароль</div>
				<div class="field">
					<input name="pass" type="text" class="w100" value="<?php echo $eClient->pass; ?>"/><a id="generate-pass" href="javascript:void(0);" style="margin: 0 0 0 10px; color: #555;">Сгенерировать</a>
				</div>
				<div class="clear"></div>
			</div>
		
		</form>
</div>

<?php } ?>

<div class="block">
	
	<div class="bHead">
		<div class="bName">Клиенты</div>
		<div class="bActions">
			<a class="save" href="<?php echo admin; ?>/clients?act=add" >Добавить</a>
		</div>
	</div>
<?php

	
	$orderLink = admin."/clients?f=1";
	
	if ($type) { $orderLink .= "&type={$type}"; }
	
	$orderLink_id	= "{$orderLink}&orderby=id&sort={$sort}";
	$orderLink_name	= "{$orderLink}&orderby=name&sort={$sort}";
	$orderLink_card	= "{$orderLink}&orderby=card&sort={$sort}";

?>
	<div class="filter">
		<div class="w20"><a href="<?php echo $orderLink_id; ?>">Id</a></div>
		<div class="w300"><a href="<?php echo $orderLink_name; ?>">Название</a></div>
		<div class="w200">
			<a href="<?php echo $orderLink_card; ?>">№ карты</a>
			<input id="card" type="text" placeholder="0000" style="width: 50px; margin: 0 5px;"/> <a id="resetCard" href="javascript:void(0);">сброс</a>
		</div>
		<div class="w300">Контакты</div>
		<div style="float: right; margin: 1px 10px 0 0;">
			<select id="type" onchange="filterType(this);">
				<option value="">все типы</option>
				<option value="1"<?php echo ($type == 1) ? " selected" : ""; ?>>Группа</option>
				<option value="2"<?php echo ($type == 2) ? " selected" : ""; ?>>Музыкант</option>
				<option value="3"<?php echo ($type == 3) ? " selected" : ""; ?>>Админ</option>
			</select>
		</div>
		<p class="clear"></p>
	</div>
	
	<div id="clients">
<?php
	
	foreach ($clients as $n => $client) {
		
	
	?>
	<div class="item" id="item_<?php echo $client->id; ?>">
		
		<div class="id w20">
			<?php echo $client->id; ?>
		</div>
		
		<div class="name w300">
			<a title="Информация" href="<?php echo admin; ?>/clients?edit=<?php echo $client->id; ?>"><?php echo $client->name; ?></a>
			<!--<a title="Информация" href="javascript:void(0);" onclick="clientInfo(<?php echo $client->id; ?>);"><?php echo $client->name; ?></a>-->
			<br/>
			<span><?php echo getClientTypeName($client->type); ?></span>
		</div>
		
		<div class="name w200 card">
			<?php echo $client->card; ?>
		</div>
		
		
		<div class="name">
			<?php echo getClientContacts($client->contacts); ?>
		</div>
		
		<div class="lActions">
			<a class="edit" href="<?php echo admin; ?>/clients?edit=<?php echo $client->id; ?>">Изменить</a>
			<a class="delete" href="javascript:void(0);" onclick="deleteData('<?php echo $client->id; ?>');">Удалить</a>
		</div>
		
		<div class="clear"></div>
	</div>
	<?php
	
	}
	
?>
	</div>
</div>

<div id="infoWindow">
	<div class="infoWindowOverlay">
	</div>
	<div id="clientInfo" class="infoWindowContent">
		<div id="ci"></div>
	</div>
</div>

<script type="text/javascript">


	$('#generate-pass').on('click', function(){
		
		 var hash = Math.round(Math.random() * (9999 - 1111) + 1111);
		
		$('#client input[name="pass"]').val(hash);
	});
				

function filterLink(){
		
		var type = $('#type').val();
		
		var link = '<?php echo admin; ?>/clients?f=1';
		
		if (type) { link += '&type='+type; }
		
		<?php if (getVar('orderby', 'get')) { ?>link += '&orderby=<?php echo getVar('orderby', 'get'); ?>';<?php } ?>
		<?php if (getVar('sort', 'get')) { ?>link += '&sort=<?php echo getVar('sort', 'get'); ?>';<?php } ?>
		
		return link;
	}
	
	function filterType(e){
		
		window.location.assign(filterLink());
	}
	
	function closeForm(e){ location.assign('<?php echo admin; ?>/clients'); }
	
	function applyForm(e) {
	
		$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'applyForm(this);') }, 1000);
		$('#client').data('act', 1); $('#client').submit();
	}
	
	function saveForm(e) {
		
		$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'saveForm(this);') }, 1000);
		$('#client').data('act', 2); $('#client').submit();
	}
	
	function deleteData(id) {
	
		if (confirm("Вы подтверждаете удаление?")) {
			
			$.ajax({
			
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'clients',
					'subtask':	'delete',
					'id':			id
					
				},
				dataType: 'script'
				
			});
		}
	}
	
	$("#client").validate({
		errorClass: "error",
		rules: {
			name: {
				required: true
			}
		},
		submitHandler: function(form, event) {
			
			$.ajax({
				
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'clients',
					'subtask': 	'<?php echo $subtask; ?>',
					'id':			'<?php echo $id; ?>',
					'data':		JSON.stringify($('#client').serializeArray()),
					'act':		$('#client').data('act')
				},
				dataType: 'script'
				// dataType: 'html'
			}).done(function(data){
			
				// $('#main').append('<div id="temp"></div>'); $('#temp').html(data);
			});
		}
	});
	
	function clientInfo(id) {
		
		// $('#infoWindow').fadeIn();
		
		// $.ajax({
				
			// url:		'/action.php',
			// type:	'post',
			// data: {
				
				// 'ext':		'user',
				// 'task':		'client',
				// 'subtask':	'info',
				// 'id':			id
			// },
			// dataType: 'html'
		// }).done(function(data){ $('#ci').html(data); });
	}
	
	<?php if ($vid = getInt('view', 'get')) { ?>
	clientInfo(<?php echo $vid; ?>);
	<?php } ?>
	
	$('.infoWindowOverlay').click(function(){ $('#infoWindow').fadeOut(); });
	
	// Поиск по номеру карты
	$('#card').on('change keyup', function(){
		
		var name = $.trim($(this).val());
		
		if (name.length > 2){
		
			$('#clients .item').hide();
			$('#clients .item .card:Contains("' + name + '")').parent().show();
		}
		else {
			
			$('#clients .item').show();
		}
	});
	
	$('#resetCard').on('click', function(){
		
		$('#clients .item').show();
		$('#card').val('');
	});
	
</script>