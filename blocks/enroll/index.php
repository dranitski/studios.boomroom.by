<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/enroll/data.php";
?>

<div id="enroll">
	<h2>Запишись сегодня и получи свой первый урок вокала Бесплатно!</h2>
	<form data-state="0">
		<input name="name" type="text" value="" placeholder="Ваше имя" />
		<input name="phone" type="text" value="" placeholder="Телефон" />
		<input type="submit" value="Записаться на бесплатное занятие!"/>
	</form>
	<div class="result">
		Заявка успешно отправлена! В ближайшее время мы свяжемся с Вами!
	</div>
</div>

<script type="text/javascript">

	var form2 = $('#enroll form');
	
	form2.submit(function(event){
		
		form2.attr('data-state', 0);
		
		form2.children('input, textarea').each(function(){
			
			elem = $(this);
			
			switch (elem.attr('name')) {
				
				// case 'name': case 'tel': break;
				
				default: 
				
					if(elem.val() == '') {
						
						elem.addClass('required');
						form2.attr('data-state', 1);
					}
			}
		});
		
		if (form2.attr('data-state') == 1) {
			
			event.preventDefault();
			alert('Заполните обязательные поля');
		}
		else {
		
			event.preventDefault();
			
			$.ajax({
			
				url:		'/blocks/enroll/action.php',
				type:	'post',
				data: {
					
					'task':	'send',
					'name':	form2.children('input[name=name]').val(),
					'phone':	form2.children('input[name=phone]').val()
				},
				dataType: 'script'
			})
			.done(function(data){});
		}
		
	});
	
	$('#enroll input, textarea').on('change keyup', function(){
		
		if ($(this).val() != '') {
			
			$(this).removeClass('required');
		}
	});

</script>