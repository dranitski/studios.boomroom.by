$(function() {
	
	// Системные сообщения начало
	var sysmsg = $('#message').text();
	
	if (sysmsg.length > 0) {
		
		$('#message').show();
		setTimeout(function() { $('#message').fadeOut(1500); }, 3000);
		
	}
	// Системные сообщения конец
	
	setElements();
	$(window).resize(function() { setElements(); });
	
	function setElements(){
		
		var w		= $(window).height();
		var mh		= $('#main').height();
		var hh		= ($('#head').is(':visible')) ? $('#head').height() : 0;
		var ah		= $('#about').height();
		
		var s	= mh + hh + ah + 30;
		if (s < w) {
		
			var r = w - s -5;
			$('#about').css('margin-top', r);
		}
		else {
			$('#about').css('margin-top', 5);
		}
	}
	
	checkRequests();
	checkSync();
	setInterval(intervalCheck, 60000);
});


function intervalCheck() {
	
	// var d = new Date();
	// var s = d.getSeconds();
	
	// Проверка новых заявок
	// if (s == 0 || s == 15 || s == 30 || s == 45) {
		
	checkRequests();
	// }
	
	// Проверка синхронизации
	// if (s == 10 || s == 20 || s == 30 || s == 40 || s == 50) {
	// if (s == 10 || s == 30 || s == 50) {
		
	checkSync();
	// }
}

function checkRequests() {
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'requests',
			'option':		'check'
			
		},
		dataType: 'script'
		
	})
	.done(function(data){
		
		if ($('#requests').data('count') > 0) {
		
			var audio = new Audio();
			audio.src = '/files/request3.mp3';
			audio.autoplay = true;
		}
		
	});
}

function showRequests(){

	if ($('#requests').data('count') > 0) {

		$.ajax({
				
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'table',
				'subtask':	'requests',
				'option':		'get'
				
			},
			dataType: 'html'
			
		})
		.done(function(data){
			
			$('#requests .list').html(data);
			$('#requests .w').fadeIn();
			$('#requests .list').animate({'right': 0}, 200);
		});
	}
}

function hideRequests(){
	
	$('#requests .w').fadeOut();
	$('#requests .list').animate({'right': -300}, 200);
}

function applyRequest(id){
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'request',
			'option':		'apply',
			'id':		id
			
		},
		dataType: 'script'
		
	})
	.done(function(data){ if ($('#menu a:first-child').attr('class') == 'active') { location.reload(); } });
}

function cancelRequest(id){
	
	if (confirm("Подтверждаете отмену?")) {

		$.ajax({
				
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'table',
				'subtask':	'request',
				'option':		'cancel',
				'id':		id
				
			},
			dataType: 'script'
			
		})
		.done(function(data){ if ($('#menu a:first-child').attr('class') == 'active') { location.reload(); } });
	}
}

function checkSync() {
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'sync',
			'option':		'check'
			
		},
		dataType: 'script'
		
	})
	.done(function(data){});
}
