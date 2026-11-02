<?php

	defined('access') or die ('no access');
	
	switch (getInt('type', 'get')) {
		
		case 1: $type = 1; break;
		case 2: $type = 2; break;
		
		default: $type = 1;
	}
	
	// Устанавливаем HEAD
	$title = "Отчеты";
	
	$tags['script'][] = "/plugins/datepick/datepick.js";
	$tags['script'][] = "/plugins/datepick/datepick-ru.js";
	$tags['style'][] = "/plugins/datepick/datepick.css";
	
	$tags['script'][] = "/plugins/chartist/chartist.min.js";
	$tags['script'][] = "/plugins/chartist/chartist.plugins.js";
	$tags['style'][] = "/plugins/chartist/chartist.min.css";
	$tags['style'][] = "/plugins/chartist/chartist.plugins.css";
	
	$tags['style'][] = "/exts/admin/statistics/style.css";
?>