<?php
	
	defined('access') or die ('no access');
	
	// Обновление сессии
	if ($user->admin) {
		
		setcookie("user_admin", 1, time() + (authTime * 60), "/", ".".domain);
	}
	elseif ($user->admin_panel) {
		
		setcookie("user_admin_panel", 1, time() + (authTime * 60), "/", ".".domain);
	}
	else {
		
		setcookie("user_admin", '', time() - (authTime * 60), "/", ".".domain);
		setcookie("user_admin_panel", '', time() - (authTime * 60), "/", ".".domain);
	}
	
?>