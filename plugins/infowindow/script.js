$(function() {
	
	setIW();
	
	$(window).resize(function(){ setIW(); });
	
	$('#iw, #iw.close span').on('click', function(){ $('body').css('overflow', 'auto'); $('#iw').hide(); });
	$('#iw > div').on('click', function(event){ event.stopPropagation(); });
	
});
	
function setIW() {
	
	var mt = 0;
	var w = $('#iw').data('w');
	var h = $('#iw').data('h');
	
	$('#iw > div').width(w);
	$('#iw > div').height(h);
	
	if ($(window).height() >= h) { mt = ($(window).height()-h) / 2; } else { h = $(window).height(); }
	
	$('#iw > div').height(h);
	$('#iw .close span').css('margin-top', 10-h);
	$('#iw > div').css('margin-top', mt);
}