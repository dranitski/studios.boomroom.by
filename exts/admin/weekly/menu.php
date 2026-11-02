<?php
	
	defined('access') or die ('no access');
	
	$studios = loadObjectList("SELECT * FROM `studios`");
	
?>

<div id="tablemenu">
<table>
	<tr>
	<?php
	
		foreach ($studios as $studio) {
		
		$rooms = loadObjectList("SELECT * FROM `rooms` WHERE `sid` = {$studio->id}");
	?>
		<td>
			<p><?php echo $studio->name; ?></p>
			<ul>
			<?php
			foreach ($rooms as $room) {
			?>
			<li><a href="<?php echo admin; ?>/weekly?rid=<?php echo $room->id; ?>" <?php echo ($room->id == $rid) ? "class=\"active\"" : ""; ?>><?php echo $room->name; ?></a></li>
			<?php } ?>
			</ul>
		</td>
	<?php } ?>
	<tr/>
</table>
</div>
