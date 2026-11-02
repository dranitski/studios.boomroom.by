<?php
	
	defined('access') or die ('no access');
	
	function setUser($fields) {
		
		$authTime = authTime * 60;
		
		if (is_array($fields)) {
			
			$now = date('Y-m-d H:i:s');
			
			foreach ($fields as $k => $v) {
				
				setcookie("user_{$k}", $v, time() + $authTime, "/", ".".domain);
			}
			
			$cid	= $fields['id'];
			$time	= time();
			
			// Устанавливаем хеш
			$hash = md5(time().rand(1, 99999999));
			setcookie("user_hash", $hash, time() +$authTime, "/", ".".domain);
			
			$sql = "UPDATE `clients` SET `state` = 2 WHERE `id` = {$cid}";
			runSql($sql);
			
			$sql = "INSERT INTO `sessions` (`cid`, `hash`, `time`) VALUES ({$cid}, '{$hash}', '{$time}')";
			runSql($sql);
			
			return true;
		}
		else {
			
			return false;
		}
		
	}
	
	function updateUser() {
		
		$id			= getUser('id');
		$fields		= array('id', 'card', 'company', 'name', 'post', 'tel', 'email', 'dob', 'state', 'hash');
		$authTime	= authTime * 60;
		
		$time	= time();
		
		foreach ($fields as $v) {
				
			$value = getUser($v);
			
			setcookie("user_{$v}", $value, time() +$authTime, "/", ".".domain);
			
		}
		
		$hash =  getUser('hash');
		$sql = "UPDATE `sessions` SET `time` = '{$time}' WHERE `cid` = {$id} AND `hash` = '{$hash}'";
		runSql($sql);
		
	}
	
	function unsetUser($id) {
		
		$fields		= array('id', 'card', 'company', 'name', 'post', 'tel', 'email', 'dob', 'state', 'hash');
		$authTime	= authTime * 60;
		
		if ($id) {
			
			foreach ($fields as $v) {
			
				setcookie("user_{$v}", '', time() -$authTime, "/", ".".domain);
				
			}
			
			$sql = "UPDATE `clients` SET `state` = 1 WHERE `id` = {$id}";
			runSql($sql);
			
			$hash =  getUser('hash');
			$sql = "DELETE FROM `sessions` WHERE `cid` = {$id} AND `hash` = '{$hash}'";
			runSql($sql);
			
			// Удаляем данные из сессии
			
			// if(isset($_SESSION['main_url'])) {
				// unset($_SESSION['main_url']);
			// }
			
			// if(isset($_SESSION['user_type'])) {
				// unset($_SESSION['user_type']);
			// }
			
			return true;
		}
		else {
			
			return false;
		}
		
	}
	
	function getUser($field) {
		
		switch($field) {
			
			case 'id':
				
				$value = getInt("user_{$field}", 'COOKIE');
			
			break;
			
			default:
				$value = getVar("user_{$field}", 'COOKIE');
		}
		
		return $value;
		
	}
	
	class User {
		
	}
	
	// Создание объекта с данными пользователя
	$user = new User;
	
	
	$user->admin	= getUser('admin');
	$user->admin_panel	= getUser('admin_panel');
	$user->director	= getUser('director');
	
	// $user->id		= getUser('id');
	
	// $user->card	= getUser('card');
	// $user->company	= getUser('company');
	// $user->name	= getUser('name');
	// $user->email	= getUser('email');
	// $user->tel		= getUser('tel');
	// $user->dob	= getUser('dob');
	
?>