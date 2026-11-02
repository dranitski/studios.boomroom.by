<?php
	
	defined('access') or die ('no access');
	
?>

<?php

if(getInt('edit', 'get')) {
	
	$id	 = getInt('edit', 'get');
	$sql = "SELECT * FROM `blocks` WHERE `id` = {$id}";
	$eBlock	= loadObject($sql);
	
}
?>

<table class="table">
<tr>
<td class="w400">
	<div class="block mw300">
		
		<div class="bHead">
			<div class="bName">Блоки</div>
		</div>
	<?php

		$orderLink	= "/admin/blocks?";
		
		$orderLink_id	= "{$orderLink}orderby=id&sort={$sort}";
		$orderLink_name	= "{$orderLink}orderby=name&sort={$sort}";

	?>
		<div class="filter">
			<div class="w20"><a href="<?php echo $orderLink_id; ?>">Id</a></div>
			<div class=""><a href="<?php echo $orderLink_name; ?>">Название</a></div>
			<div class="clear"></div>
		</div>
	<?php
		
		foreach ($blocks as $n => $block) {
		
		?>
		<div class="item" id="pe_<?php echo $block->id; ?>">
			
			<div class="id w20">
				<?php echo $block->id; ?>
			</div>
			
			<div class="name">
				<a href="/admin/blocks?edit=<?php echo $block->id; ?>"><?php echo $block->name; ?></a>
			</div>
			
			<div class="lActions">
				<a class="edit" href="/admin/blocks?edit=<?php echo $block->id; ?>">Изменить</a>
			</div>
			
			<div class="clear"></div>
		</div>
		<?php
		
		}
		
	?>
	</div>
</td>
<td class="">
<?php if($eBlock->id) { ?>
	<div class="block mw500">
		<div class="bHead">
			<div class="bName"><?php echo $eBlock->name; ?></div>
			<div class="bActions">
				<a class="apply" href="#" onclick="applyBlock();">Применить</a>
				<a class="save" href="#" onclick="saveBlock();">Сохранить</a>
				<a class="close" href="#" onclick="closeBlock();">Закрыть</a>
			</div>
		</div>
		<div class="clear"></div>
		<div class="bContent">
			<?php include_once path."/blocks/{$eBlock->folder}/admin.php"; ?>
		</div>
	</div>
<?php } else { ?>&nbsp;<?php } ?>
</td>
</tr>
</table>

<script type="text/javascript">
	
</script>