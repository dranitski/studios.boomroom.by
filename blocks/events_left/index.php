<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/events/data.php";
	

?>
<?php
	if (count($events)) {
	foreach ($events as $event) {
		
		$event->date = date('d.m.Y в H:i', strtotime($event->addDate));
?>
	<div class="event">
		<a href="/<?php echo $event->link; ?>"><img src="<?php echo getFirstSrc(stripslashes($event->text)); ?>" /></a>
		<a href="/<?php echo $event->link; ?>"><?php echo $event->name; ?></a><br/>
		<span><?php echo $event->date; ?></span><br>
		<?php echo getIntroText(strip_tags(stripslashes($event->text)), 200); ?>
	</div>
<?php
	}
	}
	else {
?>Нет новостей<?php
	}
?>