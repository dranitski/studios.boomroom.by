<?php
	
	defined('access') or die ('no access');
	
	$sql = "SELECT n.*, l.`url` AS `link`" .
			" FROM `pages` AS n".
			" LEFT JOIN `links` AS l ON l.`id` = n.`lid`".
			" WHERE n.`type` = 1 AND `state` = 1".
			" ORDER BY n.`addDate` DESC";
	
	$events = loadObjectList($sql);
	
?>