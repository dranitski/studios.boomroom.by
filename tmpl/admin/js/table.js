$(function() {
	
	setTdWidth();
	
	$(window).resize(function(){
		
		setTdWidth();
	});
	
	$(window).scroll(function() {
			
		// if ($(window).scrollTop() > 0) {
			
			// $('#tablemenu').css({'top': '0px'});
			// $('#tablehead').css({'top': '0px'});
		// }
		// else {
			
			// $('#tablemenu').css({'top': 'auto'});
			// $('#tablehead').css({'top': 'auto'});
		// }
	});
});

function setTdWidth() {
	
	$('.table .item').width($('.table .item').parent().width());
}

function newEvent(tid, date){
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'event',
			'option':		'new',
			'tid':		tid,
			'date':		date
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#event').html(data);
		$('#event').data('id', 0).data('date', date);
		
		getEqs();
		setPrice();
		
		$('#iw').show();
	});
}

function changeEnd(){

	getEqs();
	setPrice();
}

function getEqs(){

	var d = $('#event .data');
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'eqs',
			'option':		'get',
			'rid':		d.data('rid'),
			'date':		d.data('date'),
			'start':		d.data('start'),
			'end':		$('#event select[name="end"]').val()
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#rent .list').html(data);
	});
}

function getClient(id){

	if (id > 0) {
	
		$('#event .client .add').hide();
		$('#event .client .info').show();
	
		$.ajax({
				
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'table',
				'subtask':	'client',
				'option':		'get',
				'id':		id
				
			},
			dataType: 'html'
			
		})
		.done(function(data){
			
			$('#event .client .info').html(data);
			
			$('#event input[name="card"]').val($('#event select[name="cid"] option:selected').data('card'));
		});
		
		$('#event input[name="client_name"]').val('');
		$('#event input[name="client_phone"]').val('');
	}
	else {
		
		$('#event .client .add').show();
		$('#event .client .info').hide();
		
		$('#event input[name="card"]').val('');
	}
	
	setPrice();
}

$.expr[":"].Contains = $.expr.createPseudo(function(arg) {
	return function( elem ) {
	  return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
	};
});

function findClient(value){

	if (value.length >= 4) {
	
		$('#event select[name="cid"] option').each(function(){
			
			if ($(this).data('card') == value) {
				
				$('#event select[name="cid"]').val($(this).val());
				getClient($(this).val());
			}
		});
	}
	
	if (value.length > 2){
		
		$('#event select[name="cid"] option').hide();
		$('#event select[name="cid"] option:Contains("' + value + '")').show();
	}
	else {
		
		$('#event select[name="cid"] option').show();
	}
}

function findClientPhone(number){

	if (number.length >= 9) {
		
		$.ajax({
				
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'table',
				'subtask':	'client',
				'option':		'findbyphone',
				'number':	number
				
			},
			dataType: 'html'
			
		})
		.done(function(data){
			
			if (data.length > 0) {
			$('#event .findByPhoneList').html(data).show();
			} else { $('#event .findByPhoneList').hide(); }
		});
	}
	else { $('#event .findByPhoneList').hide(); }
}

function toRent(type, id){
	
	var item = '#rent .item[data-id="'+id+'"]';
	
	var count = $(item).data('count');
	var price = $(item).data('price');
	var rent = $(item).data('rent');
	var h = $('#event select[name="end"] option:selected').data('h');

	switch (type) {
		
		case 1: if (count > 0) { rent++; count--; } break;
		case 0: if (rent > 0) { rent--; count++; } break;
	}
	
	$(item).data('count', count);
	$(item+' .count').text(count);
	
	if (rent > 0) {
		
		price = price*rent*h;
		price = Math.round(price *100) / 100;
		$(item+' .rent').text('+ '+price+' ('+rent+')');
	}
	else {
		
		$(item+' .rent').text('-');
	}
	
	$(item).data('rent', rent);
	
	setPrice();
}

function setPrice() {
	
	var nm = $('#event input[name="nm[]"]:checked').val();
	var h = $('#event select[name="end"] option:selected').data('h');
	var card = $('#event select[name="cid"] option:selected').data('card');
	var dis = $('#event select[name="cid"] option:selected').data('dis');
	var price = $('#event select[name="end"] option:selected').data('price_'+nm);
	
	// Базовая стоимость с учетом правки админа
	var event_price = Number($('#event .data').data('price')); if ($('#price-edits input').is(':checked') && event_price > 0) { price = event_price; }
	
	if (dis || card) {
		
		$('#event .price h3 p').text('Скидка '+dis+'%');
		if ($('#price-edits input').is(':checked') == false) {
		price = price - (price/100*dis);
		}
	}
	else {
		
		$('#event .price h3 p').text('Без скидки');
	}
	
	var price_eqs = 0;
	$('#rent .item').each(function(){
		
		if ($(this).data('rent')) {
			
			price_eqs += $(this).data('price') * $(this).data('rent') * h;
		}
	});
	price_eqs = Math.round(price_eqs *10) / 10;
	$('#price-eqs').text(price_eqs);
	
	// price = Math.ceil(price *100) / 100;
	price = Math.round(price *10) / 10;
	
	var price_total = Math.round((price + price_eqs) *10) / 10;
	
	$('#event input[name="price"]').val(price);
	$('#price-total').text(price_total);
}

