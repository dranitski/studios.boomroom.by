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
		
		default:
			
			$orderby = "b.`id` {$sort}";
	}
	
	$where = " WHERE `id` > 0";
	
	if ($sid = getInt('sid', 'get')) {
	
		$where .= " AND `sid` = {$sid}";
	}
	
	$sql = "SELECT b.* " .
			// " (SELECT `name` FROM `contractors` WHERE `id` = b.`cid`) AS `contractor`".
			" FROM `equipments` AS b".
			"{$where}".
			" ORDER BY {$orderby}";
	
	$equipments = loadObjectList($sql);
	
	// Устанавливаем HEAD
	$title = "Оборудование";
	
	$tags['style'][] = "/plugins/infowindow/style.css";
	
	$tags['script'][] = "/includes/js/jquery.validate.js";
	$tags['script'][] = "/includes/js/jquery.validate.additional.methods.js";
	$tags['script'][] = "/includes/js/jquery.form.min.js";
	
	$tags['script'][] = "/plugins/infowindow/script.js";
	$tags['style'][] = "/plugins/infowindow/style.css";
?>