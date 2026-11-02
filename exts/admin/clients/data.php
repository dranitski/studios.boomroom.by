<?php

	defined('access') or die ('no access');
	
	$orderby	= getVar('orderby', 'get');
	$sort		= getVar('sort', 'get');
	
	if (!$sort) { $sort =  'desc'; }
	if ($sort == 'asc') { $sort = 'desc'; } else { $sort = 'asc'; }
	
	switch ($orderby) {
		
		case 'id':
		
			$orderby = "b.`id` {$sort}";
		break;
	
		case 'name':
		
			$orderby = "b.`name` {$sort}";
		break;
	
		case 'card':
		
			$orderby = "b.`card` {$sort}";
		break;
		
		default:
			
			$orderby = "b.`name` ASC";
	}
	
	$where = " WHERE `id` > 0";
	
	if ($type = getInt('type', 'get')) {
	
		$where .= " AND `type` = {$type}";
	}
	
	$sql = "SELECT b.* " .
			// " (SELECT `name` FROM `contractors` WHERE `id` = b.`cid`) AS `contractor`".
			" FROM `clients` AS b".
			"{$where}".
			" ORDER BY {$orderby}";
	
	$clients = loadObjectList($sql);
	
	// Устанавливаем HEAD
	$title = "Клиенты";
	
	$tags['script'][] = "/includes/js/jquery.validate.js";
	$tags['script'][] = "/includes/js/jquery.validate.additional.methods.js";
	$tags['script'][] = "/includes/js/jquery.form.min.js";
	
	$tags['script'][] = "/plugins/infowindow/script.js";
	$tags['style'][] = "/plugins/infowindow/style.css";
?>