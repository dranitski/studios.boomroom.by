<?php
	
	defined('access') or die ('no access');
	
	if (!$user->admin and !$user->admin_panel and !$user->director) {
			
		$view = "login";
	}
	
	
?>