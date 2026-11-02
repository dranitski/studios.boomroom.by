<?php
	
	defined('access') or die ('no access');
	
	include_once path."/functions/date.php";
	
?>
<div id="education" class="ms">
	
	<div class="desc">
		<h1>Современное музыкальное образование. Для каждого!</h1><br/></br>
		<h5>BooM</h5> – музыкальная школа в центре города Минска. Эта школа, в которую с радостью приходят наши ученики, эта школа, в которой мы с удовольствием занимаемся сами, и будем приводить на занятия своих детей. Это место встречи друзей, место интеллектуального отдыха и забвения. Отсюда нет желания уходить, и сюда можно всегда вернуться. Здесь нас любят и всегда ждут. Мы – группа действующих и профессиональных музыкантов создали это место для Вас.
		<br/><br/>
		<h5><?php echo date('j'); ?> <?php echo getMonth(date('n'), 2); ?> <?php echo date('Y'); ?></h5> – самый подходящий день, чтобы встать рядом с нами и покорять музыкальные высоты.<br/><h5>Просто позвони и запишись на первое бесплатное занятие.</h5>

	</div>
	
	<hr/>
	
	<div class="desc"><h1>Выбери свой инструмент</h1><br/><br/></div>
	
	<div id="lessons">
		
		<a href="/music_school/education/vocals">
			<span>Вокал</span>
		</a>
		
		<a href="/music_school/education/guitar">
			<span>Гитара</span>
		</a>
		
		<a href="/music_school/education/bass">
			<span>Бас-гитара</span>
		</a>
		
		<a href="/music_school/education/drums">
			<span>Барабаны</span>
		</a>
		
		<a href="/music_school/education/piano">
			<span>Фортепиано</span>
		</a>
		
		<a href="/music_school/education/solfeggio">
			<span>Сольфеджио</span>
		</a>
		
		<div class="clear"></div>
				
	</div>
	
	<div class="desc"><br/><h1>«Групповые курсы «Музыкальной школы «BooM»</h1><br/><br/></div>
	
	<div id="lessons_gr">
		
		<a href="/music_school/education/band">
			<span>Игра в группе</span>
		</a>
		
		<a href="/music_school/education/chorus">
			<span>Вокальный ансамбль</span>
		</a>
		
		<div class="clear"></div>
				
	</div>
	
	<hr/>
	
	<!--
	<div class="desc">
	
		<h1><a href="/music_school/videos_of_teachers">Видео наших преподавателей</a></h1>
		<iframe style="margin-top: 15px;" width="860" height="484" src="https://www.youtube.com/embed/11LVbxDH_Ec" frameborder="0" allowfullscreen></iframe>
		
		Яна исполняет песню Rihanna «Diamonds». Аккомпанемент — учитель фортепиано Марина.
		
		<div class="videos">
			<iframe width="285" height="200" src="https://www.youtube.com/embed/11LVbxDH_Ec" frameborder="0" allowfullscreen></iframe>
			<iframe width="285" height="200" src="https://www.youtube.com/embed/11LVbxDH_Ec" frameborder="0" allowfullscreen></iframe>
			<iframe width="285" height="200" src="https://www.youtube.com/embed/11LVbxDH_Ec" frameborder="0" allowfullscreen></iframe>
			<div class="clear"></div>
		</div>
		
	</div>
	-->
	
	<div class="desc"><h1><a href="/music_school/videos_of_teachers">Видео преподавателей музыкальной школы «BooM»</a></h1><br/><br/></div>
	<div id="videos">
		<div class="item">
		
			<div class="foto">
				<img src="/images/school/teachers/07.jpg"/>
				<h3>Алина Ягодная</h3>
				<span>Эстрадный, вокал, фортепиано, сольфеджио, композиция</span>
			</div>
			
			<div class="video">
				<iframe width="530" height="300" src="https://www.youtube.com/embed/67fktBLeUJI" frameborder="0" allowfullscreen></iframe>
				<audio src="/files/audio/67fktBLeUJI.mp3" controls></audio>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	
	<hr/>
	
	<div class="desc resons">
		<h2>5 причин, почему с нами</h2><br/><br/>
		<div class="rsn">
			<span id="rsn1"></span>
			<h3>Индивидуальный подход</h3>
			<p>
				- Мы составим для тебя подходящее расписание в зависимости от твоего свободного и рабочего времени<br/>
				- Мы будем обучать тебя по персонально составленной методике с учетом твоих целей и возможностей
			</p>
			<div class="clear"></div>
		</div>
		<div class="rsn">
			<span id="rsn2"></span>
			<h3>Профессиональное обучение</h3>
			<p>
				- Все занятия ведут профессиональные педагоги с высшим образованием<br/>
				- Все преподаватели – действующие музыканты, поэтому всегда в курсе современных веяний
			</p>
			<div class="clear"></div>
		</div>
		<div class="rsn">
			<span id="rsn3"></span>
			<h3>Возможность показать себя</h3>
			<p>
				- Участие в отчетных концертах школы в лучших клубах города гарантируем!<br/>
				- Мы даем тебе возможность выступить на мероприятиях любого уровня: от рядовых концертов до большой сцены<br/>
				- Ты можешь участвовать в записи на профессиональной студии
			</p>
			<div class="clear"></div>
		</div>
		<div class="rsn">
			<span id="rsn4"></span>
			<h3>Общение и контакты</h3>
			<p>
				- В нашей школе сплелись воедино судьбы людей разных возрастов, предпочтений и мышления, которые поддерживают друг друга в своих начинаниях<br/>
				- Наши преподаватели – это, в первую очередь, друзья, которые всегда выслушают и всегда поймут<br/>
				- Мы познакомим тебя с лучшими музыкантами, чтобы они рассказали тебе, через что прошли они
			</p>
			<div class="clear"></div>
		</div>
		<div class="rsn">
			<span id="rsn5"></span>
			<h3>Доступное образование за доступные деньги</h3>
			<p>
				- Демократичные цены, постоянные акции и скидки<br/>
				- Особо талантливые и целеустремлённые ученики обучаются бесплатно<br/>
				- Записи, концерты и другие мероприятия проводятся за счет школы
			</p>
			<div class="clear"></div>
		</div>
	</div>
	
	<hr/>
	
	<div class="desc"><h1><a href="/music_school/videos_of_learners">Видео учеников музыкальной школы «BooM»</a></h1><br/><br/></div>
	<div id="videos">
		<div class="item">
		
			<div class="foto">
				<img src="/images/school/students/alisa_vopnyarskaya.jpg"/>
				<h3>Даша Трумен</h3>
				<span>Эстрадный вокал</span>
			</div>
			
			<div class="video">
				<iframe width="530" height="300" src="https://www.youtube.com/embed/QhCJRiLOyPA" frameborder="0" allowfullscreen></iframe>
				<audio src="/files/audio/QhCJRiLOyPA.mp3" controls></audio>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	
	<!--
	<div class="desc">
	
		<h1><a href="/music_school/videos_of_learners">Видео учеников музыкальной школы «BooM»</a></h1>
		<iframe style="margin-top: 15px;" width="860" height="484" src="https://www.youtube.com/embed/11LVbxDH_Ec" frameborder="0" allowfullscreen></iframe>
		
		Евгений исполняет песню «Pastime Paradise» Стиви Уандера. Аккомпанемент — учителя школы.
		
		<div class="videos">
			<iframe width="285" height="200" src="https://www.youtube.com/embed/11LVbxDH_Ec" frameborder="0" allowfullscreen></iframe>
			<iframe width="285" height="200" src="https://www.youtube.com/embed/11LVbxDH_Ec" frameborder="0" allowfullscreen></iframe>
			<iframe width="285" height="200" src="https://www.youtube.com/embed/11LVbxDH_Ec" frameborder="0" allowfullscreen></iframe>
			<div class="clear"></div>
		</div>
		
	</div>
	-->
	
	<hr/>
	
	<div class="desc">Музыка – это любовь и жизнь, радость и счастье, смерть и отчаяние, гениальность и безумие. Ты создаешь музыку, потому что слышишь ее внутри. Музыка – это лучшая возможность научиться любить и понимать себя и других. Преподаватели музыкальной школы «Black&White», профессиональные действующие музыканты и просто добрые и искренние люди, помогут тебе познать все тайны и секреты этого удивительного и многогранного мира. Живи музыкой и будь с нами!
	</div>
	
	<hr/>
	
	<?php insertBlock('enroll'); ?>
	
	<hr/>

	<div class="desc">
	
		<h1>Наши кумиры</h1>
		
		<div style="text-align: left;">
			<blockquote style="">«Герои — не всегда те, кто побеждает. Иногда они проигрывают. Но они приходят в себя и продолжают сражаться. Они не сдаются. Вот что делает их героями». <p style="text-align: right;">(с) Кассандра Клэр. </p></blockquote><br/>
