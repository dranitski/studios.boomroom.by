<?php
	
	defined('access') or die ('no access');
	
	$date = date('d.m.Y в H:i', strtotime($page->addDate));
	
?>
<div id="event">
	<div class="desc">
		<h1><?php echo $page->name; ?></h1><br/>
		<span><?php echo $date; ?></span>
	</div>
	<div class="text">
		<?php setText($page->text); ?>
	</div>
</div>