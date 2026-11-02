<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/menu_bottom/data.php";
?>
<?php foreach ($items as $item) { ?>
<a href="/<?php echo $item->link; ?>" class="m<?php echo $item->id; ?><?php echo ($item->active) ? " active" : ""; ?>"><?php echo $item->name; ?></a>
<?php } ?>