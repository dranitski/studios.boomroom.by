<?php
	
	defined('access') or die ('no access');
	
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
						<option value="1" selected>Новость</option>
						<!--<option value="2">Информация</option>-->
						<!--<option value="3">Люди</option>-->
					</select>
					<?php } else { ?>
					Новость
					<input name="type" type="hidden" value="1"/>
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
				<div class="label">Мета | Ключевые слова</div>
				<div class="field">
					<input name="m_keys" type="text" class="w400" value="<?php echo $ePage->m_keys; ?>"/>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="box">
				<div class="label">Мета | Описание</div>
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
			
			$.ajax({
				
				url:		form.action,
				type:	form.method,
				data: {
					
					'ext':		'admin',
					'task':		'page',
					
					'lid':		form.lid.value,
					'pid':		form.pid.value,
					'type':		form.type.value,
					
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
				
			})
			.done(function(data){
				
			});
		
		},
		invalidHandler: function() {
		
		}
	});
</script>