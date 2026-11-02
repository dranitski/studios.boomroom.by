<?php
	
	defined('access') or die ('no access');

	function returnHead($title, $meta = array(), $tags = array()){
		
		if(!$title) {
			$title = siteName;
		}
		
		$keywords		= $meta['keywords'];
		$description	= $meta['description'];
		
		function setStyle($path){
			
			$string = "\t<link rel=\"stylesheet\" href=\"{$path}\">\n";
			return $string;
			
		}
		
		function setScript($path){
			
			$string = "\t<script src=\"{$path}\"></script>\n";
			return $string;
			
		}
		
		function setTags($funcName, $array){
			
			$string = "";
			
			foreach($array as $v) {
				$string .= $funcName($v);
			}
			
			return $string;
		}
		
		if (isset($tags)) {
			
			if (isset($tags['style'])) {
				$styles		= setTags('setStyle', $tags['style']);
			}
			if (isset($tags['script'])) {
				$scripts	= setTags('setScript', $tags['script']);
			}
			
		}
	
	if($GLOBALS['ext'] == 'admin') {
	
		$user = $GLOBALS['user'];
?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<title>Администрирование | <?php echo $title; ?></title>
	<link rel="shortcut icon" href="/tmpl/admin/favicon.ico" type="image/x-icon" >
	<link rel="stylesheet" href="/tmpl/admin/css/style.css">
	<link rel="stylesheet" href="/tmpl/admin/css/panel.css">
<?php echo isset($styles) ? $styles : ""; ?>
	<script src="/includes/js/jquery.js"></script>
	<script src="/tmpl/admin/js/script.js?v=3"></script>
<?php echo isset($scripts) ? $scripts : ""; ?>
<?php
	}
	else {
?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<meta name="keywords" content="<?php echo $keywords; ?>">
	<meta name="description" content="<?php echo $description; ?>">
	<title><?php echo $title; ?></title>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" >
	<link rel="stylesheet" href="/tmpl/site/css/style.css">
<?php echo isset($styles) ? $styles : ""; ?>
	<script src="/includes/js/jquery.js"></script>
	<script src="/tmpl/site/js/script.js"></script>
<?php echo isset($scripts) ? $scripts : ""; ?>
<?php
		}
	}
	
	if (!$ext) {
		
		$title = "404 - Страницы не существует";
		$meta = null;
		$tags = null;
		
		$pageTitle = "Страницы не существует";
	}
	
	returnHead($title, $meta, $tags);
?>
