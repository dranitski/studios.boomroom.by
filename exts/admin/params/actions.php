<?php
	
	defined('access') or die ('no access');
	
	$subtask = getVar('subtask', 'post');
	
	switch ($subtask) {
	
		case 'holidays':
		
			$data = getVar('data', 'post');
			
			runSql("UPDATE `params` SET `holidays` = '{$data}'");
			
			echo getMsg("Данные сохранены", "ok", "location.reload();");
		
		break;
	}
	
	
?>