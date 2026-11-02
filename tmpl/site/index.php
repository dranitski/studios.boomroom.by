<?php defined('access') or die ('no access'); ?>
<!DOCTYPE HTML>
<html>
<head>
<?php include path.'/includes/head.php'; ?>
</head>
<body>

<?php if ($view == 'repaby' or $view == 'rooms') { include path.'/includes/body.php'; } else { ?>
	
	<div id="wrapper">
		
	<?php if($view == 'home') { ?>
	
		<div id="header-h">
		
			<div id="head-h">
			
				<div class="fl">
					<div id="tmenu">
						<?php // insertBlock('menu_top'); ?>
					</div>
					<div class="clear"></div>
					<div id="social">
						<a id="vk" href="https://vk.com/boom.studio" target="_blank"></a>
						<a id="yt" href="https://www.youtube.com/channel/UCj21uTLwEQwZaxfKQYPKvog" target="_blank"></a>
						<a id="in" href="https://www.instagram.com/boomroom/" target="_blank"></a>
					</div>
					<a class="book book_home" href="/booking-rooms">Online Бронирование Репетиций</a>
					<!--<input id="callback" class="callback cki" type="button" value="ОБРАТНЫЙ ЗВОНОК"/>-->
					<div class="clear"></div>
				</div>
				
				<img id="logo-h" src="/tmpl/site/images/logo-h.png" alt="Музыкальная компания BOOM" title="Музыкальная компания BOOM"/>
				
				<div id="ph-t" class="phones ph-t">
					<div class="slider">
						<div class="slide">
							<p>Музыкальная школа</p>
							<a class="mts" href="tel:+375333582979">+375 33 <span>358-29-79</span></a>
							<a class="vel" href="tel:+375296582979">+375 29 <span>658-29-79</span></a>
						</div>
						<div class="slide">
							<p>Репетиционные студии</p>
							<a class="mts" href="tel:+375295714120">+375 29 <span>571-41-20</span></a>
							<a class="vel" href="tel:+375296366227">+375 29 <span>636-62-27</span></a>
							<a class="tel" href="tel:+375173272034">+375 17 <span> 327-20-34</span></a>
						</div>
						<div class="slide">
							<p>Студия звукозаписи</p>
							<a class="mts" href="tel:+375297657879">+375 33 <span>765-78-79</span></a>
						</div>
						<div class="slide">
							<p>Аренда оборудования</p>
							<a class="mts" href="tel:+375297767292">+375 29 <span>776-72-92</span></a>
							<a class="vel" href="tel:+375447767297">+375 44 <span>776-72-97</span></a>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			
			<!--
			<div id="menu">
				<?php // insertBlock('menu'); ?>
				<ul>
					<li><a href="/music_school">Музыкальная<br/>школа</a></li>
					<li><a href="/rehearsal_bases">Репетиционные<br/>студии</a></li>
					<li><a href="/recording_studio">Студия<br/>звукозаписи</a></li>
					<li><a href="/equipment_rental">Прокат музыкального<br/>оборудования</a></li>
				</ul>
			</div>
			-->
			<div id="menu">
				<?php // insertBlock('menu'); ?>
				<ul>
					<li><a href="/contacts">Музыкальная<br/>школа</a></li>
					<li><a href="/rehearsal_bases">Репетиционные<br/>студии</a></li>
					<li><a href="/contacts">Студия<br/>звукозаписи</a></li>
					<li><a href="/contacts">Прокат музыкального<br/>оборудования</a></li>
				</ul>
			</div>
		</div>
		
	<?php } else { ?>
		
		<div id="header-i">
		
			<div id="head-i">
			
				<div class="fl">
					<div id="tmenu">
						<?php // insertBlock('menu_top'); ?>
					</div>
					<div class="clear"></div>
					<div id="social">
						<a id="vk" href="https://vk.com/boom.studio" target="_blank"></a>
						<a id="yt" href="https://www.youtube.com/channel/UCj21uTLwEQwZaxfKQYPKvog" target="_blank"></a>
						<a id="in" href="https://www.instagram.com/boomroom/" target="_blank"></a>
					</div>
					<a class="book" href="/booking-rooms">Online Бронирование Репетиций</a>
					<!--<input id="callback" class="callback cki" type="button" value="ОБРАТНЫЙ ЗВОНОК"/>-->
					<div class="clear"></div>
				</div>
				
				<a href="/" title="Музыкальная компания BOOM"><img id="logo-i" src="/tmpl/site/images/logo-i.png" alt="Музыкальная компания BOOM"/></a>
				
				<div id="ph-t-i" class="phones">
					<?php if(strpos($_SERVER['REQUEST_URI'], "music_school")) { ?>
					<a class="mts" href="tel:+375333582979">+375 33 <span>358-29-79</span></a>
					<a class="vel" href="tel:+375296582979">+375 29 <span>658-29-79</span></a>
					<?php } elseif(strpos($_SERVER['REQUEST_URI'], "rehearsal_bases")) { ?>
					<a class="mts" href="tel:+375295714120">+375 29 <span>571-41-20</span></a>
					<a class="vel" href="tel:+375296366227">+375 29 <span>636-62-27</span></a>
					<a class="tel" href="tel:+375173272034">+375 17 <span> 327-20-34</span></a>
					<?php } elseif(strpos($_SERVER['REQUEST_URI'], "recording_studio")) { ?>
					<a class="mts" href="tel:+375297657879">+375 33 <span>765-78-79</span></a>
					<?php } elseif(strpos($_SERVER['REQUEST_URI'], "equipment_rental")) { ?>
					<a class="mts" href="tel:+375297767292">+375 29 <span>776-72-92</span></a>
					<a class="vel" href="tel:+375447767297">+375 44 <span>776-72-97</span></a>
					<?php } else { ?>
					<a class="mts" href="tel:+375295714120">+375 29 <span>571-41-20</span></a>
					<a class="vel" href="tel:+375296366227">+375 29 <span>636-62-27</span></a>
					<a class="tel" href="tel:+375173272034">+375 17 <span> 327-20-34</span></a>
					<!--
					<div class="slider">
						<div class="slide">
							<p>Музыкальная школа</p>
							<a class="mts" href="tel:+375333582979">+375 33 <span>358-29-79</span></a>
							<a class="vel" href="tel:+375296582979">+375 29 <span>658-29-79</span></a>
						</div>
						<div class="slide">
							<p>Репетиционные студии</p>
							<a class="mts" href="tel:+375295714120">+375 29 <span>571-41-20</span></a>
							<a class="vel" href="tel:+375296366227">+375 29 <span>636-62-27</span></a>
							<a class="tel" href="tel:+375173272034">+375 17 <span> 327-20-34</span></a>
						</div>
						<div class="slide">
							<p>Студия звукозаписи</p>
							<a class="mts" href="tel:+375297657879">+375 33 <span>765-78-79</span></a>
						</div>
						<div class="slide">
							<p>Аренда оборудования</p>
							<a class="mts" href="tel:+375297767292">+375 29 <span>776-72-92</span></a>
							<a class="vel" href="tel:+375447767297">+375 44 <span>776-72-97</span></a>
						</div>
					</div>
					-->
					<?php } ?>
				</div>
				
				<div class="clear"></div>
			</div>
			
			<!--
			<div id="menu">
				<ul>
					<li><a href="/music_school">Музыкальная<br/>школа</a></li>
					<li><a href="/rehearsal_bases">Репетиционные<br/>студии</a></li>
					<li><a href="/recording_studio">Студия<br/>звукозаписи</a></li>
					<li><a href="/equipment_rental">Прокат музыкального<br/>оборудования</a></li>
				</ul>
			</div>
			-->
			<div id="menu">
				<ul>
					<li><a href="/contacts?t=1">Музыкальная<br/>школа</a></li>
					<li><a href="/rehearsal_bases">Репетиционные<br/>студии</a></li>
					<li><a href="/contacts?t=3">Студия<br/>звукозаписи</a></li>
					<li><a href="/contacts?t=4">Прокат музыкального<br/>оборудования</a></li>
				</ul>
			</div>
			
		</div>
		
	<?php } ?>
	
	<?php insertBlock('slider'); ?>
	
	<?php if(strpos($_SERVER['REQUEST_URI'], "rehearsal_bases") or $getId == 4) { ?>
	
		<div id="sub1" class="smenu">
			<div>
				<?php insertBlock('menu_sub1'); ?>
			</div>
		</div>
	
	<?php } ?>
	
	<?php if(strpos($_SERVER['REQUEST_URI'], "music_school")) { ?>
	
		<div id="sub2" class="smenu">
			<div>
				<?php insertBlock('menu_sub2'); ?>
			</div>
		</div>
	
	<?php } ?>
		
		<div id="middle">
			
		<?php if($view == 'home') { ?>
				
			<div id="services">
				<a href="/music_school"><span>Музыкальная школа</span></a>
				<a href="/rehearsal_bases"><span>Репетиционные студии</span></a>
				<a href="/recording_studio"><span>Студия звукозаписи</span></a>
				<a href="/equipment_rental"><span>Прокат музыкального оборудования</span></a>
				<div class="clear"></div>
			</div>
			
			<div id="affiliates">
				<div class="title">НАШИ ФИЛИАЛЫ</div>
				<div id="affsMap"></div>
			</div>
			
			<?php insertBlock('events'); ?>
			
			<div id="about">
				
				<?php include path.'/includes/body.php'; ?>
				
			</div>
			
			<div id="facts">
			
				<div class="item">
					<div class="name">микрофонов</div>
					<div class="line"></div>
					<div id="f1" class="count" data-num="112">112</div>
				</div>
			
				<div class="item">
					<div class="count" data-num="36">36</div>
					<div class="line"></div>
					<div class="name">гитар</div>
				</div>
			
				<div class="item">
					<div class="name fs14">репетиционных студий</div>
					<div class="line"></div>
					<div class="count" data-num="10">10</div>
				</div>
			
				<div class="item">
					<div class="count" data-num="16">16</div>
					<div class="line"></div>
					<div class="name fs14">барабанных установок</div>
				</div>
			
				<div class="item">
					<div class="count fs50 mt30" data-num="55000">55000</div>
					<div class="line"></div>
					<div class="name">ватт звука</div>
				</div>
			
				<div class="item">
					<div class="name fs14">гитарных усилителей</div>
					<div class="line"></div>
					<div class="count" data-num="30">30</div>
				</div>
			
				<div class="item">
					<div class="name fs12 mt50">более <span class="count fs50" data-num="250">250</span> учеников</div>
					<div class="line"></div>
					<div class="name fs14">музыкальной школы</div>
				</div>
			
				<div class="item">
					<div class="name fs12 mt50">более <span class="count fs50" data-num="380">380</span> групп</div>
					<div class="line"></div>
					<div class="name fs14">репетируют у нас</div>
				</div>
			
			</div>
			
			<div id="ww">
				<div class="hib">
					<span id="hib1"></span>
					<h3>Что в начале?</h3>
					<p>
						Сотрудничая с нами, вы получаете возможность выступать на концертах и фестивалях, которые мы организовываем для ВАС. Мы предоставляем огромное количество бонусов и скидок для наших постоянных гостей. Если вы у нас в первый раз, мы найдем, чем Вас удивить. 
					</p>
					<div class="clear"></div>
				</div>
				<div class="hib">
					<span id="hib2"></span>
					<h3>Что потом?</h3>
					<p>
						Работая с нами, вы можете обзавестись пластиковой Картой Почётного Гостя от студии BOOM, которая сразу дарит вам 5%-ую скидку на все репетиции на всех наших студиях в любое время. Коллективы, которые с нами уже как минимум год, получают золотую Карту VIP-Гостя с 10%-ой скидкой на все репетиции. Ну а мэтры музыки, которые блистают на мировых сценах, получают платиновую Карту со скидкой в 30%.<br/>
						Обладая картой Почетного Гостя, вы также претендуете на скидки и приятные бонусы на услуги Проката профессионального музыкального и светового оборудования, и нашей студии звукозаписи «EverestMedia».<br/>
						Чтобы получить Карту Гостя, обращайтесь к администратору. 

					</p>
					<div class="clear"></div>
				</div>
				<div class="hib">
					<span id="hib3"></span>
					<h3>Что в итоге? </h3>
					<p>
						Мы создавали нашу компанию, опираясь на опыт профессиональных музыкантов, звукорежиссёров и сценических техников. Мы даем Вам прекрасные  условия, чтобы вы могли быстро и плодотворно двигаться к своей цели, развиваться музыкально, обладая всем необходимым под рукой.
					</p>
					<div class="clear"></div>
				</div>
			</div>
			
			<a href="#"><img id="bnnr1" src="/tmpl/site/images/bnnr1.jpg"/></a>
				
		<?php } else { ?>
		
			<?php if ($getId == 2 or $getView == 'news' or $getExt == 'booking') { ?>
			<div id="content" class="no-column">
				
				<?php include path.'/includes/body.php'; ?>
				
			</div>
			<?php } else { ?>
			
			<div id="column">
				
				<div id="cmenu">
					<?php insertBlock('menu_left'); ?>
				</div>
				
				<?php if(strpos($_SERVER['REQUEST_URI'], "events")) { insertBlock('events_left'); }?>
				
			</div>
			
			<div id="content">
				
				<?php include path.'/includes/body.php'; ?>
				
			</div>
			<?php } ?>
			
		<?php } ?>
			
		</div>
		
		<div id="footer">
			<div id="footer-sh"></div>
			<div id="foot">
				
				<div class="fl">
					<a href="/"><img id="logo-f" src="/tmpl/site/images/logo-f.png" alt="Музыкальная компания BOOM" title="Музыкальная компания BOOM"/></a>
					<div class="clear"></div>
					<div id="copy">© 2005-<?php echo date('Y'); ?></div>
				</div>
				
				<div id="bmenu">
					<?php // insertBlock('menu_bottom'); ?>
				</div>
				
				<div class="fr">
					<div id="ph-f" class="phones">
					
						<?php if(strpos($_SERVER['REQUEST_URI'], "music_school")) { ?>
						<a class="mts" href="tel:+375333582979">+375 33 <span>358-29-79</span></a>
						<a class="vel" href="tel:+375296582979">+375 29 <span>658-29-79</span></a>
						<?php } elseif(strpos($_SERVER['REQUEST_URI'], "rehearsal_bases")) { ?>
						<a class="mts" href="tel:+375295714120">+375 29 <span>571-41-20</span></a>
						<a class="vel" href="tel:+375296366227">+375 29 <span>636-62-27</span></a>
						<a class="tel" href="tel:+375173272034">+375 17 <span> 327-20-34</span></a>
						<?php } elseif(strpos($_SERVER['REQUEST_URI'], "recording_studio")) { ?>
						<a class="mts" href="tel:+375297657879">+375 33 <span>765-78-79</span></a>
						<?php } elseif(strpos($_SERVER['REQUEST_URI'], "equipment_rental")) { ?>
						<a class="mts" href="tel:+375297767292">+375 29 <span>776-72-92</span></a>
						<a class="vel" href="tel:+375447767297">+375 44 <span>776-72-97</span></a>
						<?php } else { ?>
						<div class="slider">
							<div class="slide">
								<p>Музыкальная школа</p>
								<a class="mts" href="tel:+375333582979">+375 33 <span>358-29-79</span></a>
								<a class="vel" href="tel:+375296582979">+375 29 <span>658-29-79</span></a>
							</div>
							<div class="slide">
								<p>Репетиционные студии</p>
								<a class="mts" href="tel:+375295714120">+375 29 <span>571-41-20</span></a>
								<a class="vel" href="tel:+375296366227">+375 29 <span>636-62-27</span></a>
								<a class="tel" href="tel:+375173272034">+375 17 <span> 327-20-34</span></a>
							</div>
							<div class="slide">
								<p>Студия звукозаписи</p>
								<a class="mts" href="tel:+375297657879">+375 33 <span>765-78-79</span></a>
							</div>
							<div class="slide">
								<p>Аренда оборудования</p>
								<a class="mts" href="tel:+375297767292">+375 29 <span>776-72-92</span></a>
								<a class="vel" href="tel:+375447767297">+375 44 <span>776-72-97</span></a>
							</div>
						</div>
						<?php } ?>
					</div>
					<div id="repa">
						<p>Наш партнер</p>
						<a href="http://repa.by/" target="_blank" title="REPA.BY - сервис онлайн-бронирования репетиций" class="logo"></a>
						<a href="http://repa.by/" target="_blank">Онлайн-бронирования репетиций</a>
					</div>
					<div id="share">
						<div class="title">ПОДЕЛИТЬСЯ С ДРУЗЬЯМИ</div>
						<script type="text/javascript">(function() {
						  if (window.pluso)if (typeof window.pluso.start == "function") return;
						  if (window.ifpluso==undefined) { window.ifpluso = 1;
						    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
						    s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
						    s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
						    var h=d[g]('body')[0];
						    h.appendChild(s);
						  }})();</script>
						<div class="pluso" data-background="transparent" data-options="big,round,line,horizontal,nocounter,theme=06" data-services="vkontakte,odnoklassniki,facebook,twitter,google"></div>
					</div>
				</div>
				
			</div>
		</div>
		
	</div>
	<?php if($view == 'home') { ?>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&language=ru"></script>
	<?php } ?>
	<script type="text/javascript" src="/tmpl/site/js/jquery.slides.min.js"></script>
	<script type="text/javascript">
		
		$(function(){
		
			<?php if($view == 'home') { ?>setMap();<?php } ?>
			
			$('.slider').slidesjs({
				
				width: 240,
				height: 170,
				play: {
					active: false,
					auto: true,
					interval: 3000,
					swap: true,
					pauseOnHover: true
				},
				navigation: {
					active: false
				},
				pagination: {
					active: false
				}	
			});
			
		});
		
		<?php if($view == 'home') { ?>
		function setMap() {
			
			var mapOptions = {
				
				scrollwheel: false,
				zoom: 12,
				center: new google.maps.LatLng(53.914338,27.567037),
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			
			var map = new google.maps.Map(document.getElementById('affsMap'), mapOptions);

			var marker_1 = new google.maps.Marker({
				position: new google.maps.LatLng(53.901173,27.555439),
				map: map,
				icon: 'http://mazanik.com/marker_sm.png',
				title: 'Репетиционный комплекс BOOM'
			});
			
			var infowindow_1 = new google.maps.InfoWindow({
				content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/rehearsal_bases/boom">Репетиционный комплекс BOOM</a></b><br/>ул. Ленина, 4<br/><i style="color: red;">ст.м. Октябрьская</i></div>'
			});
			
			var marker_2 = new google.maps.Marker({
				position: new google.maps.LatLng(53.889904,27.573893),
				map: map,
				icon: 'http://mazanik.com/marker_sm.png',
				title: 'Репетиционный комплекс Hendrix'
			});
			
			var infowindow_2 = new google.maps.InfoWindow({
				content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/rehearsal_bases/hendrix">Репетиционный комплекс Hendrix</a></b><br/>ул. Октябрьская, 16<br/><i style="color: red;">ст.м. Первомайская</i></div>'
			});
			
			var marker_3 = new google.maps.Marker({
				position: new google.maps.LatLng(53.902409,27.567621),
				map: map,
				icon: 'http://mazanik.com/marker_sm.png',
				title: 'Репетиционный комплекс Jam'
			});
			
			var infowindow_3 = new google.maps.InfoWindow({
				content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/rehearsal_bases/jam">Репетиционный комплекс Jam</a></b><br/>ул. К. Маркса, 50а<br/><i style="color: red;">ст.м. Октябрьская</i></div>'
			});
			
			var marker_4 = new google.maps.Marker({
				position: new google.maps.LatLng(53.929157,27.608635),
				map: map,
				icon: 'http://mazanik.com/marker_sm.png',
				title: 'Репетиционный комплекс Точка'
			});
			
			var infowindow_4 = new google.maps.InfoWindow({
				content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/rehearsal_bases/point">Репетиционный комплекс Точка</a></b><br/>ул. К. Чорного, 33а<br/><i style="color: red;">ст.м. Парк Челюскинцев</i></div>'
			});
			
			var marker_5 = new google.maps.Marker({
				position: new google.maps.LatLng(53.902436,27.543022),
				map: map,
				icon: 'http://mazanik.com/marker_sm.png',
				title: 'Музыкальная школа Black&White'
			});
			
			var infowindow_5 = new google.maps.InfoWindow({
				content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/music_school">Музыкальная школа Black&White</a></b><br/>ул. Короля, 6<br/><i style="color: red;">ст.м. Фрунзенская</i></div>'
			});
			
			var metro_1 = new google.maps.Marker({
				position: new google.maps.LatLng(53.9051808,27.5399035),
				map: map,
				icon: 'http://boomroom.by/tmpl/site/images/metro.png',
				title: 'ст.м. Фрунзенская'
			});
			
			var metro_2 = new google.maps.Marker({
				position: new google.maps.LatLng(53.905462,27.554487),
				map: map,
				icon: 'http://boomroom.by/tmpl/site/images/metro.png',
				title: 'ст.м. Немига'
			});
			
			var metro_3 = new google.maps.Marker({
				position: new google.maps.LatLng(53.901529,27.560482),
				map: map,
				icon: 'http://boomroom.by/tmpl/site/images/metro.png',
				title: 'ст.м. Октябрьская'
			});
			
			var metro_4 = new google.maps.Marker({
				position: new google.maps.LatLng(53.893810,27.570505),
				map: map,
				icon: 'http://boomroom.by/tmpl/site/images/metro.png',
				title: 'ст.м. Первомайская'
			});
			
			var metro_5 = new google.maps.Marker({
				position: new google.maps.LatLng(53.924719,27.614419),
				map: map,
				icon: 'http://boomroom.by/tmpl/site/images/metro.png',
				title: 'ст.м. Парк Челюскинцев'
			});
			
			// var infowindow_6 = new google.maps.InfoWindow({
				// content: '<div style="height: 100px; font-size: 18px;"><b style="font-size: 22px;"><a href="/music_school">Музыкальная школа Black&White</a></b><br/>ул. Короля, 6<br/><i style="color: red;">ст.м. Фрунзенская</i></div>'
			// });
			  
			google.maps.event.addListener(marker_1, 'click', function() { infowindow_1.open(map, marker_1); });
			google.maps.event.addListener(marker_2, 'click', function() { infowindow_2.open(map, marker_2); });
			google.maps.event.addListener(marker_3, 'click', function() { infowindow_3.open(map, marker_3); });
			google.maps.event.addListener(marker_4, 'click', function() { infowindow_4.open(map, marker_4); });
			google.maps.event.addListener(marker_5, 'click', function() { infowindow_5.open(map, marker_5); });
		}
		<?php } ?>
	</script>
<?php } ?>
</body>
</html>