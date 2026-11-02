<?php

	defined('access') or die ('no access');
	
	$xml = simplexml_load_file(path."/blocks/menu_top/admin.xml");
	$items = $xml->items;
	
?>
<form name="menu_top">
<?php
	foreach ($items->item as $n => $item) {
		
		$id		= $item->attributes()->id;
		$name	= $item->attributes()->name;
		$link	= $item->attributes()->link;
		$ord	= $item->attributes()->ord;
?>
	<div class="box">
		<div class="field">
			<input title="Название" name="name[]" type="text" value="<?php echo $name; ?>" class="w200"/>
		</div>
		<div class="field">
			<input title="Ссылка" name="link[]" type="text" value="<?php echo $link; ?>" class="w200"/>
		</div>
		<div class="field">
			<input title="Порядок" name="ord[]" type="text" value="<?php echo $ord; ?>" class="w20"/>
		</div>
		<div class="lActions">
			<a class="delete" href="#" onclick="deleteItem(<?php echo $id; ?>);">Удалить</a>
		</div>
		<div class="clear"></div>
	</div>
<?php
	}
	
?>
	<div class="pActions">
		<a class="add" href="#" onclick="addItem();">Добавить</a>
	</div>
	
	<input name="bid" type="hidden" value="<?php echo $eBlock->id; ?>"/>
	<input name="r" type="hidden" value="0"/>

</form>


<script type="text/javascript">
	
	function applyBlock(){ $('form[name=menu_top] input[name=r]').val(0); saveData(); }
	function saveBlock(){ $('form[name=menu_top] input[name=r]').val(1); saveData(); }
	
	
	function saveData(){
		
		var form = 'form[name=menu_top]';
		
		var names = '';	$(form+' input[name^="name"]').each(function(index) { if (index) { names += '|'; } names += $(this).val(); });
		var links = '';	$(form+' input[name^="link"]').each(function(index) { if (index) { links += '|'; } links += $(this).val(); });
		var ords = '';	$(form+' input[name^="ord"]').each(function(index) { if (index) { ords += '|'; } ords += $(this).val(); });
		
		var r = $(form+' input[name=r]').val();
		
		$.ajax({
			
			url:		'/blocks/menu_top/action.php',
			type:	'post',
			data: {
				
				'task':	'save',
				'names':	names,
				'links':	links,
				'ords':	ords,
				'r':		r,
				'bid':	<?php echo $eBlock->id; ?>
			},
			dataType: 'script'
		})
		.done(function(data){});
	}
	
	function deleteItem(id) {
	
		if (confirm("Вы подтверждаете удаление?")) {
			
			$.ajax({
			
				url:		'/blocks/menu_top/action.php',
				type:	'post',
				data: {
					
					'task':	'delete',
					'id':		id,
					'bid':	<?php echo $eBlock->id; ?>
				},
				dataType: 'script'
			})
			.done(function(data){});
		}
	}
	
	function addItem(){
		
		$(".box:last").clone().insertBefore(".box:last");
		$(".box:last input").val('');
		$(".box:last .delete").attr('onclick', 'return deleteThis(this);');
	}
	
	function deleteThis(e){
		
		if (confirm("Вы подтверждаете удаление?")) {
			
			$(e).parent().parent().remove();
		}
	}
	
	function closeBlock(){
		
		window.location.assign('/admin/blocks');
	}
	
</script>