function saveEvent(e, apply) {
	
	$(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'saveEvent(this);'); }, 500);
	$(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);
	
	var d = $('#event .data');
	
	var cid = Number($('#event select[name="cid"]').val());
	var client_type = $('#event select[name="client_type"]').val();
	var client_name = $('#event input[name="client_name"]').val();
	var client_phone = $('#event input[name="client_phone"]').val();
	
	if (cid <= 0 && client_name == '') {
		
		if (cid == 0) { $('#event select[name="cid"]').val(-1); }
		alert('Выберите клиента или введите имя нового'); $('#event input[name="client_name"]').focus(); return false;
	}
	
	var rent = [];
	$('#rent .item').each(function(){
		
		if ($(this).data('rent')) {
		
			var item = {};
			
			item.id = $(this).data('id');
			item.count = $(this).data('rent');

			rent.push(item);
		}
	});
	
	var p = ($('#price-edits input').is(':checked')) ? 1 : 0;
	
	var note = $('#event textarea[name="note"]').val();
	
	if (p == 1 && note == '') { alert('Необходимо сделать комментарий, после изменения стоимости!'); $('#event textarea[name="note"]').focus(); return false; }
	
	var free = ($('#price-free input').is(':checked')) ? 1 : 0;
	
	var price = +$('#event input[name="price"]').val();
	var pay = +$('#event input[name="pay"]').val();
	if (price > 0 && price > pay && note == '') { alert('Необходимо сделать комментарий!'); $('#event textarea[name="note"]').focus(); return false; }
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'event',
			'option':		'save',
			'apply':	(apply) ? 1 : 0,
			'id':		d.data('id'),
			'rid':		d.data('rid'),
			'date':	d.data('date'),
			'start':	d.data('start'),
			'end':		$('#event select[name="end"]').val(),
			'cid':		cid,
			'nm':		$('#event input[name="nm[]"]:checked').val(),
			'client_type':		client_type,
			'client_name':	client_name,
			'client_phone':	client_phone,
			'note':	note,
			'eqs':	JSON.stringify(rent),
			'price':	$('#event input[name="price"]').val(),
			'price_eqs':	$('#price-eqs').text(),
			'pay_type':	$('#event select[name="pay_type"]').val(),
			'dis': $('#event select[name="cid"] option:selected').data('dis'),
			'p': p,
			'free': free
			
		},
		dataType: 'script'
		
	})
	.done(function(data){
		
		if (apply) {
			
			location.assign('/admin/tablerooms');
		}
		else {
			
			location.reload();
		}
	});
}

function getEvent(id){
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'event',
			'option':		'get',
			'id':		id
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#event').html(data);
		
		// getEqs();
		// setPrice();
		
		$('#iw').show();
	});
}

function deleteEvent(e) {
	
	$(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);
	
	if (confirm("Подтвердить удаление?")) {
		
		$.ajax({
			
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'table',
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

function cancelEvent(e, type) {
	
	$(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);
	
	if (confirm("Подтвердите действие: группа попадет в должники?")) {
		
		$(e).hide();
		
		$.ajax({
			
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'table',
				'subtask':	'event',
				'option':		'cancel',
				'id':			$('#event .data').data('id'),
				'type': type
				
			},
			dataType: 'script'
			
		})
		.done(function(data){
			
			location.reload();
		});
	}
}

function weeklyEvent(e) {
	
	$(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);
	
	if (confirm("Подтвердить действие")) {
		
		$.ajax({
			
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'table',
				'subtask':	'event',
				'option':		'weekly',
				'id':			$('#event .data').data('id')
				
			},
			dataType: 'script'
			
		})
		.done(function(data){
		});
	}
}

function getWeekly(id, date){
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'event',
			'option':		'weekly_get',
			'id':		id,
			'date':	date
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#event').html(data);
		
		$('#iw').show();
	});
}

function saveWeeklyEvent(id, date, e) {
	
	$(e).removeAttr('onclick');
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'event',
			'option':	'weekly_save',
			'id':			id,
			'date':		date,
			'note':		$('#event textarea[name="note"]').val()
			
		},
		dataType: 'script'
		
	})
	.done(function(data){
		
		location.reload();
	});
}

function nodateWeekly(id, date){
	
	if (confirm("Вы подтверждаете исключение?")) {
		
		$.ajax({
				
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'table',
				'subtask':	'event',
				'option':		'weekly_nodate',
				'id':		id,
				'date':	date
				
			},
			dataType: 'script'
			
		})
		.done(function(data){
		});
	}
}

function applyEvent(e, save) {
	
	$(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);
	
	$.ajax({
		
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'event',
			'option':	'apply',
			'id':		$('#event .data').data('id')
			
		},
		dataType: 'script'
		
	})
	.done(function(data){
	});
}

function payEvent(e) {
	
	$(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);

	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'event',
			'option':	'pay',
			'id':			$('#event .data').data('id'),
			'pay':		$('#event input[name="pay"]').val()
			
			
		},
		dataType: 'script'
		
	})
	.done(function(data){
	});
}

function saveNote(e) {
	
	$(e).addClass('clicked'); setTimeout(function(){ $(e).removeClass('clicked'); }, 1500);

	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'admin',
			'task':		'table',
			'subtask':	'event',
			'option':		'note',
			'id':			$('#event .data').data('id'),
			'note':		$('#event textarea[name="note"]').val()
			
			
		},
		dataType: 'script'
		
	})
	.done(function(data){
	});
}

function priceEdits() {
	
	if ($('#price-edits input').is(':checked')) {
		
		$('#event input[name="price"]').attr('disabled', false);
	}
	else {
		
		$('#event input[name="price"]').attr('disabled', true);
		setPrice();
	}
}

function priceFree() {
	
	if ($('#price-free input').is(':checked')) {
		
		$('#event input[name="price"]').val(0);
	}
	else {
		
		setPrice();
	}
}

function changePrice(price) {
	
	$('#event .data').data('price', price);
	setPrice();
}

