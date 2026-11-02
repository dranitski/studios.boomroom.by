<?php
	
	defined('access') or die ('no access');
	
	$url = getVar('REQUEST_URI', 'SERVER', 1, 1);
	
	if ($url == '/' or $url == '/index.php'){
		
		// $getExt	= 'content';
		// $getView	= 'home';
		// $getId		= 0;
		
		$getExt	= 'content';
		$getView	= 'page';
		$getId		= 4;
		
	}
	else {
		
		$url = substr($url, 1);
		
		if (strpos($url, '?')) {
			
			$urlArr = explode('?', $url);
			$urlClear = $urlArr[0];
		}
		else {
			
			$urlClear = $url;
		}
		
		$sql = "SELECT * FROM `links` WHERE `url` = '{$urlClear}'";
		
		if (!$link = loadObject($sql)) {
			
			if (substr_count($url, '/')) {
				
				$urlParts	= explode('/', $url);
				
				$urlLevel1	= $urlParts[0];
				
				// Убираем все после знака `?`
				if (strpos($urlParts[1], '?')) {
					
					$urlLevel2 = explode('?', $urlParts[1]);
					$urlLevel2 = $urlLevel2[0];
				}
				else {
					
					$urlLevel2 = $urlParts[1];
				}
				
				$sql = "SELECT * FROM `links` WHERE `url` = '{$urlLevel1}'";
				
				if ($link = loadObject($sql)) {
					
					$url = explode("&", $link->link);
			
					$getExt	= '';
					$getView	= '';
					$getId		= 0;
					
					foreach ($url as $part) {
						
						if (substr_count($part, 'ext=')) {
							$getExt = str_replace('amp;ext=','', $part);
							$getExt = str_replace('ext=','', $getExt);
						}
						
						if (substr_count($part, 'view=')) {
							$getView = str_replace('amp;view=','', $part);
							$getView = str_replace('view=','', $getView);
						}
						
						if (substr_count($part, 'id=')) {
							$getId = str_replace('amp;id=','', $part);
							$getId = str_replace('id=','', $getId);
						}
						
					}
					
				}
				
			}
			else {
				
				//Получаем значения из url
				$getExt	= getVar('ext', 'get');
				$getView	= getVar('view', 'get');
				$getId		= getInt('id', 'get');
			}
			
		}
		else {
			
			$url = explode("&", $link->link);
			
			$getExt	= '';
			$getView	= '';
			$getId		= 0;
			
			foreach ($url as $part) {
				
				if (substr_count($part, 'ext=')) {
					$getExt = str_replace('amp;ext=','', $part);
					$getExt = str_replace('ext=','', $getExt);
				}
				
				if (substr_count($part, 'view=')) {
					$getView = str_replace('amp;view=','', $part);
					$getView = str_replace('view=','', $getView);
				}
				
				if (substr_count($part, 'id=')) {
					$getId = str_replace('amp;id=','', $part);
					$getId = str_replace('id=','', $getId);
				}
				
			}
			
		}
		
	}
	
	if (file_exists(path."/exts/{$getExt}") and strlen($getExt) > 0) {
		
		$ext = $getExt;
		
		if (file_exists(path."/exts/{$ext}/{$getView}") and strlen($getView) > 0) {
		
			$view = $getView;
			
		}
		else {
			
			$view = null;
			
		}
	}
	else {
		
		$ext = null;
		
	}
	
	// Подключение собственных роутеров расширений
	if (file_exists(path."/exts/{$ext}/router.php")) {

		include path."/exts/{$ext}/router.php";

	}
	
?>