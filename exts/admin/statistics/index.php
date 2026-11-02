<?php
	
	defined('access') or die ('no access');
	
?>

<?php
	
	$type_file = path."/exts/admin/statistics/{$type}.php";
	if (file_exists($type_file)) {
		
		include $type_file;
	}
?>
