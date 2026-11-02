<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/events/data.php";
	
	/*
	
if (count($events)) {
	
?>
<div id="event-h">
	<?php foreach ($events as $event) { $image = getFirstSrc($event->text); ?>
	<a href="<?php echo $event->link; ?>" style="background-image: url('<?php echo $image; ?>');"><span><?php echo $event->name; ?></span></a>
	<?php } ?>
	<a href="<?php echo $events[0]->link; ?>" style="background-image: url('<?php echo getFirstSrc($events[0]->text); ?>');"><span><?php echo $events[0]->name; ?></span></a>
	<div class="clear"></div>
</div>
<?php
}
?>
	*/
?>
<div class="desc" style="margin: 30px 0 0 0;"><h2>Последние события</h2></div>
<div id="event-h">
	<div class="main-new">
		<a href="<?php echo $events[0]->link; ?>">
			<div><img src="<?php echo getFirstSrc(stripslashes($events[0]->text)); ?>" /></div>
			<h3><?php echo $events[0]->name; ?></h3>
		</a>
	</div>
	<div class="main-new">
		<a href="<?php echo $events[1]->link; ?>">
			<div><img src="<?php echo getFirstSrc(stripslashes($events[1]->text)); ?>" /></div>
			<h3><?php echo $events[1]->name; ?></h3>
		</a>
	</div>
	<!--
	<div class="news">
		<div class="new"><a href="<?php echo $events[1]->link; ?>"><div><img src="<?php echo getFirstSrc($events[1]->text); ?>" /></div><p><?php echo $events[1]->name; ?></p></a></div>
		<div class="new"><a href="<?php echo $events[2]->link; ?>"><div><img src="<?php echo getFirstSrc($events[2]->text); ?>" /></div><p><?php echo $events[2]->name; ?></p></a></div>
		<div class="new"><a href="<?php echo $events[3]->link; ?>"><div><img src="<?php echo getFirstSrc($events[3]->text); ?>" /></div><p><?php echo $events[3]->name; ?></p></a></div>
		<div class="new"><a href="<?php echo $events[4]->link; ?>"><div><img src="<?php echo getFirstSrc($events[4]->text); ?>" /></div><p><?php echo $events[4]->name; ?></p></a></div>
	</div>
	-->
	<p class="clear"></p>
</div>