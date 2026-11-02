<?php
	
	defined('access') or die ('no access');
	
	$subtask = getVar('subtask', 'post');
	$option = getVar('option', 'post');
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	
	switch ($subtask) {
	
		case 'template':
		
			switch ($option) {
	
				case 'get':
					
					$id = getInt('id', 'post');
					$rid = getInt('rid', 'post');
					$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
					$room->min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
					$date = getVar('date', 'post');
					$start = getInt('start', 'post');
					
					$day = date('w', strtotime($date));
					$dayWeek = getDayWeek($day, true);
					
					$weekly = true;
					
					// Выбранное окончания
					$min = ($start < 23) ? $start+1 : 0;
					$max = ($start < 23) ? 23 : 6;
					$end = ($start < 23) ? $start+2 : 6;
					
					if ($template = loadObject("SELECT * FROM `event_templates` WHERE `id` = {$id}")) {
						
						$end = $template->end;
						
						$weekly = ($template->date != '0000-00-00') ? false : true;
						
						$price = json_decode($template->price);
					}
					
					$templates = loadObjectList("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND `day` = {$day} AND `start` = {$start} ORDER BY `date` ASC");
					
					// echo 
				
					?>
					<div class="title">Настройка шаблона</div>
					<?php if (cnt($templates) > 1) { ?>
					<div class="templates">
					Варианты:
					<select onchange="changeTemplate(this);">
					<?php foreach($templates as $t) { ?>
						<option value="<?php echo $t->id;  ?>" <?php echo ($t->id == $id) ? "selected" : ""; ?> data-date="<?php echo $t->date; ?>"><?php echo ($t->date != '0000-00-00') ? date('d.m.Y', strtotime($t->date)) : "Еженедельно";  ?>, <?php echo $t->start; ?>:<?php echo $room->min; ?> - <?php echo $t->end; ?>:<?php echo $room->min; ?></option>
					<?php } ?>
					</select>
					</div>
					<?php } ?>
					<div class="clear"></div>
					<div class="datetime">
						<div class="date"><b><?php echo date('d.m.Y', strtotime($date)); ?></b>, <?php echo $dayWeek; ?></div>
						<div class="time">
							<?php echo $start; ?>:<?php echo $room->min; ?> -
							<select name="end">
								<?php echo getTimesList($min, $max, 'option', $end, $room->min); ?>
							</select>
						</div>
						<div class="clear"></div>
					</div>
					<div class="weekly">
						<label><input name="weekly" type="checkbox" value="1" <?php echo ($weekly) ? "checked" : ""; ?>/> Еженедельно </label>
					</div>
					<div class="price">
						<p>Цены</p>
						<label><span>Базовая </span><input name="price_0" type="text" value="<?php echo $price[0]; ?>" placeholder="0"/> руб.</label>
						<label><span>1 музыкант </span><input name="price_1" type="text" value="<?php echo $price[1]; ?>" placeholder="0"/> руб.</label>
						<label><span>2 музыканта </span><input name="price_2" type="text" value="<?php echo $price[2]; ?>" placeholder="0"/> руб.</label>
					</div>
					<?php if ($user->director) { ?>
					<div class="actions">
						<p class="save" onclick="saveTemplate(this);">Сохранить</p>
						<?php if ($id) { ?><p class="delete" onclick="deleteTemplate(this);">Удалить</p><?php } ?>
						<div class="clear"></div>
					</div>
					<?php } ?>
					<?php
				
				break;
				
				case 'save':
				
					$id = getInt('id', 'post');
					$rid = getInt('rid', 'post');
					$date = getVar('date', 'post');
					$start = getInt('start', 'post');
					$end = getInt('end', 'post');
					$weekly = getInt('weekly', 'post');
					$price_0 = getVar('price_0', 'post');
					$price_1 = getVar('price_1', 'post');
					$price_2 = getVar('price_2', 'post');
					
					$day = date('w', strtotime($date));
					
					$date = ($weekly) ? '0000-00-00' : $date;
					
					$price = json_encode(array($price_0, $price_1, $price_2));
					
					$update = ($id) ? true : false;
					
					if ($has_id = loadResult("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND `date` = '{$date}' AND `day` = {$day} AND `start` = {$start} ORDER BY `id` ASC")) {
					
						$id = $has_id;
					}
					else {
						
						$update = false;
					}
					
					if ($update) {
						
						runSql("UPDATE `event_templates` SET `date` = '{$date}', `end` = {$end}, `price` = '{$price}' WHERE `id` = {$id}");
					}
					else {
						
						runSql("INSERT INTO `event_templates` (`rid`, `date`, `day`, `start`, `end`, `price`) VALUES ({$rid}, '{$date}', {$day}, {$start}, {$end}, '{$price}')");
						$id = insertId();
					}
					
				
				break;
				
				case 'delete':
					
					$id = getInt('id', 'post');
				
					runSql("DELETE FROM `event_templates` WHERE `id` = {$id}");
					
				
				break;
			}
		
		break;
	}
	
	
?>