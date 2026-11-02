<?php
	
	defined('access') or die ('no access');
	
?>
<?php
	
	$sub_path = path."/exts/content/page/html/{$page->id}.php";
	
	if(file_exists($sub_path)) {
		
		include $sub_path;
	}
	else {
		
	?>
	<div class="desc">
		<h1><?php echo $page->name; ?></h1><br/><br/>
		<?php setText($page->text); ?>
	</div>
	<hr/>
	<?php
	}
	
?>
