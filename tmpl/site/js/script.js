$(function(){
	
	$('.count').text('0');
	
	function setNumbers(){
		
		$('.count').each(function(){
			
			setNumber($(this));
		});
	}
	
	function setNumber(elem) {
		
		var currentNumber = elem.text();
		var endNumber = elem.attr('data-num');

		$({numberValue: currentNumber}).animate({numberValue: endNumber}, {
			duration: 2000,
			easing: 'linear',
			step: function() { 
				elem.text(Math.round(this.numberValue)); 
			}
		});
		
		setTimeout(function() { elem.text(endNumber); }, 2100);
	}
	
	if ($('#facts').is(':visible')) {
		$(window).scroll(function() {
			
			if ($(this).scrollTop() > $('#facts').offset().top - 400) {
				
				setNumbers();
			}
		});
	}
	
});