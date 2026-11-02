<?php
	
	defined('access') or die ('no access');
	
	switch ($page->type) {
		
		case 0: include "type_0.php"; break;
		case 1: include "type_1.php"; break;
		// case 2: include "type_2.php"; break;
		// case 3: include "type_3.php"; break;
		
		default:
		
			include "type_0.php";
		
	}
?>
