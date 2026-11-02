$(function() {

});

function newWeekly(tid){
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'weekly',
			'subtask':	'event',
			'option':		'new',
			'tid':		tid
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#event').html(data);
		$('#event').data('id', 0);
		
		getEqsWeekly();
		setPrice();
		
		$('#iw').show();
	});
}

function changeEnd(){

	getEqsWeekly();
	setPrice();
}

function getEqsWeekly(){

	var d = $('#event .data');
	
	$('#rent .item').each(function(){ $(this).data('rent', 0); });
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'weekly',
			'subtask':	'eqs',
			'option':		'get',
			'rid':		d.data('rid'),
			'day':		d.data('day'),
			'start':		d.data('start'),
			'end':		$('#event select[name="end"]').val()
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#rent .list').html(data);
	});
}

function saveWeekly(e) {
	
	$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'saveWeekly(this);'); }, 500);
	// $(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);
	
	var d = $('#event .data');
	
	var cid = $('#event select[name="cid"]').val();
	var client_type = $('#event select[name="client_type"]').val();
	var client_name = $('#event input[name="client_name"]').val();
	var client_phone = $('#event input[name="client_phone"]').val();
	
	if (cid == 0) { alert('Выберите клиента'); return false; }
	
	var rent = [];
	$('#rent .item').each(function(){
		
		if ($(this).data('rent')) {
		
			var item = {};
			
			item.id = $(this).data('id');
			item.count = $(this).data('rent');

			rent.push(item);
		}
	});
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'weekly',
			'subtask':	'event',
			'option':		'save',
			'id':			d.data('id'),
			'rid':		d.data('rid'),
			'day':		d.data('day'),
			'start':		d.data('start'),
			'end':		$('#event select[name="end"]').val(),
			'cid':		cid,
			'nm':		$('#event input[name="nm[]"]:checked').val(),
			'note':	$('#event textarea[name="note"]').val(),
			'eqs':	JSON.stringify(rent),
			'price':	$('#event input[name="price"]').val(),
			'price_eqs':	$('#price-eqs').text(),
			'dis': $('#event select[name="cid"] option:selected').data('dis')
		},
		dataType: 'script'
		
	})
	.done(function(data){
		
		location.reload();
	});
}

function getWeekly(id){
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'weekly',
			'subtask':	'event',
			'option':		'get',
			'id':		id
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#event').html(data);
		
		$('#iw').show();
	});
}

function deleteWeekly(e) {
	
	// $(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);
	
	if (confirm("Подтвердить удаление?")) {
		
		$.ajax({
			
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'weekly',
				'subtask':	'event',
				'option':		'delete',
				'id':			$('#event .data').data('id')
				
			},
			dataType: 'script'
			
		})
		.done(function(data){
			
			location.reload();
		});
	}
}



