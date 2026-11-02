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
						<!--<option value="2">Информация</option>-->
						<!--<option value="3">Люди</option>-->
					</select>
					<?php } else { ?>
					Текст
					<input name="type" type="hidden" value="0"/>
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
			
			<div class="bHead"><div class="bName">Слайдер</div></div>
			<div id="slider">
			
				<?php
				if (count($params->slides)) {
				foreach ($params->slides as $n => $slide) {
				?>
				<div class="box">
					<div class="label">Слайд <?php echo $n+1; ?></div>
					<div class="field">
						<input name="slide_image[]" type="text" class="w200" value="<?php echo $slide->image; ?>" placeholder="файл, например: slide.jpg"/>
						<input name="slide_video[]" type="text" class="w200" value="<?php echo $slide->video; ?>" placeholder="код видео, например: f4K6ZxDwi34"/>
						<div class="lActions">
							<a class="delete" href="#" onclick="deleteSlide(this);">Удалить</a>
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
					<div class="label">Слайд 1</div>
					<div class="field">
						<input name="slide_image[]" type="text" class="w200" value="" placeholder="файл, например: slide.jpg"/>
						<input name="slide_video[]" type="text" class="w200" value="" placeholder="код видео, например: f4K6ZxDwi34"/>
						<div class="lActions">
							<a class="delete" href="#" onclick="deleteSlide(this);">Удалить</a>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<?php
				}
				?>
				<div class="pActions">
					<a class="add" href="#" onclick="addSlide();">Добавить</a>
				</div>
			</div>
			
			<div class="bHead"><div class="bName">Текст</div></div>
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
	
	function addSlide(){
		
		var num = Number($("#slider .box:last").index()) +2;
		
		$("#slider .box:last").clone().insertBefore("#slider .box:last");
		$("#slider .box:last input").val('');
		$("#slider .box:last .label").text('Слайд '+num);
	}
	
	function deleteSlide(e){
	
		if ($("#slider .box").size() != 1) {
		
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
			
			var slide_images = ''; $('#slider input[name^="slide_image"]').each(function(index) { if (index) { slide_images += '|'; } slide_images += $(this).val(); });
			var slide_videos = ''; $('#slider input[name^="slide_video"]').each(function(index) { if (index) { slide_videos += '|'; } slide_videos += $(this).val(); });
			
			$.ajax({
				
				url:		form.action,
				type:	form.method,
				data: {
					
					'ext':		'admin',
					'task':		'page',
					
					'lid':		form.lid.value,
					'pid':		form.pid.value,
					'type':		form.type.value,
					
					'slide_images':		slide_images,
					'slide_videos':		slide_videos,
					
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