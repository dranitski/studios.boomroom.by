<?php

	defined('access') or die ('no access');

	// Только для локальной разработки. На сервере используется secrets-release.php (не в git)
	// База данных
	define('_DB_HOST', 'localhost');
	define('_DB_NAME', 'studios_boomroom_site');
	define('_DB_USER', 'boomroom');
	define('_DB_PASS', 'boomroom');
	
	// Администратор
	define('apass', 'admin');
	// Панель
	define('apass_panel', 'panel');
	// Директор
	define('pass_director', 'director');

?>
