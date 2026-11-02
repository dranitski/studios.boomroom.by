<?php
	
	define('access', 1);
	
	// Настройки
	include 'config.php';
	
	// Сессия нужна для сообщений redirect() и бронирования по карте
	session_start();
	
	if(siteOn) {
		
		// Подключение основных функций
		include path.'/functions/main.php';
		include path.'/functions/db.php';
		include path.'/functions/user.php';
		
		$ext	= getVar('ext', 'post');
		
		// Подключение контроллера
		if (preg_match('/^[a-z0-9_]+$/', $ext)) {
			
			if (file_exists(path."/exts/{$ext}/controller.php")) {
			
				include path."/exts/{$ext}/controller.php";
				
			}
			
		}
	}

?>