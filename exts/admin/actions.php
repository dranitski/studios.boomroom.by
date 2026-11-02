<?php
	
	defined('access') or die ('no access');
	
	switch ($task) {
	
		case 'page':
			
			if (getVar('subtask', 'post') == 'delete') {
				
				$id		= getInt('id', 'post');
				
				$sql = "SELECT `lid` FROM `pages` WHERE `id` = {$id}";
				$lid = loadResult($sql);
				
				$sql = "DELETE FROM `pages` WHERE `id` = {$id}"; runSql($sql);
				$sql = "DELETE FROM `links` WHERE `id` = {$lid}"; runSql($sql);
				
				echo getMsg("Страница удалена", "ok", "window.location.assign('/admin/pages');");
				
				exit;
			}
		
			$id		= getInt('id', 'post');
			
			$pid	= getInt('pid', 'post');
			$type	= getInt('type', 'post');
			
			$name	= getVar('name', 'post');
			$text	= getVar('text', 'post', 0, 1);
			
			$title		= getVar('title', 'post');
			$m_keys	= getVar('m_keys', 'post');
			$m_desc	= getVar('m_desc', 'post');
			
			$state	= getInt('state', 'post');
			
			$lid		= getInt('lid', 'post');
			$url		= getVar('link', 'post');
			
			$url	= getTranslit($url);
			$url	= str_replace('"', '', $url);
			
			$act	= getInt('act', 'post');
			
			if ($id) {
					
				$sql = "SELECT `id` FROM `links` WHERE `url` = '{$url}' AND `id` != {$lid}";
				
				if (loadResult($sql)) {
					
					$errorMsg = "Такой адрес ссылки уже есть в базе";
				}
				else {
				
					$sql = "UPDATE `pages` SET 
						pid		= {$pid}, 
						type	= {$type}, 
						name	= '{$name}', 
						text		= '{$text}', 
						title	= '{$title}', 
						m_keys	= '{$m_keys}', 
						m_desc	= '{$m_desc}', 
						state	= {$state} 
					";
					
					$sql .= "WHERE `id` = {$id}";
					
					runSql($sql);
					
					$sql = "UPDATE `links` SET `url` = '{$url}' WHERE `id` = {$lid}";
					runSql($sql);
					
					$msg = "информация обновлена";
				}
				
			}
			else {
				
				$sql = "SELECT `id` FROM `links` WHERE `url` = '{$url}'";
				
				if (loadResult($sql)) {
					
					$errorMsg = "Такой адрес ссылки уже есть в базе";
				}
				else {
					
					$addDate = date('Y-m-d H:i:s', strtotime('now'));
					
					$sql = "INSERT INTO `pages` (
						pid,
						type,
						name,
						text,
						title,
						m_keys,
						m_desc,
						addDate,
						state
					)";
					
					$sql .= " VALUES (
						{$pid},
						{$type},
						'{$name}',
						'{$text}',
						'{$title}',
						'{$m_keys}',
						'{$m_desc}',
						'{$addDate}',
						{$state}
					)";
						
					runSql($sql);
					
					$last_id = insertId();
					
					$link = "ext=content&view=page&id={$last_id}";
					
					$sql = "SELECT MAX(`id`) FROM `links` WHERE `id` > 100";
					$last_lid = loadResult($sql);
					$lid = $last_lid +1;
					
					$sql = "INSERT INTO `links` (
						id,
						link,
						url
					)";
					
					$sql .= " VALUES (
						{$lid},
						'{$link}',
						'{$url}'
					)";
						
					runSql($sql);
					
					$sql = "UPDATE `pages` SET `lid` = {$lid} WHERE `id` = {$last_id}";
					runSql($sql);
					
					$msg = "новая страница добавлена";
				
				}
				
			}
			
			// Параметры страниц
			if(!$errorMsg) {
				
				$id = ($id) ? $id : $last_id;
				
				$array_params = array();
				
				switch ($type) {
					
					case 0:
						
						// Слайды
						$slide_images = explode("|", getVar('slide_images', 'post'));
						$slide_videos = explode("|", getVar('slide_videos', 'post'));
						
						$slides = array();
						
						if (getVar('slide_images', 'post')) {
							
							foreach ($slide_images as $n=> $image) {
								
								$video = $slide_videos[$n];
								
								$slides[] = array('image' =>$image, 'video' =>$video);
							}
						}
						
						$array_params['slides'] = $slides;
						
					break;
					
					case 2:
						
						// Слайды
						$slide_images = explode("|", getVar('slide_images', 'post'));
						$slide_videos = explode("|", getVar('slide_videos', 'post'));
						
						$slides = array();
						
						if (getVar('slide_images', 'post')) {
							
							foreach ($slide_images as $n=> $image) {
								
								$video = $slide_videos[$n];
								
								$slides[] = array('image' =>$image, 'video' =>$video);
							}
						}
						
						$array_params['slides'] = $slides;
						
						// Порядок
						$ords = explode("|", getVar('ord', 'post'));
						$ordering = array();
						
						foreach ($ords as $ord) {
							
							$parts = explode(":", $ord);
							$ordering["{$parts[0]}"] = $parts[1];
						}
						
						$array_params['ord'] = $ordering;
						
						// Галерея
						$array_params['gallery'] = getVar('gallery', 'post');
						
						// Большое видео
						$array_params['vb_image'] = getVar('vb_image', 'post');
						$array_params['vb_video'] = getVar('vb_video', 'post');
						
						// 3 видео
						$array_params['vs1_image']	= getVar('vs1_image', 'post');
						$array_params['vs1_video']	= getVar('vs1_video', 'post');
						$array_params['vs2_image']	= getVar('vs2_image', 'post');
						$array_params['vs2_video']	= getVar('vs2_video', 'post');
						$array_params['vs3_image']	= getVar('vs3_image', 'post');
						$array_params['vs3_video']	= getVar('vs3_video', 'post');
						
						// Дополнительно
						$array_params['extra']		= getVar('extra', 'post', 0, 1);
						
					break;
					
					case 3:
						
						// Слайды
						$person_names = explode("|", getVar('person_names', 'post'));
						$person_intros = explode("|", getVar('person_intros', 'post'));
						$person_fotos = explode("|", getVar('person_fotos', 'post'));
						$person_images = explode("|", getVar('person_images', 'post'));
						$person_videos = explode("|", getVar('person_videos', 'post'));
						$person_descs = explode("|", getVar('person_descs', 'post', 0, 1));
						
						$persons = array();
						
						if (getVar('person_names', 'post')) {
							
							foreach ($person_names as $n=> $name) {
								
								$foto	= $person_fotos[$n];
								$intro	= $person_intros[$n];
								$image	= $person_images[$n];
								$video	= $person_videos[$n];
								$desc	= $person_descs[$n];
								
								$persons[] = array('name' =>$name, 'intro' =>$intro, 'foto' =>$foto, 'image' =>$image, 'video' =>$video, 'desc' =>$desc);
							}
						}
						
						$array_params['persons'] = $persons;
						
						// Порядок
						$ords = explode("|", getVar('ord', 'post'));
						$ordering = array();
						
						foreach ($ords as $ord) {
							
							$parts = explode(":", $ord);
							$ordering["{$parts[0]}"] = $parts[1];
						}
						
						$array_params['ord'] = $ordering;
						
					break;
				}
				
				// $params = json_encode($array_params);
				$params = dbEscape(json_encode($array_params, JSON_UNESCAPED_UNICODE));
				
				runSql("UPDATE `pages` SET `params` = '{$params}' WHERE `id` = {$id}");
			
			}
				
			
			if($errorMsg) {
				
				echo getMsg($errorMsg, "error", "");
			}
			else {
				
				if ($act == 1) {
					
					if (!$last_id) {
						
						$action = "";
					}
					else {
						
						$action = "window.location.assign('/admin/pages?edit={$last_id}&type={$type}');";
					}
				}
				elseif ($act == 2) {
					
					$action = "window.location.assign('/admin/pages');";
				}
				else {
				
					$action = "";
				}
				
				echo getMsg($msg, "ok", "{$action}");
			}
			
		break;
		
		case 'table': case 'clients': case 'equipments': case 'edittable': case 'weekly': case 'statistics': case 'params': case 'economics': case 'debtors':
		
			include_once path."/exts/admin/{$task}/actions.php";
		
		break;
	}
	
	
?>