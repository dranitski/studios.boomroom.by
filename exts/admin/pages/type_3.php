<?php
	
	defined('access') or die ('no access');
	
	$params = json_decode($ePage->params);
	
?>

<div class="block Bl mt0">
						
	<div class="bHead">
		<div class="bName"><?php echo $eTitle; ?></div>
		<div class="bActions">
			<a class="apply" href="#" onclick="applyPage();">Применить</a>
			<a class="save" href="#" onclick="savePage();">Сохранить</a>
			<a class="close" href="#" onclick="closeForm();">Закрыть</a>
		</div>
	</div>
		
		<div class="clear"></div>
		
		<form name="page" id="page" action="/action.php" method="post">
			
			<div class="box">
				<div class="label">Состояние</div>
				<div class="field">
					
					<div class="param">
						<label for="state">Опубликовано</label>
						<input id="state" name="state" type="checkbox" <?php if ($ePage->state) { ?>checked<?php }?> />
					</div>
					
				</div>
			
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Тип</div>
				<div class="field">
					<?php if (!$id) { ?>
					<select id="type" name="type" onchange="setType();">
						<option value="0">Текст</option>
						<option value="1">Новость</option>
						<option value="2">Информация</option>
						<option value="3" selected>Люди</option>
					</select>
					<?php } else { ?>
					Информация
					<input name="type" type="hidden" value="3"/>
					<?php } ?>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Название</div>
				<div class="field">
					<input name="name" type="text" class="w400" value="<?php echo $ePage->name; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Ссылка</div>
				<div class="field">
					<input name="link" type="text" class="w400" value="<?php echo $ePage->link; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Родитель</div>
				<div class="field">
					<select name="pid" class="w300">
						<option value="0">Нет</option>
					<?php foreach ($pages as $page) { if ($page->id == $ePage->id) continue; ?>
						<option value="<?php echo $page->id; ?>" <?php echo ($page->id == $ePage->pid) ? " selected" : ""; ?>><?php echo ($page->pid) ? " - " : ""; ?><?php echo $page->name; ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="clear"></div>
			</div>
			
			
			<div class="bHead">
				<div class="bName">Люди</div>
				<div class="bActions">
					порядок <input name="ord_p" type="number" value="<?php echo $params->ord->ord_p ? $params->ord->ord_p : 1; ?>"/>
				</div>
			</div>
			<div id="persons">
			
				<?php
				if (cnt($params->persons)) {
				foreach ($params->persons as $n => $person) {
				?>
				<div class="box">
					<div class="label">Человек <?php echo $n+1; ?></div>
					<div class="field">
						<div class="sublabel">Имя</div>
						<div class="subfield">
							<input name="person_name[]" type="text" class="w300" value="<?php echo $person->name; ?>" placeholder="Имя"/>
						</div><div class="clear"></div>
						<div class="sublabel">Вступление</div>
						<div class="subfield">
							<input name="person_intro[]" type="text" class="w300" value="<?php echo $person->intro; ?>" placeholder="Краткая информация"/>
						</div><div class="clear"></div>
						<div class="sublabel">Фото</div>
						<div class="subfield">
							<input name="person_foto[]" type="text" class="w300" value="<?php echo $person->foto; ?>" placeholder="файл, например: foto.jpg"/>
						</div><div class="clear"></div>
						<div class="sublabel">Видео</div>
						<div class="subfield">
							<input name="person_image[]" type="text" class="w200" value="<?php echo $person->image; ?>" placeholder="файл, например: foto.jpg"/>
							<input name="person_video[]" type="text" class="w200" value="<?php echo $person->video; ?>" placeholder="код видео, например: f4K6ZxDwi34"/>
						</div><div class="clear"></div>
						<div class="sublabel">Описание</div>
						<div class="subfield">
							<textarea name="person_desc[]"><?php echo $person->desc; ?></textarea>
						</div><div class="clear"></div>
						<div class="lActions">
							<a class="delete" href="#" onclick="deletePerson(this);">Удалить</a>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<?php
				}
				}
				else {
				?>
				<div class="box">
					<div class="label">Человек 1</div>
					<div class="field">
						<div class="sublabel">Имя</div>
						<div class="subfield">
							<input name="person_name[]" type="text" class="w300" value="" placeholder="Имя"/>
						</div><div class="clear"></div>
						<div class="sublabel">Вступление</div>
						<div class="subfield">
							<input name="person_intro[]" type="text" class="w300" value="" placeholder="Краткая информация"/>
						</div><div class="clear"></div>
						<div class="sublabel">Фото</div>
						<div class="subfield">
							<input name="person_foto[]" type="text" class="w300" value="" placeholder="файл, например: foto.jpg"/>
						</div><div class="clear"></div>
						<div class="sublabel">Видео</div>
						<div class="subfield">
							<input name="person_image[]" type="text" class="w200" value="" placeholder="файл, например: foto.jpg"/>
							<input name="person_video[]" type="text" class="w200" value="" placeholder="код видео, например: f4K6ZxDwi34"/>
						</div><div class="clear"></div>
						<div class="sublabel">Описание</div>
						<div class="subfield">
							<textarea name="person_desc[]"></textarea>
						</div><div class="clear"></div>
						<div class="lActions">
							<a class="delete" href="#" onclick="deletePerson(this);">Удалить</a>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<?php
				}
				?>
				<div class="pActions">
					<a class="add" href="javascript:void(0);" onclick="addPerson();">Добавить</a>
				</div>
			</div>
			
			
			
			<div class="bHead">
				<div class="bName">Текст</div>
				<div class="bActions">
					порядок <input name="ord_t" type="number" value="<?php echo $params->ord->ord_t ? $params->ord->ord_t : 2; ?>"/>
				</div>
			</div>
			<div id="text" class="box">
				<textarea class="editor" name="text"><?php echo stripslashes($ePage->text); ?></textarea>
			</div>
			
			<div class="bHead"><div class="bName">Мета</div></div>
			<div class="box">
				<div class="label">Заголовок</div>
				<div class="field">
					<input name="title" type="text" class="w400" value="<?php echo $ePage->title; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Ключевые слова</div>
				<div class="field">
					<input name="m_keys" type="text" class="w400" value="<?php echo $ePage->m_keys; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Описание</div>
				<div class="field">
					<input name="m_desc" type="text" class="w400" value="<?php echo $ePage->m_desc; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<input name="lid" type="hidden" value="<?php echo $ePage->lid; ?>"/>
			<input name="id" type="hidden" value="<?php echo $ePage->id; ?>"/>
			
			<input name="act" type="hidden" value="1"/>
		
		</form>
</div>

<script type="text/javascript">
	
	function addPerson(){
		
		var num = Number($("#persons .box:last").index()) +2;
		
		$("#persons .box:last").clone().insertBefore("#persons .box:last");
		$("#persons .box:last input").val('');
		$("#persons .box:last textarea").text('');
		$("#persons .box:last .label").text('Человек '+num);
	}
	
	function deletePerson(e){
	
		if ($("#persons .box").size() != 1) {
		
			if (confirm("Вы подтверждаете удаление?")) {
				
				$(e).parent().parent().parent().remove();
			}
		}
	}

	$("#page").validate({
		errorClass: "fError",
		rules: {
			name: {
				required: true
			},
			link: {
				required: true
			}
		},
		submitHandler: function(form) {
			
			event.preventDefault();
			
			var person_names = ''; $('#persons input[name^="person_name"]').each(function(index) { if (index) { person_names += '|'; } person_names += $(this).val(); });
			var person_intros = ''; $('#persons input[name^="person_intro"]').each(function(index) { if (index) { person_intros += '|'; } person_intros += $(this).val(); });
			var person_fotos = ''; $('#persons input[name^="person_foto"]').each(function(index) { if (index) { person_fotos += '|'; } person_fotos += $(this).val(); });
			var person_images = ''; $('#persons input[name^="person_image"]').each(function(index) { if (index) { person_images += '|'; } person_images += $(this).val(); });
			var person_videos = ''; $('#persons input[name^="person_video"]').each(function(index) { if (index) { person_videos += '|'; } person_videos += $(this).val(); });
			var person_descs = ''; $('#persons textarea[name^="person_desc"]').each(function(index) { if (index) { person_descs += '|'; } person_descs += $(this).val(); });
			
			var ord = 'ord_p:'+$('input[name=ord_p]').val()+'|ord_t:'+$('input[name=ord_t]').val();
			
			$.ajax({
				
				url:		form.action,
				type:	form.method,
				data: {
					
					'ext':		'admin',
					'task':		'page',
					
					'lid':		form.lid.value,
					'pid':		form.pid.value,
					'type':		form.type.value,
					
					'person_names':		person_names,
					'person_intros':		person_intros,
					'person_fotos':		person_fotos,
					'person_images':		person_images,
					'person_videos':		person_videos,
					'person_descs':		person_descs,
					
					'ord':		ord,
					
					'name':		form.name.value,
					'text':		form.text.value,
					
					'title':		form.title.value,
					'm_keys':	form.m_keys.value,
					'm_desc':	form.m_desc.value,
					
					'state':		form.state.value,
					
					'link':		form.link.value,
					
					'id':			form.id.value,
					
					'act':		form.act.value
					
				},
				dataType: 'script'
			});
		}
	});
</script>