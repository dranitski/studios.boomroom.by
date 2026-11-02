$(function() {
	
});

function getTemplate(id, rid, date, start){
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'edittable',
			'subtask':	'template',
			'option':		'get',
			'id':			id,
			'rid':		rid,
			'date':		date,
			'start':		start
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#template').html(data);
		$('#template').data('id', id).data('rid', rid).data('date', date).data('start', start);
		
		$('#iw').show();
	});
}

function saveTemplate(e){

	$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'saveTemplate(this);'); }, 500);
	// $(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);

	var t = '#template';
	
	var weekly = ($(t+' input[name="weekly"]').is(':checked')) ? 1 : 0;
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'edittable',
			'subtask':	'template',
			'option':		'save',
			'id':			$(t).data('id'),
			'rid':		$(t).data('rid'),
			'date':		$(t).data('date'),
			'start':		$(t).data('start'),
			'end':		$(t+' select[name="end"]').val(),
			'weekly':	weekly,
			'price_0':	$(t+' input[name="price_0"]').val(),
			'price_1':	$(t+' input[name="price_1"]').val(),
			'price_2':	$(t+' input[name="price_2"]').val()
			
		},
		dataType: 'script'
		
	})
	.done(function(data){
		
		location.reload();
	});
}

function changeTemplate(t){
	
	var id = $(t).val();
	var rid = $('#template').data('rid');
	var date = $(t).children('option:selected').data('date');
	
	date = (date == '0000-00-00') ? $('#template').data('date') : date; 
	
	var start = $('#template').data('start');
	
	getTemplate(id, rid, date, start);
}


function deleteTemplate(e){
	
	// $(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);
	
	if (confirm("Подтвердить удаление?")) {
		
		$.ajax({
			
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'edittable',
				'subtask':	'template',
				'option':		'delete',
				'id':			$('#template').data('id')
				
			},
			dataType: 'script'
			
		})
		.done(function(data){
			
			location.reload();
		});
	}
}













