<?php
	
	defined('access') or die ('no access');
	
?>
<div id="contacts">
	<div class="desc" style="margin-bottom: 30px;"><h1>Контакты</h1></div>
	
	<?php if (!$_GET['t'] or $_GET['t'] == 1) { ?>
	<div class="contact">
		<div class="logo"><img src="/tmpl/site/images/logo-music_school.png"/></div>
		<div class="info">
			<h3>Музыкальная школа</h3>
			<span>г. Минск, ул. Короля, 6</span>
			<div class="phones">
				<a class="mts" href="tel:+375333582979">+375 33 <span>358-29-79</span></a>
				<a class="vel" href="tel:+375296582979">+375 29 <span>658-29-79</span></a>
			</div>
			<span>ms@boomroom.by</span>
			<span>Eжедневно с 13.00-23.00</span>
		</div>
		<div class="map" id="map-1"></div>
		<div class="clear"></div>
	</div>
	
	<hr/>
	<?php } ?>
	
	<?php if (!$_GET['t'] or $_GET['t'] == 2) { ?>
	<div class="contact">
		<div class="logo"><img src="/tmpl/site/images/logo-rehearsal_bases.png"/></div>
		<div class="info">
			<h3>Репетиционные студии</h3>
			<span></span>
			<div class="phones">
				<a class="mts" href="tel:+375295714120">+375 29 <span>571-41-20</span></a>
				<a class="vel" href="tel:+375296366227">+375 29 <span>636-62-27</span></a>
				<a class="tel" href="tel:+375173272034">+375 17 <span> 327-20-34</span></a>
			</div>
			<span>rb@boomroom.by</span>
			<span>Eжедневно с 11.00-23.00</span>
		</div>
		<div class="map" id="map-2"></div>
		<div class="clear"></div>
	</div>
	
	<hr/>
	<?php } ?>
	
	<?php if (!$_GET['t'] or $_GET['t'] == 3) { ?>
	<div class="contact">
		<div class="logo"><img src="/tmpl/site/images/logo-recording_studio.png"/></div>
		<div class="info">
			<h3>Студия звукозаписи</h3>
			<span>г. Минск, ул. Октябрьская, 16</span>
			<div class="phones">
				<a class="mts" href="tel:+375297657879">+375 33 <span>765-78-79</span></a>
			</div>
			<span>rs@boomroom.by</span>
			<span>Eжедневно с 9.00-23.00</span>
		</div>
		<div class="map" id="map-3"></div>
		<div class="clear"></div>
	</div>
	
	<hr/>
	<?php } ?>
	
	<?php if (!$_GET['t'] or $_GET['t'] == 4) { ?>
	<div class="contact">
		<div class="logo"><img src="/tmpl/site/images/logo-equipment_rental.png"/></div>
		<div class="info">
		<h3>Прокат оборудования</h3>
			<span>г. Минск, Уручье, д. Копище</span>
			<div class="phones">
				<a class="mts" href="tel:+375297767292">+375 29 <span>776-72-92</span></a>
				<a class="vel" href="tel:+375447767297">+375 44 <span>776-72-97</span></a>
			</div>
			<span>er@boomroom.by</span>
			<span>Eжедневно с 9.00-23.00</span>
		</div>
		<div class="map" id="map-4"></div>
		<div class="clear"></div>
	</div>
	<?php } ?>
	
