<?php
	
	defined('access') or die ('no access');
	
	// Получаем глобальные данные
	$user	= $GLOBALS['user'];
	$view	= $GLOBALS['view'];	
	$subPages	= $GLOBALS['subPages'];
	
	// Получаем меню
	$xml = simplexml_load_file(path."/blocks/menu_left/admin.xml");
	
	$state = (int) $xml->params->state;
	
	$items = array();
	
	if(strpos($_SERVER['REQUEST_URI'], "music_school")) {
		
		$active = (substr($_SERVER['REQUEST_URI'], 1) == "music_school") ? true : false;
		
		$items[] = (object) array(
			
			'name'	=> "О школе",
			'link'	=> "music_school",
			'pages'	=> null,
			'active'	=> $active
		
		);
	}
	
	if(strpos($_SERVER['REQUEST_URI'], "rehearsal_bases")) {
		
		$active = (substr($_SERVER['REQUEST_URI'], 1) == "rehearsal_bases") ? true : false;
		
		$items[] = (object) array(
			
			'name'	=> "О студиях",
			'link'	=> "rehearsal_bases",
			'pages'	=> null,
			'active'	=> $active
		
		);
	}
	
	if(strpos($_SERVER['REQUEST_URI'], "recording_studio")) {
		
		$active = (substr($_SERVER['REQUEST_URI'], 1) == "recording_studio") ? true : false;
		
		$items[] = (object) array(
			
			'name'	=> "О студии",
			'link'	=> "recording_studio",
			'pages'	=> null,
			'active'	=> $active
		
		);
	}
	
	if(cnt($subPages)) {
	
		foreach ($subPages as $page) {
			
			// ВРЕМЕННО с 29,05,2016
			// if(strpos($_SERVER['REQUEST_URI'], "rehearsal_bases")) { break; }
			
			$active = (substr($_SERVER['REQUEST_URI'], 1) == $page->link) ? true : false;
			
			if(cnt($page->pages)) {
			
				foreach ($page->pages as $subPage) {
					
					$subPage->active = (substr($_SERVER['REQUEST_URI'], 1) == $subPage->link) ? true : false;
					
					if ($subPage->active) {
					
						$active = true;
					}
				}
			}
			
			// Делаем ссылки с якорями
			// $link = (string) $page->link;
			// $finds = array('boom', 'hendrix', 'jam', 'point');
			
			// foreach ($finds as $find) {
				
				// $link = (strpos($link, $find)) ? "{$link}#top" : "{$link}";
			// }
			
			// Убираем из меню
			$link = (string) $page->link;
			$finds = array('boom', 'hendrix', 'jam', 'point');
			
			$continue = false;
			foreach ($finds as $find) {
				
				if (strpos($link, $find)) { $continue = true; }
			}
			
			if ($continue) { continue; }
			
			
			$items[] = (object) array(
				
				'name'	=> (string) $page->name,
				'link'	=> $link,
				'pages'	=> (array) $page->pages,
				'active'	=> $active
			);
		
		}
	}
?>