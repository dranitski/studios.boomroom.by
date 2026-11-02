<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/menu/data.php";
?>
<ul>
	<?php foreach ($items as $item) { ?>
	<li>
		<a href="/<?php echo $item->link; ?>" class="m<?php echo $item->id; ?><?php echo ($item->active) ? " active" : ""; ?>">
			<?php echo $item->name; ?>
		</a>
	</li>
	<?php } ?>
</ul>