<?php

	defined('access') or die ('no access');
	
	$orderby	= getVar('orderby', 'get');
	$sort		= getVar('sort', 'get');
	
	if (!$sort) { $sort =  'desc'; }
	if ($sort == 'asc') { $sort = 'desc'; } else { $sort = 'asc'; }
	
	switch ($orderby) {
		
		case 'id':
		
			$orderby = "p.`id` {$sort}";
		break;
	
		case 'name':
		
			$orderby = "p.`name` {$sort}";
		break;
	
		case 'pid':
		
			$orderby = "p.`pid` {$sort}";
		break;
		
		default:
			
			$orderby = "p.`id` {$sort}";
	}
	
	$sql = "SELECT p.*, (SELECT `name` FROM `pages` WHERE `id` = p.`pid`) AS `parent`, l.`url` AS `link`" .
			" FROM `pages` AS p".
			" LEFT JOIN `links` AS l ON l.`id` = p.`lid`".
			" ORDER BY {$orderby}";
	
	$pages = loadObjectList($sql);
	
	// Устанавливаем HEAD
	$title = "Страницы";
	
	$tags['style'][] = "/exts/admin/inc/style.css";
	
	$tags['script'][] = "/includes/js/jquery.validate.js";
	$tags['script'][] = "/includes/js/jquery.validate.additional.methods.js";
	$tags['script'][] = "/includes/js/jquery.form.min.js";
	
	$tags['script'][] = "/plugins/tinymce/tinymce.min.js";
?>