Многие интересуются, что мотивирует нас и наших учеников на кропотливые занятия и саморазвитие, что нас сплачивает, кроме музыки. Мы близки по духу и имеем общую цель. Каждый ученик, прежде всего наш друг, с которым мы можем поговорить о музыке, музыкальных предпочтениях, музыкальных коллективах и исполнителях.<br/><br/>
Вот список великих музыкантов, которые оставили свой след в истории музыки, это стена славы наших героев, которые каждый день вдохновляют нас на новые подвиги. Все они разные люди и преуспели в разных музыкальных жанрах, но их вклад в музыкальную сокровищницу неоценим.<br/><br/>
		</div>
		
		<div id="heroes">
			<div class="foto">
				<img src="/images/heroes/acdc.jpg"/>
				<span>ACDC</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/adele.jpg"/>
				<span>Adele</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/amy_winehouse.jpg"/>
				<span>Amy Winehouse</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/bbking.jpg"/>
				<span>BB King</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/bjork.jpg"/>
				<span>Bjork</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/elvis_presley.jpg"/>
				<span>Elvis Presley</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/eminem.jpg"/>
				<span>Eminem</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/eric_clapton.jpg"/>
				<span>Eric Clapton</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/jastin_timberleik.jpg"/>
				<span>Jastin Timberleik</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/jimi_hendrix.jpg"/>
				<span>Jimi Hendrix</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/jim_morrison.jpg"/>
				<span>Jim Morrison</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/kurt_cobain.jpg"/>
				<span>Kurt Cobain</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/led_zeppelin.jpg"/>
				<span>Led Zeppelin</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/linkin_park.jpg"/>
				<span>Linkin Park</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/madonna.jpg"/>
				<span>Madonna</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/michael_jackson.jpg"/>
				<span>Michael Jackson</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/muse.jpg"/>
				<span>MUSE</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/ray_charles.jpg"/>
				<span>Ray Charles</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/red_hot_chili_peppers.jpg"/>
				<span>Red Hot Chili Peppers</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/sting.jpg"/>
				<span>Sting</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/the_clash.jpg"/>
				<span>The Clash</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/the_ramones.jpg"/>
				<span>The Ramones</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/the_beatles.jpg"/>
				<span>The Beatles</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/the_prodigy.jpg"/>
				<span>The Prodigy</span>
			</div>
			<div class="foto">
				<img src="/images/heroes/thom_yorke.jpg"/>
				<span>Thom Yorke</span>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	
	<hr/>
	
	<div class="desc">
		<h2>Отчетные концерты</h2><br/><br/>
		Наша музыкальная школа регулярно проводит отчетные концерты для наших учеников и преподавателей.
		<br/>
		Теперь если Вы желаете выступить на большой сцене, мы любезно предоставим Вам такую возможность.<br/><br/>
	</div>
	
	<div class="gallery gnh">
		<a class="foto" href="/images/school/vocals/k_1.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_1.jpg"></a>
		<a class="foto" href="/images/school/vocals/k_2.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_2.jpg"></a>
		<a class="foto" href="/images/school/vocals/k_3.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_3.jpg"></a>
		<a class="foto" href="/images/school/vocals/k_4.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_4.jpg"></a>
		<a class="foto" href="/images/school/vocals/k_5.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_5.jpg"></a>
		<a class="foto" href="/images/school/vocals/k_6.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_6.jpg"></a>
		<a class="foto" href="/images/school/vocals/k_7.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_7.jpg"></a>
		<a class="foto" href="/images/school/vocals/k_8.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_8.jpg"></a>
		<a class="foto" href="/images/school/vocals/k_9.jpg" data-fancybox-group="gallery"><img src="/images/school/vocals/k_9.jpg"></a>
		<div class="clear"></div>
	</div>
	
	<hr/>
	
	<div class="desc">
		<h2>Внутри музыкальной школы «BooM»</h2><br/><br/>
		Это место, где царит атмосфера андеграунда, рока и музыкальной тусовки. Здесь по-домашнему уютно, всегда приятно выпить чашку чая с администратором или преподавателем и поговорить о музыке или о жизни. Тут все вдохновляет на занятие музыкой, тут легко быть собой и всегда хочется стать лучше.<br/><br/>
	</div>
	
	<div class="gallery gnw">
		<a class="foto" href="/images/school/1.jpg" data-fancybox-group="gallery"><img src="/images/school/1.jpg"></a>
		<a class="foto" href="/images/school/2.jpg" data-fancybox-group="gallery"><img src="/images/school/2.jpg"></a>
		<a class="foto" href="/images/school/3.jpg" data-fancybox-group="gallery"><img src="/images/school/3.jpg"></a>
		<a class="foto" href="/images/school/4.jpg" data-fancybox-group="gallery"><img src="/images/school/4.jpg"></a>
		<a class="foto" href="/images/school/5.jpg" data-fancybox-group="gallery"><img src="/images/school/5.jpg"></a>
		<a class="foto" href="/images/school/6.jpg" data-fancybox-group="gallery"><img src="/images/school/6.jpg"></a>
		<a class="foto" href="/images/school/7.jpg" data-fancybox-group="gallery"><img src="/images/school/7.jpg"></a>
		<a class="foto" href="/images/school/8.jpg" data-fancybox-group="gallery"><img src="/images/school/8.jpg"></a>
		<a class="foto" href="/images/school/9.jpg" data-fancybox-group="gallery"><img src="/images/school/9.jpg"></a>
		<a class="foto" href="/images/school/10.jpg" data-fancybox-group="gallery"><img src="/images/school/10.jpg"></a>
		<a class="foto" href="/images/school/11.jpg" data-fancybox-group="gallery"><img src="/images/school/11.jpg"></a>
		<a class="foto" href="/images/school/12.jpg" data-fancybox-group="gallery"><img src="/images/school/12.jpg"></a>
		<div class="clear"></div>
	</div>
	
	<hr/>
	
	<div class="desc">
		<h2>Отзывы наших учеников</h2><br/><br/>
		
		<div class="comments">
			<div class="item">
				<blockquote>
					«Я занимаюсь вокалом. Были и данные, и возможности, и желание с малых лет. Преподавателем по данной дисциплине является Маргарита Алешкевич. С ней приятно учиться из-за её настроения и общения с учениками и её требованиями.
	Занятия всегда проходят легко, интересно и познавательно. Обстановка в самой студии очень приятная. Менеджеры всегда приветливы и готовы помочь...»
				</blockquote>
				<img src="/images/school/students/aleksei_novodvorskii.jpg">
				<h5>Алексей, 18 лет</h5>
			</div>
			<div class="item">
				<blockquote>
					«Замечательное, располагащее к саморазвитию место, с понимающимии и поддерживающими людьми, талантливыми преподавателями и работящими администраторами.»
				</blockquote>
				<img src="/images/school/students/darya_palchik.jpg">
				<h5>Даша, 17 лет</h5>
			</div>
			<div class="item">
				<blockquote>
					«История начинается почти год назад, когда я впервые пришел на студию. И сразу меня встретила приветливая и веселая девочка:) на ресепшене, которая ответила на мои вопросы и помогла выбрать удобное время для занятий. Придя на первое занятие я надеялся получить хоть какие-то простые знания, но уже через пару занятий я знал такие вещи о которых не рассказано ни в каких самоучителях и умельцах в интернете...»
				</blockquote>
				<img src="/images/school/students/aleksandr_polyko.jpg">
				<h5>Александр, 22 года</h5>
			</div>
		</div>
		
		<div class="clear"></div>
	</div>
	
	<hr/>
	
	<div class="desc">
		<h2>Как проходят занятия</h2><br/><br/>
		
		<h5>Индивидуальные и групповые занятия</h5><br/><br/>
		<p>
			Групповые занятия дают возможность учиться не только у преподавателя, но и друг у друга. Ученики поддерживают друг друга в успехах и неудачах. На индивидуальных занятиях ты обучаешься один на один с преподавателем.
		</p>
		<br/>
		<h5>Теоретические занятия</h5><br/><br/>
		<p>
			В нашей школе ученики обучаются не только игре на инструменте, каждый получает знания базовой музыкальной теории: нотная грамота, музыкальные диктанты, построение аккордов. Более того, слух и чувство ритма можно развивать, чем мы и займёмся.  Для желающих углубиться в дебри музыкальной науки есть курсы по композиции, аранжировке и гармонии. 
		</p>
		<br/>
		<h5>Практические занятия</h5><br/><br/>
		<p>
			На занятиях по любому инструменту ты сможешь практически изучить всё: от технических навыков до свободной импровизации - от А до Я. 
		</p>
		<br/>
		<h5>Домашнее задание</h5><br/><br/>
		<p>
			Основная работа происходит на занятиях с преподавателем. Но преподаватель указывает только путь, многое зависит от твоих собственных усилий и занятий дома. Чем больше вы занимайтесь сейчас – тем профессиональнее вы станете потом.
		</p>
		<br/>
		<h5>Концерты</h5><br/><br/>
		<p>
			У каждого ученика есть возможность выступить на отчетном концерте и показать, чему он научился. Мы организуем для тебя репетиции, запись или джемы с преподавателями, главное – твое усердие и желание.
		</p>
	</div>
	
	<hr/>
	
	<div class="desc faq">
		<h2>Ответы на популярные вопросы</h2><br/><br/>
		
		<table>
			<tr>
				<td>
					<span>1</span>
					<h5>Что делать, если у меня нет слуха?</h5><br/><br/>
					<p>
						Слух есть у каждого человека, просто у всех он развит по-разному от природы. Постоянная практика – и вы сможете поспорить даже с «абсолютниками».
					</p>
				</td>
				<td>
					<span>2</span>
					<h5>Когда можно записаться в вашу школу?</h5><br/><br/>
					<p>
						Прямо сейчас! В любую пору года, любое время, в любой день без выходных и перерывов на обед мы ждём тебя и готовы приступить к работе. 
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<span>3</span>
					<h5>Как проходит занятие?</h5><br/><br/>
					<p>
						По любому курсу и инструменту ты можешь выбрать, как ты хочешь обучаться – индивидуально или в группе (3 человека максимум). Преподаватель отдаёт тебе все свои знания и накопленный опыт. Когда-то он тоже учился, так же, как и ты, поэтому он знает, с чем ты можешь столкнуться и как этого избежать. Это ускорит и максимально облегчит весь процесс обучения.
					</p>
				</td>
				<td>
					<span>4</span>
					<h5>Как часто нужно заниматься самостоятельно?</h5><br/><br/>
					<p>
						Все зависит от того, чего ты хочешь достичь. Хочешь жить музыкой, колесить по белому свету с турами, собирать десятки тысяч на концертах – 6 – 7 часов в день каждый день, и все получится. Хочешь просто расслабиться после работы или учебы, приобрести полезное хобби – достаточно всего 40 – 60 мин в день регулярно. Дисциплина – залог успеха. 
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<span>5</span>
					<h5>Какие музыкальные предпочтения у вашей школы?</h5><br/><br/>
					<p>
						В нашей школе сплелись судьбы людей с разными музыкальными вкусами, что не мешает им сосуществовать друг с другом. Музыкальное направление: от джаза и блюза до рока и поп-музыки - выбираешь ты сам, а мы научим тебя базовым основам и секретам мировых звёзд.
					</p>
				</td>
				<td>
					<span>6</span>
					<h5>Какая у вас методика обучения?</h5><br/><br/>
					<p>
						Для каждого ученика подбирается индивидуально своя методика обучения, согласно целям и предпочтениям. С тебя – терпение и труд, с нас – новые знания и навыки. Честный обмен.
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<span>7</span>
					<h5>Какие мероприятия проходят в вашей школе?</h5><br/><br/>
					<p>
						Ученикам, которые усердно трудились и достигли успеха в своей области, предоставляется возможность участвовать в отчетных концертах в лучших залах Города-Героя, записи в профессиональной студии. 
					</p>
				</td>
				<td>
					<span>8</span>
					<h5>Как оплатить занятия?</h5><br/><br/>
					<p>
						Оплачивать нужно до 5-ого числа месяца за текущий месяц Оплатить можно по квитанции в любом отделении банка. Квитанцию ты можешь получить у администратора. 
					</p>	
				</td>
			</tr>
			<tr>
				<td>
					<span>9</span>
					<h5>Какие ограничения по возрасту?</h5><br/><br/>
					<p>
						Абсолютно никаких ограничений. Наши двери открыты для каждого. 
					</p>
				</td>
				<td>
				</td>
			</tr>
		</table>
		
	</div>
	
	<hr/>
	
	<?php insertBlock('question'); ?>
	
	<hr/>
	
	<img src="/tmpl/site/images/cit2.jpg"/>
	
</div>

<link type="text/css" href="/tmpl/site/css/jquery.fancybox.css?v=2.1.5" rel="stylesheet" media="screen" />
<script type="text/javascript" src="/tmpl/site/js/jquery.fancybox.js?v=2.1.5"></script>
<script type="text/javascript" src="/tmpl/site/js/jquery.fancybox-media.js?v=1.0.6"></script>
<script type="text/javascript">
$(function() {

	// Галерея
	$('.gallery .foto').fancybox();
});
</script>