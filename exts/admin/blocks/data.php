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
	
	$sql = "SELECT b.*" .
			" FROM `blocks` AS b WHERE b.`state` = 1".
			" ORDER BY {$orderby}";
	
	$blocks = loadObjectList($sql);
	
	
	// Устанавливаем HEAD
	$title = "Блоки";
	
	$tags['style'][] = "/exts/admin/inc/style.css";
	
	// $tags['script'][] = "/includes/js/jquery.validate.js";
	// $tags['script'][] = "/includes/js/jquery.validate.additional.methods.js";
	// $tags['script'][] = "/includes/js/jquery.form.min.js";
	
	// $tags['script'][] = "/plugins/tinymce/tinymce.min.js";
?>