<?php
	
	defined('access') or die ('no access');
	
	switch (getInt('type', 'get')) {
		
		case 1: $type = 1; $title = "Расходы"; break;
		case 2: $type = 2; $title = "Доп. доходы"; break;
		case 3: $type = 3; $title = "Отчет"; break;
		case 4: $type = 4; $title = "Должники"; break;
		
		default: $type = 1;
	}
	
	$tags['script'][] = "/includes/js/jquery.validate.js";
	$tags['script'][] = "/includes/js/jquery.validate.additional.methods.js";
	$tags['script'][] = "/includes/js/jquery.form.min.js";
	
	$tags['script'][] = "/plugins/datepick/datepick.js";
	$tags['script'][] = "/plugins/datepick/datepick-ru.js";
	$tags['style'][] = "/plugins/datepick/datepick.css";
	
	$tags['style'][] = "/exts/admin/economics/style.css";
?>