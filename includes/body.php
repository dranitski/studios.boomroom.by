<?php
	
	defined('access') or die ('no access');
	
	if ($ext) {

		include path."/exts/{$ext}/index.php";
		
	}
	else {
		
		include path."/404.php";
		
	}
?>