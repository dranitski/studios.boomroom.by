<?php
	
	defined('access') or die ('no access');
	
	$errorPage = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
	
?>
Страницы <b><?php echo $errorPage; ?></b> не существует
