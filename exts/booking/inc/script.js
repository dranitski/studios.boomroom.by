$(function() {
	
	// newEvent(11, '2016-05-07');
});

function newEvent(tid, date){
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'booking',
			'task':		'event',
			'subtask':	'new',
			'tid':		tid,
			'date':		date
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#event').html(data);
		$('#event').data('id', 0).data('tid', tid).data('date', date);
		
		getEqs();
		setPrice();
		
		$('#iw').show();
		
		// СОБЫТИЯ
		$('#card input[name="card"], #card input[name="pass"]').on('change keyup', function(){
			
			var card = $('#card input[name="card"]').val();
			var pass = $('#card input[name="pass"]').val();
			
			if (card.length == 4 && pass.length == 4) {
			
				$.ajax({
			
					url:		'/action.php',
					type:	'post',
					data: {
						
						'ext':	'booking',
						'task':	'client',
						'subtask':	'check',
						'card':	card,
						'pass':	pass
						
					},
					dataType: 'script'
					
				}).done(function(data){});
			}
			else {
				
				$('#card input[type="button"]').attr('data-state', 0).data('state', 0);
			}
		});
		
		$('#card input[type="button"]').on('click', function(){
			
			if ($(this).data('state') == 0) { alert("Неверный номер карты или пароль"); return false; }
			
			$.ajax({
			
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':	'booking',
					'task':	'client',
					'subtask':	'get',
					'id':		$('#client').data('id')
					
				},
				dataType: 'html'
				
			}).done(function(data){
				
				$('#client').html(data).show();
				$('#event .client').hide();
				setPrice();
			});
		});
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
			
			'ext':		'booking',
			'task':		'eqs',
			'subtask':	'get',
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
	var h = +$('#event select[name="end"] option:selected').data('h');
	var price = +$('#event select[name="end"] option:selected').data('price_'+nm);
	
	var rentprice = 0;
	
	$('#rent .item').each(function(){
		
		if ($(this).data('rent')) {
			
			rentprice += +$(this).data('price') * +$(this).data('rent') * h;
		}
	});
	
	$('#event .price p[data-type="price"]').text(price + rentprice);
	
	var dis = 10;
	if (+$('#client .card').data('dis') > 0) {
		
		dis = +$('#client .card').data('dis');
	}
	
	var disPrice = price - (price/100*dis) + rentprice;
	disPrice = Math.round(disPrice *100) / 100;
		
	$('#event .price.dis').show();
	$('#event .price p[data-type="dis"]').text(disPrice);
}



function saveEvent(e) {
	
	// $(e).removeAttr('onclick'); setTimeout(function(){ $(e).attr('onclick', 'saveEvent(this);'); }, 500);
	
	var d = $('#event .data');
	
	var client_type = $('#event select[name="client_type"]').val();
	var client_name = $('#event input[name="client_name"]').val();
	var client_phone = '+375 '+$('#event select[name="client_phone_code"]').val()+' '+$('#event input[name="client_phone_number"]').val();
	
	if ($('#client').is(':hidden')) {
	
		var client_phone_number = $('#event input[name="client_phone_number"]').val();
		
		if (client_name == '') { alert('Введите Ваше название / имя'); $('#event input[name="client_name"]').focus(); return false; }
		if (client_phone_number == '') { alert('Введите контактный номер телефона'); $('#event input[name="client_phone_number"]').focus(); return false; }
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
	
	if (+$(e).data('hold') === 1) return false;
	$(e).data('hold', 1);
	
	$('#event').append('<div class="result" style="position: absolute; width: 100%; height: 100%; background: #fff; top: 0; left: 0; padding: 15px 20px;"><p>Проверка бронирования</p>Пожалуйста, подождите!<br/><br/><img src="https://studio.boomroom.by/tmpl/site/images/loader.gif"/></div>');
	
	$.ajax({
			
		url:		'/action.php',
		type:	'post',
		data: {
			
			'ext':		'booking',
			'task':		'event',
			'subtask':	'save',
			'id':			d.data('id'),
			'rid':		d.data('rid'),
			'date':		d.data('date'),
			'start':		d.data('start'),
			'end':		$('#event select[name="end"]').val(),
			'nm':		$('#event input[name="nm[]"]:checked').val(),
			'client_type':		client_type,
			'client_name':	client_name,
			'client_phone':	client_phone,
			'cid':	$('#client').data('id'),
			'note':	$('#event textarea[name="note"]').val(),
			'eqs':	JSON.stringify(rent),
			'price':	$('#event .price p[data-type="price"]').text()
			
			
		},
		dataType: 'html'
		
	})
	.done(function(data){
		
		$('#event').html(data);
		$('#booking .item[data-tid="'+$('#event').data('tid')+'"]').addClass('blocked');
	});
}