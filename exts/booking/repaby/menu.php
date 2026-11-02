<?php
	
	defined('access') or die ('no access');
	
?>

<ul class="menu">
	<?php foreach ($studios as $studio) { ?>
		<li><a href="/repaby?sid=<?php echo $studio->id; ?>#booking" <?php echo ($studio->id == $sid) ? "class=\"active\"" : ""; ?>><?php echo $studio->name; ?></a></li>
	<?php } ?>
</ul>
<ul class="menu sub">
	<?php foreach ($rooms as $room) { if ($room->sid != $sid) continue; ?>
		<li><a href="/repaby?sid=<?php echo $sid; ?>&rid=<?php echo $room->id; ?>#booking" <?php echo ($room->id == $rid) ? "class=\"active\"" : ""; ?>><?php echo $room->name; ?></a></li>
	<?php } ?>
</ul>

