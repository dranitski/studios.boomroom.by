<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/menu_left/data.php";
	
	if ($state and $items) {
		
	?>
		<?php foreach ($items as $n => $item) { ?>
		<a href="/<?php echo $item->link; ?>" class="m<?php echo $item->id; ?><?php echo ($item->active) ? " active" : ""; ?>"><?php echo $item->name; ?></a>
		<?php echo ($n != cnt($items) - 1) ? "<hr/>" : "";?>
		<?php } ?>
	<?
	}
?>
