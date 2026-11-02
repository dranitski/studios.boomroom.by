<?php
	
	defined('access') or die ('no access');
	
	$page = loadObject("SELECT * FROM `pages` WHERE `id` = {$getId} AND `state` = 1");
	$params = json_decode($page->params);
	
	$parent_id = ($page->pid) ? $page->pid : $page->id;
	if ($pre_parent_id = loadResult("SELECT `pid` FROM `pages` WHERE `id` = {$parent_id}")) { $parent_id = $pre_parent_id; }
	
	$sql = "SELECT p.`id`, p.`pid`, p.`name`, l.`url` AS `link`" .
			" FROM `pages` AS p".
			" LEFT JOIN `links` AS l ON l.`id` = p.`lid`".
			" WHERE p.`pid` = {$parent_id}".
			" ORDER BY p.`id`";
	
	$subPages = loadObjectList($sql);
	
	if (count($subPages)) {
		
		foreach ($subPages as $subPage) {
			
			$sql = "SELECT p.`id`, p.`pid`, p.`name`, l.`url` AS `link`" .
				" FROM `pages` AS p".
				" LEFT JOIN `links` AS l ON l.`id` = p.`lid`".
				" WHERE p.`pid` = {$subPage->id}".
				" ORDER BY p.`id`";
		
			$pages = loadObjectList($sql);
			
			if(count($pages)) {
				
				$subPage->pages = $pages;
			}
		}
	}
	
	// Устанавливаем HEAD
	$title = "{$page->title}";
	
	$meta['keywords']	=  "{$page->m_keys}";
	$meta['description']	=  "{$page->m_desc}";
?>