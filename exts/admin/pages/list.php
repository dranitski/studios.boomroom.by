<?php
	
	defined('access') or die ('no access');
	
?>
<div class="block">
	
	<div class="bHead">
		<div class="bName">Страницы</div>
		<div class="bActions">
			<a class="save" href="/admin/pages?act=add" >Новая</a>
		</div>
	</div>
<?php

	$orderLink	= "/admin/pages?";
	
	$orderLink_id	= "{$orderLink}orderby=id&sort={$sort}";
	$orderLink_name	= "{$orderLink}orderby=name&sort={$sort}";
	$orderLink_pid	= "{$orderLink}orderby=pid&sort={$sort}";

?>
	<div class="filter">
		<div class="w20"><a href="<?php echo $orderLink_id; ?>">Id</a></div>
		<div class="w300"><a href="<?php echo $orderLink_name; ?>">Название</a></div>
		<div class="w300"><a href="<?php echo $orderLink_pid; ?>">Родитель</a></div>
		<div class="clear"></div>
	</div>
<?php
	
	foreach ($pages as $n => $page) {
	
	?>
	<div class="item" id="pe_<?php echo $page->id; ?>">
		
		<div class="id w20">
			<?php echo $page->id; ?>
		</div>
		
		<div class="name w300">
			<a title="изменить" href="/admin/pages?edit=<?php echo $page->id; ?>&type=<?php echo $page->type; ?>"><?php echo $page->name; ?></a>&nbsp;&nbsp;&nbsp;<a title="перейти на страницу" href="/<?echo $page->link;?>" target="_blank">&rarr;</a>
		</div>
		
		<div class="parent w300">
			<?php echo $page->parent; ?>
		</div>
		
		<div class="lActions">
			<a class="edit" href="#" onclick="window.location.assign('/admin/pages?edit=<?php echo $page->id; ?>&type=<?php echo $page->type; ?>');">Изменить</a>
			<a class="delete" href="#" onclick="deleteData('<?php echo $page->id; ?>');">Удалить</a>
		</div>
		
		<div class="clear"></div>
	</div>
	<?php
	
	}
	
?>
</div>