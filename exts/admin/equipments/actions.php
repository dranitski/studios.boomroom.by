<?php
	
	defined('access') or die ('no access');
	
	$subtask = getVar('subtask', 'post');
	
	$admin = admin;
	
	switch ($subtask) {
	
		// Формируем данные
		case 'add': case 'edit':
		
			$data = getFormFields('data');
			
			$fields = array();
			foreach ($data as $n => $field) { $fields[$field->name] = $field->value; }
			$fields = (object) $fields; $fields->sid = (int) $fields->sid;
			
			switch($subtask) {
				
				case 'add':
					
					runSql("INSERT INTO `equipments` (`sid`, `name`, `price`, `count`) VALUES ({$fields->sid}, '{$fields->name}', '{$fields->price}', '{$fields->count}')");
					$id = insertId();
					toSyncItem(1, 'equipments', $id);
					
					$msg = "Оборудование добавлено";
					
				break;
				
				case 'edit':
					
					$id = getInt('id', 'post');
					
					runSql("UPDATE `equipments` SET `sid` = {$fields->sid}, `name` = '{$fields->name}', `price` = '{$fields->price}', `count` = '{$fields->count}' WHERE `id` = {$id}");
					toSyncItem(2, 'equipments', $id);
					
					$msg = "Информация обновлена";
					
				break;
			}
			
			$action = (getInt('act', 'post') == 1) ? "location.assign('{$admin}/equipments?edit={$id}');" : "location.assign('{$admin}/equipments');";
			
			// toSync(5);
			
			echo getMsg($msg, "ok", $action);
			
		break;
		
		case 'delete':
		
			$id = getInt('id', 'post');
			
			runSql("DELETE FROM `equipments` WHERE `id` = {$id}");
			toSyncItem(0, 'equipments', $id);
			
			// toSync(5);
			
			echo getMsg("Оборудование удалено", "ok", "location.assign('{$admin}/equipments');");
		
		break;
	}
	
	
?>