</div>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&language=ru"></script>
<script type="text/javascript">
$(function() {
	
	<?php if (!$_GET['t'] or $_GET['t'] == 1) { ?>
	// Музыкальная школа
	var map_1 = new google.maps.Map(document.getElementById('map-1'), {
		scrollwheel: true,
		zoom: 14,
		center: new google.maps.LatLng(53.902436,27.543022),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	var marker_1 = new google.maps.Marker({
		position: new google.maps.LatLng(53.902436,27.543022),
		map: map_1,
		icon: 'http://mazanik.com/marker_sm.png',
		title: 'Музыкальная школа'
	});
	
	// Метро
	var metro_1_1 = new google.maps.Marker({
		position: new google.maps.LatLng(53.9051808,27.5399035), map: map_1, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Фрунзенская'
	});
	var metro_1_2 = new google.maps.Marker({
		position: new google.maps.LatLng(53.905462,27.554487), map: map_1, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Немига'
	});
	<?php } ?>
	
	<?php if (!$_GET['t'] or $_GET['t'] == 2) { ?>
	// Студии звукозаписи
	var map_2 = new google.maps.Map(document.getElementById('map-2'), {
		scrollwheel: true,
		zoom: 11,
		center: new google.maps.LatLng(53.914338,27.567037),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	var marker_2_1 = new google.maps.Marker({
		position: new google.maps.LatLng(53.901173,27.555439),
		map: map_2,
		icon: 'http://mazanik.com/marker_sm.png',
		title: 'Репетиционный комплекс BOOM'
	});
	
	var infowindow_2_1 = new google.maps.InfoWindow({
		content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/rehearsal_bases/boom">Репетиционный комплекс BOOM</a></b><br/>ул. Ленина, 4<br/><i style="color: red;">ст.м. Октябрьская</i></div>'
	});
	
	var marker_2_2 = new google.maps.Marker({
		position: new google.maps.LatLng(53.889904,27.573893),
		map: map_2,
		icon: 'http://mazanik.com/marker_sm.png',
		title: 'Репетиционный комплекс Hendrix'
	});
	
	var infowindow_2_2 = new google.maps.InfoWindow({
		content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/rehearsal_bases/hendrix">Репетиционный комплекс Hendrix</a></b><br/>ул. Октябрьская, 16<br/><i style="color: red;">ст.м. Первомайская</i></div>'
	});
	
	var marker_2_3 = new google.maps.Marker({
		position: new google.maps.LatLng(53.902409,27.567621),
		map: map_2,
		icon: 'http://mazanik.com/marker_sm.png',
		title: 'Репетиционный комплекс Jam'
	});
	
	var infowindow_2_3 = new google.maps.InfoWindow({
		content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/rehearsal_bases/jam">Репетиционный комплекс Jam</a></b><br/>ул. К. Маркса, 50а<br/><i style="color: red;">ст.м. Октябрьская</i></div>'
	});
	
	var marker_2_4 = new google.maps.Marker({
		position: new google.maps.LatLng(53.929157,27.608635),
		map: map_2,
		icon: 'http://mazanik.com/marker_sm.png',
		title: 'Репетиционный комплекс Точка'
	});
	
	var infowindow_2_4 = new google.maps.InfoWindow({
		content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/rehearsal_bases/point">Репетиционный комплекс Точка</a></b><br/>ул. К. Чорного, 33а<br/><i style="color: red;">ст.м. Парк Челюскинцев</i></div>'
	});
	
	google.maps.event.addListener(marker_2_1, 'click', function() { infowindow_2_1.open(map_2, marker_2_1); });
	google.maps.event.addListener(marker_2_2, 'click', function() { infowindow_2_2.open(map_2, marker_2_2); });
	google.maps.event.addListener(marker_2_3, 'click', function() { infowindow_2_3.open(map_2, marker_2_3); });
	google.maps.event.addListener(marker_2_4, 'click', function() { infowindow_2_4.open(map_2, marker_2_4); });
	
	var metro_2_1 = new google.maps.Marker({
		position: new google.maps.LatLng(53.9051808,27.5399035), map: map_2, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Фрунзенская'
	});
	var metro_2_2 = new google.maps.Marker({
		position: new google.maps.LatLng(53.905462,27.554487), map: map_2, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Немига'
	});
	var metro_2_3 = new google.maps.Marker({
		position: new google.maps.LatLng(3.901529,27.560482), map: map_2, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Октябрьская'
	});
	var metro_2_4 = new google.maps.Marker({
		position: new google.maps.LatLng(53.893810,27.570505), map: map_2, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Первомайская'
	});
	var metro_2_5 = new google.maps.Marker({
		position: new google.maps.LatLng(53.924719,27.614419), map: map_2, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Парк Челюскинцев'
	});
	<?php } ?>
	
	<?php if (!$_GET['t'] or $_GET['t'] == 3) { ?>
	// Студия звукозаписи
	var map_3 = new google.maps.Map(document.getElementById('map-3'), {
		scrollwheel: true,
		zoom: 14,
		center: new google.maps.LatLng(53.889812, 27.573885),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	var marker_3 = new google.maps.Marker({
		position: new google.maps.LatLng(53.889812, 27.573885),
		map: map_3,
		icon: 'http://mazanik.com/marker_sm.png',
		title: 'Студия звукозаписи'
	});
	
	var metro_3_1 = new google.maps.Marker({
		position: new google.maps.LatLng(53.893810,27.570505), map: map_3, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Первомайская'
	});
	<?php } ?>
	
	<?php if (!$_GET['t'] or $_GET['t'] == 4) { ?>
	// Аренда оборудования
	var map_4 = new google.maps.Map(document.getElementById('map-4'), {
		scrollwheel: true,
		zoom: 12,
		center: new google.maps.LatLng(53.955466, 27.672004),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	var marker_4 = new google.maps.Marker({
		position: new google.maps.LatLng(53.955466, 27.672004),
		map: map_4,
		icon: 'http://mazanik.com/marker_sm.png',
		title: 'Аренда оборудования'
	});
	
	var metro_4_1 = new google.maps.Marker({
		position: new google.maps.LatLng(53.945519, 27.687501), map: map_4, icon: '/tmpl/site/images/metro.png',
		title: 'ст.м. Уручье'
	});
	<?php } ?>
});
</script>