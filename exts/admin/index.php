<?php
	
	defined('access') or die ('no access');
	
	if ($view) {
		
		if(file_exists(extPath."/{$view}/index.php")) {
			
			include extPath."/{$view}/index.php";
		}
	}
	else {
	
		echo "Ошибка: отсутствует вид";
	
	}
	
?>