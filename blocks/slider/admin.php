<?php

	defined('access') or die ('no access');
	
	$xml = simplexml_load_file(path."/blocks/slider/admin.xml");
	$state = (int) $xml->params->state;
	
?>
<form name="menu">
	<div class="box">
		<div class="label">Включен</div>
		<div class="field">
			<select name="state" class="">
				<option value="0">нет</option>
				<option value="1"<?php echo ($state) ? " selected" : ""; ?>>да</option>
			</select>
		</div>
		<div class="clear"></div>
	</div>
	
	<input name="bid" type="hidden" value="<?php echo $eBlock->id; ?>"/>
	<input name="r" type="hidden" value="0"/>

</form>


<script type="text/javascript">
	
	function applyBlock(){ $('form[name=menu] input[name=r]').val(0); saveData(); }
	function saveBlock(){ $('form[name=menu] input[name=r]').val(1); saveData(); }
	
	
	function saveData(){
		
		var form = 'form[name=menu]';
		
		var r		= $(form+' input[name=r]').val();
		var state	= $(form+' select[name=state]').val();
		
		$.ajax({
			
			url:		'/blocks/slider/action.php',
			type:	'post',
			data: {
				
				'task':	'save',
				'state':	state,
				'r':		r,
				'bid':	<?php echo $eBlock->id; ?>
			},
			dataType: 'script'
		})
		.done(function(data){});
	}
	
	function closeBlock(){
		
		window.location.assign('/admin/blocks');
	}
	
</script>
