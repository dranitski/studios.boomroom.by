<?php

	defined('access') or die ('no access');
	
	$params = loadObject("SELECT * FROM `params`");
	
	// Устанавливаем HEAD
	$title = "Параметры";
	
	$tags['style'][] = "/exts/admin/inc/style.css";
	
	// $tags['script'][] = "/includes/js/jquery.validate.js";
	// $tags['script'][] = "/includes/js/jquery.validate.additional.methods.js";
	// $tags['script'][] = "/includes/js/jquery.form.min.js";
	
	// $tags['script'][] = "/plugins/tinymce/tinymce.min.js";
?>