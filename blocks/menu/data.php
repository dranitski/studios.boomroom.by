<?php
	
	defined('access') or die ('no access');
	
	// Получаем глобальные данные
	$user	= $GLOBALS['user'];
	$view	= $GLOBALS['view'];	
	
	// Получаем меню
	$xml = simplexml_load_file(path."/blocks/menu/admin.xml");
	
	foreach ($xml->items->item as $n => $p_item) {
		
		if ((int) $p_item->attributes()->ord != 1) {
			$active = (substr($_SERVER['REQUEST_URI'], 1) == $p_item->attributes()->link) ? true : false;
		}
		
		$items[] = (object) array(
			
			'id'		=> (int) $p_item->attributes()->id,
			'name'	=> (string) $p_item->attributes()->name,
			'link'	=> (string) $p_item->attributes()->link,
			'ord'	=> (int) $p_item->attributes()->ord,
			'active'	=> $active
		
		);
	
	}
	
	// Сортируем
	uasort($items, "sortByOrd");
?>