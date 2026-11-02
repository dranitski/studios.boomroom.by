<?php
	
	defined('access') or die ('no access');
	
	$subtask = getVar('subtask', 'post');
	$admin = admin;
	
	switch ($subtask) {
	
		// Формируем данные
		case 'add': case 'edit':
		
			$json = getVar('data', 'post', 0, 0);
			$data = json_decode(stripslashes($json), false);
			
			$fields = array();
			$contacts = array();
							
			foreach ($data as $n => $field) {
			
				if (strpos($field->name, "contact") !== false) {
					
					$var = str_replace(array('contact_', '[]'), "", $field->name); $var = "contact_{$var}s"; ${$var}[] = $field->value;
				}
				else {
					
					$fields[$field->name] = $field->value;
				}
			}
			
			$fields = (object) $fields;
			
			foreach ($contact_ids as $n => $id) {
				
				$contacts[] = (object) array('id' => $id, 'name' => $contact_names[$n], 'phone' => $contact_phones[$n], 'email' => $contact_emails[$n], 'vk_id' => $contact_vk_ids[$n], 'vk_addr' => $contact_vk_addrs[$n]);
			}
			
			// Информация о клиенте
			switch($subtask) {
				
				case 'add':
					
					runSql("INSERT INTO `clients` (`type`, `name`, `note`, `card`, `pass`, `dis`) VALUES ({$fields->type}, '{$fields->name}', '{$fields->note}', '{$fields->card}', '{$fields->pass}', '{$fields->dis}')");
					
					$id = insertId();
					toSyncItem(1, 'clients', $id);
					
					$msg = "Клиент добавлен";
					
				break;
				
				case 'edit':
					
					$id = getInt('id', 'post');
					
					runSql("UPDATE `clients` SET `type` = {$fields->type}, `name` = '{$fields->name}', `note` = '{$fields->note}', `card` = '{$fields->card}', `pass` = '{$fields->pass}', `dis` = '{$fields->dis}' WHERE `id` = {$id}");
					toSyncItem(2, 'clients', $id);
					
					$msg = "Информация обновлена";
					
				break;
			}
			
			// Контакты
			if (count($contacts) and $id) {
			
				foreach ($contacts as $contact) {
				
					if ($contact->id) {
						
						if ($contact->name == '' and $contact->phone == '' and $contact->email == '' and $contact->vk_id == '' and $contact->vk_addr == '') {
							
							runSql("DELETE FROM `client_contacts` WHERE `id` = {$contact->id}");
							toSyncItem(0, 'client_contacts', $contact->id);
						}
						else{
							
							runSql("UPDATE `client_contacts` SET `name` = '{$contact->name}', `phone` = '{$contact->phone}', `email` = '{$contact->email}', `vk_id` = '{$contact->vk_id}', `vk_addr` = '{$contact->vk_addr}' WHERE `id` = {$contact->id}");
							toSyncItem(2, 'client_contacts', $contact->id);
						}
					}
					else {
						
						if ($contact->name or $contact->phone or $contact->email) {
							
							runSql("INSERT INTO `client_contacts` (`cid`, `name`, `phone`, `email`, `vk_id`, `vk_addr`) VALUES ({$id}, '{$contact->name}', '{$contact->phone}', '{$contact->email}', '{$contact->vk_id}', '{$contact->vk_addr}')"); $contact_id = insertId();
							toSyncItem(1, 'client_contacts', $contact_id);
						}
					}
				}
			}
			
			$contacts2 = [];
			foreach ($contacts as $contact) {
				
				if ($contact->name or $contact->phone or $contact->email or $contact->vk_id or $contact->vk_addr) {
					
					$contacts2[] = (object)[
						'name' => $contact->name,
						'phone' => $contact->phone,
						'email' => $contact->email,
						'vk_id' => $contact->vk_id,
						'vk_addr' => $contact->vk_addr
					];
				}
			}
			
			$contacts2 = json_encode($contacts2, JSON_UNESCAPED_UNICODE);
			
			runSql("UPDATE `clients` SET `contacts` = '{$contacts2}' WHERE `id` = {$id}");
			
			$action = (getInt('act', 'post') == 1) ? "location.assign('{$admin}/clients?edit={$id}');" : "location.assign('{$admin}/clients');";
			
			// toSync(4);
			
			echo getMsg($msg, "ok", $action);
			
		break;
		
		case 'delete':
		
			$id = getInt('id', 'post');
			$contact_ids = loadResultArray("SELECT `id` FROM `client_contacts` WHERE `cid` = {$id}");
			
			runSql("DELETE FROM `clients` WHERE `id` = {$id}");
			runSql("DELETE FROM `client_contacts` WHERE `cid` = {$id}");
			toSyncItem(0, 'clients', $id);
			if (count($contact_ids)) { foreach($contact_ids as $contact_id) { toSyncItem(0, 'client_contacts', $contact_id); } }
			// toSync(4);
			
			echo getMsg("Группа удалена", "ok", "location.assign('{$admin}/clients');");
		
		break;
	}
	
	
?>