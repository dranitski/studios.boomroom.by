<?php
	
	defined('access') or die ('no access');
	
	if ($view) {
		
		include extPath."/{$view}/index.php";
		
	}
	else {
	
		echo "Ошибка: отсутствует вид";
	
	}
	
?>