<?php

	defined('access') or die ('no access');
	
	set_time_limit(300);
	
	// Общие настройки
	define('siteOn', 1);
	define('siteName', 'BOOM.BY');
	define('debugMode', 0);
	define('domain', 'boomroom.by');
	
	// Время жизни сессии авторизации в мин
	define('authTime', 525600);
	
	define('path', $_SERVER['DOCUMENT_ROOT']);
	
	// Изображения
	define('allowedImg', 'jpg|jpeg|png|gif');
	
	// Секреты: secrets-local.php для локальной разработки, secrets-release.php на сервере
	if (file_exists(__DIR__.'/secrets-local.php')) {
		include __DIR__.'/secrets-local.php';
	} elseif (file_exists(__DIR__.'/secrets-release.php')) {
		include __DIR__.'/secrets-release.php';
	} else {
		die('no secrets file found');
	}
	
	// Платформа
	define('admin', '/admin');
	
	// Часовой пояс
	date_default_timezone_set('Europe/Minsk');
	
?>
