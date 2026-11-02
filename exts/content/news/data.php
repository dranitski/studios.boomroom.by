<?php

	defined('access') or die ('no access');
	
	$sql = "SELECT n.*, l.`url` AS `link`" .
			" FROM `pages` AS n".
			" LEFT JOIN `links` AS l ON l.`id` = n.`lid`".
			" WHERE n.`type` = 1 AND `state` = 1".
			" ORDER BY n.`addDate` DESC";
	
	$news = loadObjectList($sql);
	
	// Устанавливаем HEAD
	$title = "Новости | BOOM";
	
	$meta['keywords']	=  "Новости компании BOOM";
	$meta['description']	=  "Новости компании BOOM";
?>