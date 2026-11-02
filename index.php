<?php

	define('access', 1);
	
	// Настройки
	include 'config.php';
	
	// Режим отладки
	if (debugMode) {
		$start_time = microtime(true);
	}
	
	// Начинаем сессию
	session_start();
	
	if(siteOn) {
		
		// Подключение основных функций
		include path.'/functions/main.php';
		include path.'/functions/db.php';
		include path.'/functions/user.php';
		
		// Подключение роутера
		include path.'/router.php';
		
		// Подключение данных
		if ($ext) {
		
			define('extPath', path."/exts/{$ext}");
		
			include extPath."/controller.php";
			
		}
		
		// Обновление сессии пользователя
		include path.'/functions/update.php';
		
		// Подключение шаблона
		if (!isset($notmpl)) {
		
			include path.'/tmpl/index.php';
		
		}
	}
	else {
	
		echo "Работа сайта временно приостановлена...";
		
	}
	
	if (debugMode) {
	
		// Время выполнения скрипта
		$exec_time = microtime(true) - $start_time;
		
		echo "<div id=\"debugMode\">";
		printf('Время выполнения: <b>%.4F</b> сек.', $exec_time);
		echo "<br/>Количество запросов к MySql: <b>{$countSqls}</b>";
		echo "</div>";
	 }

?>