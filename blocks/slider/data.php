<?php
	
	defined('access') or die ('no access');
	
	// Получаем глобальные данные
	$user	= $GLOBALS['user'];
	$view	= $GLOBALS['view'];	
	$page	= $GLOBALS['page'];
	$params = json_decode($page->params);
	
	// Получаем меню
	$xml = simplexml_load_file(path."/blocks/slider/admin.xml");
	
	$state = (int) $xml->params->state;
	
?>