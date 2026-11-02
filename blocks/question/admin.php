<?php

	defined('access') or die ('no access');
	
	$xml = simplexml_load_file(path."/blocks/callback/admin.xml");
	
	$email		= (string) $xml->params->email;
	$subject	= (string) $xml->params->subject;
	
?>
<form name="callback">
	
	<div class="box">
		<div class="label">Email</div>
		<div class="field">
			<input name="email" type="text" value="<?php echo $email; ?>" class="w300"/>
		</div>
		<div class="clear"></div>
	</div>
	
	<div class="box">
		<div class="label">Тема сообщения</div>
		<div class="field">
			<input name="subject" type="text" value="<?php echo $subject; ?>" class="w300"/>
		</div>
		<div class="clear"></div>
	</div>
	
	<input name="bid" type="hidden" value="<?php echo $eBlock->id; ?>"/>
	<input name="r" type="hidden" value="0"/>

</form>


<script type="text/javascript">
	
	function applyBlock(){ $('form[name=callback] input[name=r]').val(0); saveData(); }
	function saveBlock(){ $('form[name=callback] input[name=r]').val(1); saveData(); }
	
	
	function saveData(){
		
		var form = 'form[name=callback]';
		
		var r = $(form+' input[name=r]').val();
		var email = $(form+' input[name=email]').val();
		var subject = $(form+' input[name=subject]').val();
		
		$.ajax({
			
			url:		'/blocks/callback/action.php',
			type:	'post',
			data: {
				
				'task':	'save',
				'email':		email,
				'subject':	subject,
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
