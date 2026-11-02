<?php
	
	defined('access') or die ('no access');
	
	// Продление сессии: срок cookie сдвигается при каждом заходе
	foreach (array('admin', 'admin_panel', 'director') as $role) {
		
		if ($user->$role) {
			authLogin($role);
		}
	}
	
?>
