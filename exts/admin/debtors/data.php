<?php

	defined('access') or die ('no access');
	
	// $params = loadObject("SELECT * FROM `params`");
	
	// Устанавливаем HEAD
	$title = "Должники";
	
	$tags['style'][] = "/exts/admin/inc/style.css";
	
	$tags['script'][] = "/plugins/datepick/datepick.js";
	$tags['script'][] = "/plugins/datepick/datepick-ru.js";
	$tags['style'][] = "/plugins/datepick/datepick.css";
	
	// $tags['script'][] = "/includes/js/jquery.validate.js";
	// $tags['script'][] = "/includes/js/jquery.validate.additional.methods.js";
	// $tags['script'][] = "/includes/js/jquery.form.min.js";
	
	// $tags['script'][] = "/plugins/tinymce/tinymce.min.js";
?>