<?php

	defined('access') or die ('no access');
	
	$page = loadObject("SELECT * FROM `pages` WHERE `id` = 1");
	
	// Устанавливаем HEAD
	$title = "{$page->title}";
	
	$meta['keywords']	=  "{$page->m_keys}";
	$meta['description']	=  "{$page->m_desc}";
?>