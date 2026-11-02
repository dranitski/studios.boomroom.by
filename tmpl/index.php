<?php
	
	defined('access') or die ('no access');
	
	switch ($ext) {
		
		case 'admin':
			
			include_once path.'/tmpl/admin/index.php';
			
		break;
		
		default:
			
			include_once path.'/tmpl/site/index.php';
		
	}
	
?>