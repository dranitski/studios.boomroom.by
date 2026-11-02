<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/question/data.php";
?>

<div id="question">
	<h2>Хочешь что-то спросить? Спрашивай!</h2>
	<form data-state="0">
		<input name="email" type="text" value="" placeholder="Email" />
		<textarea name="text" placeholder="Сообщение"></textarea>
		<input type="submit" value="Задать вопрос"/>
	</form>
	<div class="result">
		Спасибо за Ваш вопрос! В ближайшее время мы ответим Вам!
	</div>
</div>

<script type="text/javascript">

	var form = $('#question form');
	
	form.submit(function(event){
		
		form.attr('data-state', 0);
		
		form.children('input, textarea').each(function(){
			
			elem = $(this);
			
			switch (elem.attr('name')) {
				
				// case 'name': case 'tel': break;
				
				default: 
				
					if(elem.val() == '') {
						
						elem.addClass('required');
						form.attr('data-state', 1);
					}
			}
		});
		
		if (form.attr('data-state') == 1) {
			
			event.preventDefault();
			alert('Заполните обязательные поля');
		}
		else {
		
			event.preventDefault();
			
			$.ajax({
			
				url:		'/blocks/question/action.php',
				type:	'post',
				data: {
					
					'task':	'send',
					'email':	form.children('input[name=email]').val(),
					'text':	form.children('textarea[name=text]').val()
				},
				dataType: 'script'
			})
			.done(function(data){});
		}
		
	});
	
	$('#question input, textarea').on('change keyup', function(){
		
		if ($(this).val() != '') {
			
			$(this).removeClass('required');
		}
	});

</script>