<?php

	define('access', 1);
	
	// Настройки
	include $_SERVER['DOCUMENT_ROOT'].'/config.php';
	// Подключение основных функций
	include path.'/functions/main.php';
	include path.'/functions/db.php';
	include path.'/functions/user.php';
	
	switch (getVar('task', 'post')) {
	
		case 'save':
		
			$state	= getInt('state', 'post');
			
			$r		= getInt('r', 'post');
			$bid	= getInt('bid', 'post');
			
			$new_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<data>\n\t<params>\n";
			
			$new_xml .= "\t\t<state>{$state}</state>\n";
			$new_xml .= "\t</params>\n</data>";
			
			file_put_contents("admin.xml", $new_xml);
			
			$a_link = $r ? "" : "?edit={$bid}";
			
			echo getMsg("Действие выполнено", "ok", "window.location.assign('/admin/blocks{$a_link}');");
		
		break;
	
	}
	
?>