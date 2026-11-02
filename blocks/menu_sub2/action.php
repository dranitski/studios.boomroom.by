<?php

	define('access', 1);
	
	// Настройки
	include $_SERVER['DOCUMENT_ROOT'].'/config.php';
	// Подключение основных функций
	include path.'/functions/main.php';
	include path.'/functions/db.php';
	include path.'/functions/user.php';
	
	// Изменять настройки блока может только администратор сайта, отправка форм открыта всем
	if (getVar('task', 'post') != 'send' and !$user->admin) {
		header('HTTP/1.1 403 Forbidden');
		exit;
	}
	
	switch (getVar('task', 'post')) {
	
		case 'save':
		
			$names	= getVar('names', 'post');	$names	= explode("|", $names);
			$links	= getVar('links', 'post');	$links	= explode("|", $links);
			$ords	= getVar('ords', 'post');	$ords	= explode("|", $ords);
			
			$r		= getInt('r', 'post');
			$bid	= getInt('bid', 'post');
			
			// $xml = simplexml_load_file("admin.xml");
			// $items = $xml->items;
			
			$new_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<data>\n\t<items>\n";
			
			foreach ($names as $n => $name) {
				
				$id		= $n+1;
				$link	= $links[$n];
				$ord	= $ords[$n];
				
				if ($id == $delete_id) continue;
				
				$new_xml .= "\t\t<item\n";
				$new_xml .= "\t\t\tid=\"{$id}\"\n";
				$new_xml .= "\t\t\tname=\"{$name}\"\n";
				$new_xml .= "\t\t\tlink=\"{$link}\"\n";
				$new_xml .= "\t\t\tord=\"{$ord}\"\n";
				$new_xml .= "\t\t/>\n";
				
			}
			
			$new_xml .= "\t</items>\n</data>";
			
			file_put_contents("admin.xml", $new_xml);
			
			$a_link = $r ? "" : "?edit={$bid}";
			
			echo getMsg("Действие выполнено", "ok", "window.location.assign('/admin/blocks{$a_link}');");
		
		break;
		
		case 'delete':
		
			$delete_id	= getInt('id', 'post');
			$bid		= getInt('bid', 'post');
		
			$xml = simplexml_load_file("admin.xml");
			$items = $xml->items;
			
			$new_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<data>\n\t<items>\n";
			
			foreach ($items->item as $n => $item) {
				
				$id		= $item->attributes()->id;
				$name	= $item->attributes()->name;
				$link	= $item->attributes()->link;
				$ord	= $item->attributes()->ord;
				
				if ($id == $delete_id) continue;
				
				$new_xml .= "\t\t<item\n";
				$new_xml .= "\t\t\tid=\"{$id}\"\n";
				$new_xml .= "\t\t\tname=\"{$name}\"\n";
				$new_xml .= "\t\t\tlink=\"{$link}\"\n";
				$new_xml .= "\t\t\tord=\"{$ord}\"\n";
				$new_xml .= "\t\t/>\n";
				
			}
			
			$new_xml .= "\t</items>\n</data>";
			
			file_put_contents("admin.xml", $new_xml);
			
			echo getMsg("Действие выполнено", "ok", "window.location.assign('/admin/blocks?edit={$bid}');");
			
		break;
	
	}
	
?>