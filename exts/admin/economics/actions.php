<?php
	
	defined('access') or die ('no access');
	
	$subtask = getVar('subtask', 'post');
	
	$admin = admin;
	
	switch ($subtask) {
	
		case 'expenses':
		
			$option = getVar('option', 'post');
			
			switch ($option) {
	
				// Формируем данные
				case 'add': case 'edit':
				
					$data = getFormFields('data');
					
					$fields = array();
					foreach ($data as $n => $field) { $fields[$field->name] = $field->value; }
					$fields = (object) $fields; $fields->sid = (int) $fields->sid; $fields->aid = (int) $fields->aid;
					
					$fields->date = date('Y-m-d', strtotime($fields->date));
					
					switch($option) {
						
						case 'add':
							
							runSql("INSERT INTO `expenses` (`sid`, `aid`, `type`, `comment`, `sum`, `date`, `created`) VALUES ({$fields->sid}, {$fields->aid}, '{$fields->type}', '{$fields->comment}', '{$fields->sum}', '{$fields->date}', NOW())");
							
							$id = insertId();
							
							$msg = "Запись добавлена";
							
						break;
						
						case 'edit':
							
							$id = getInt('id', 'post');
							
							runSql("UPDATE `expenses` SET `sid` = {$fields->sid}, `aid` = {$fields->aid}, `type` = '{$fields->type}', `comment` = '{$fields->comment}', `sum` = '{$fields->sum}', `date` = '{$fields->date}' WHERE `id` = {$id}");
							
							$msg = "Информация обновлена";
							
						break;
					}
					
					$action = (getInt('act', 'post') == 1) ? "location.assign('{$admin}/economics?type=1&edit={$id}');" : "location.assign('{$admin}/economics?type=1');";
					
					
					echo getMsg($msg, "ok", $action);
					
				break;
				
				case 'delete':
				
					$id = getInt('id', 'post');
					
					runSql("DELETE FROM `expenses` WHERE `id` = {$id}");
					
					
					echo getMsg("Запись удалена", "ok", "location.assign('{$admin}/economics?type=1');");
				
				break;
			
			}
			
		break;
	
		case 'incomes':
		
			$option = getVar('option', 'post');
			
			switch ($option) {
	
				// Формируем данные
				case 'add': case 'edit':
				
					$data = getFormFields('data');
					
					$fields = array();
					foreach ($data as $n => $field) { $fields[$field->name] = $field->value; }
					$fields = (object) $fields; $fields->sid = (int) $fields->sid; $fields->aid = (int) $fields->aid;
					
					$fields->date = date('Y-m-d', strtotime($fields->date));
					
					switch($option) {
						
						case 'add':
							
							runSql("INSERT INTO `incomes` (`sid`, `aid`, `name`, `sum`, `date`, `created`) VALUES ({$fields->sid}, {$fields->aid}, '{$fields->name}', '{$fields->sum}', '{$fields->date}', NOW())");
							
							$id = insertId();
							
							$msg = "Запись добавлена";
							
						break;
						
						case 'edit':
							
							$id = getInt('id', 'post');
							
							runSql("UPDATE `incomes` SET `sid` = {$fields->sid}, `aid` = {$fields->aid}, `name` = '{$fields->name}', `sum` = '{$fields->sum}', `date` = '{$fields->date}' WHERE `id` = {$id}");
							
							$msg = "Информация обновлена";
							
						break;
					}
					
					$action = (getInt('act', 'post') == 1) ? "location.assign('{$admin}/economics?type=2&edit={$id}');" : "location.assign('{$admin}/economics?type=2');";
					
					
					echo getMsg($msg, "ok", $action);
					
				break;
				
				case 'delete':
				
					$id = getInt('id', 'post');
					
					runSql("DELETE FROM `incomes` WHERE `id` = {$id}");
					
					
					echo getMsg("Запись удалена", "ok", "location.assign('{$admin}/economics?type=2');");
				
				break;
			
			}
			
		break;
	
	}
	
	
?>