<?php
	
	defined('access') or die ('no access');
	
	$task = getVar('task', 'post');
	
	if ($task) {
		
		include "actions.php";
		
	}
	else {
		
		if (file_exists(extPath."/{$view}/data.php")) {
			
			include "{$view}/data.php";
			
		}
		else {
			
			echo "Ошибка: отсутствует data.php"; exit;
		}
	
	}
?>