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
						<option value="2" selected>Информация</option>
						<option value="3">Люди</option>
					</select>
					<?php } else { ?>
					Информация
					<input name="type" type="hidden" value="2"/>
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
				if (cnt($params->slides)) {
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
			
			<div class="bHead">
				<div class="bName">Текст</div>
				<div class="bActions">
					порядок <input name="ord_t" type="number" value="<?php echo $params->ord->ord_t ? $params->ord->ord_t : 1; ?>"/>
				</div>
			</div>
			<div id="text" class="box">
				<textarea class="editor" name="text"><?php echo stripslashes($ePage->text); ?></textarea>
			</div>
			
			<div class="bHead">
				<div class="bName">Галерея</div>
				<div class="bActions">
					порядок <input name="ord_g" type="number" value="<?php echo $params->ord->ord_g ? $params->ord->ord_g : 2; ?>"/>
				</div>
			</div>
			<div class="box">
				<div class="label">Папка с изображениями</div>
				<div class="field">
					<input name="gallery" type="text" class="w400" value="<?php echo $params->gallery; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="bHead">
				<div class="bName">Большое видео</div>
				<div class="bActions">
					порядок <input name="ord_vb" type="number" value="<?php echo $params->ord->ord_vb ? $params->ord->ord_vb : 3; ?>"/>
				</div>
			</div>
			<div class="box">
				<div class="label">Видео</div>
				<div class="field">
					<input name="vb_image" type="text" class="w200" value="<?php echo $params->vb_image; ?>" placeholder="файл, например: image.jpg"/>
						<input name="vb_video" type="text" class="w200" value="<?php echo $params->vb_video; ?>" placeholder="код видео, например: f4K6ZxDwi34"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="bHead">
				<div class="bName">3 видео</div>
				<div class="bActions">
					порядок <input name="ord_vs" type="number" value="<?php echo $params->ord->ord_vs ? $params->ord->ord_vs : 4; ?>"/>
				</div>
			</div>
			<div class="box">
				<div class="label">Видео 1</div>
				<div class="field">
					<input name="vs1_image" type="text" class="w200" value="<?php echo $params->vs1_image; ?>" placeholder="файл, например: image.jpg"/>
						<input name="vs1_video" type="text" class="w200" value="<?php echo $params->vs1_video; ?>" placeholder="код видео, например: f4K6ZxDwi34"/>
				</div>
				<div class="clear"></div>
			</div>
			<div class="box">
				<div class="label">Видео 2</div>
				<div class="field">
					<input name="vs2_image" type="text" class="w200" value="<?php echo $params->vs2_image; ?>" placeholder="файл, например: image.jpg"/>
						<input name="vs2_video" type="text" class="w200" value="<?php echo $params->vs2_video; ?>" placeholder="код видео, например: f4K6ZxDwi34"/>
				</div>
				<div class="clear"></div>
			</div>
			<div class="box">
				<div class="label">Видео 3</div>
				<div class="field">
					<input name="vs3_image" type="text" class="w200" value="<?php echo $params->vs3_image; ?>" placeholder="файл, например: image.jpg"/>
						<input name="vs3_video" type="text" class="w200" value="<?php echo $params->vs3_video; ?>" placeholder="код видео, например: f4K6ZxDwi34"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="bHead">
				<div class="bName">Дополнительная информация</div>
				<div class="bActions">
					порядок <input name="ord_e" type="number" value="<?php echo $params->ord->ord_e ? $params->ord->ord_e : 5; ?>"/>
				</div>
			</div>
			<div id="extra" class="box">
				<textarea class="editor" name="extra"><?php echo stripslashes($params->extra); ?></textarea>
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
			
			var ord = 'ord_t:'+$('input[name=ord_t]').val()+'|ord_g:'+$('input[name=ord_g]').val()+'|ord_vb:'+$('input[name=ord_vb]').val()+'|ord_vs:'+$('input[name=ord_vs]').val()+'|ord_e:'+$('input[name=ord_e]').val();
			
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
					
					'ord':		ord,
					
					'gallery':		form.gallery.value,
					'vb_image':	form.vb_image.value,
					'vb_video':	form.vb_video.value,
					'vs1_image':	form.vs1_image.value,
					'vs1_video':	form.vs1_video.value,
					'vs2_image':	form.vs2_image.value,
					'vs2_video':	form.vs2_video.value,
					'vs3_image':	form.vs3_image.value,
					'vs3_video':	form.vs3_video.value,
					'extra':		form.extra.value,
					
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