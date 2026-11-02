<?php
	
	defined('access') or die ('no access');
	
	$task = getVar('task', 'post');
	
	if ($task and ($user->admin or $user->admin_panel or $user->director)) {
		
		include "actions.php";
		
	}
	else {
	
		if ($user->admin or $user->admin_panel or $user->director) {
			
			if (file_exists(extPath."/{$view}/data.php")) {
				
				include "{$view}/data.php";
				
			}
			else {
				
				echo "Ошибка: отсутствует data.php"; exit;
			}
		}
		else {
			
			// redirect('/', '', 'error');
		}
	}
?>