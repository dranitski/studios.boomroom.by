<?php
	
	defined('access') or die ('no access');
	
	$task = getVar('task', 'post');
	$subtask = getVar('subtask', 'post');
	$option = getVar('option', 'post');
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	include_once path."/functions/events.php";
	
	switch ($task) {
	
		case 'client':
			
			switch ($subtask) {
		
				case 'check':
				
					$card = getVar('card', 'post');
					$pass = getVar('pass', 'post');
			
					$id = ($card !== '' and $pass !== '') ? loadResult("SELECT `id` FROM `clients` WHERE `card` = '{$card}' AND `pass` = '{$pass}'") : null;
					
					// данные клиента отдаются только после проверки карты и пароля в этой сессии
					$_SESSION['booking_cid'] = $id ? (int) $id : 0;
					
					if (!$id) {
						// замедляет перебор 4-значных паролей
						sleep(1);
					}
					
					if ($id) {
						
						?>
						$('#card input[type="button"]').attr('data-state', 1).data('state', 1);
						$('#client').data('id', <?php echo $id; ?>);
						<?php
					}
						
				break;
		
				case 'get':
					
					$id = getInt('id', 'post');
					
					if (!$id or empty($_SESSION['booking_cid']) or $_SESSION['booking_cid'] != $id) {
						header('HTTP/1.1 403 Forbidden');
						exit;
					}
					
					$client = loadObject("SELECT * FROM `clients` WHERE `id` = {$id}");
					$contacts = loadObjectList("SELECT * FROM `client_contacts` WHERE `cid` = {$id}");
					$count_events = loadResult("SELECT COUNT(*) FROM `events` WHERE `cid` = {$id} AND `state` = 2");
					/*
					$contact_list = array();
					if (count($contacts)) {
						
						foreach ($contacts as $contact) {
						
							$string = "<b>{$contact->phone}</b>";
							$string .= ($contact->name) ? " ({$contact->name})" : "";
							
							$contact_list[] = $string;
						}
					}
				
					?>
					<?php if ($client->note) { ?><i><?php echo $client->note; ?></i><?php } ?>
					<?php if (count($contact_list)) { ?>
					<p>Контакты</p>
					<?php echo implode(", ", $contact_list); ?>
					<?php } ?>
					*/
					?>
					
					<div class="info">
						<div class="name"><?php echo $client->name; ?></div>
						<?php if (cnt($contacts)) { $x = 0; foreach ($contacts as $contact) { if ($x == 2) break; ?>
						<p><?php echo $contact->phone; ?> <span>(<?php echo $contact->name; ?>)</span></p>
						<?php $x++; } } ?>
					</div>
					<div class="card" data-dis="<?php echo getClientLevel($count_events, 1); ?>">
						<div class="name">№ карты <b><?php echo $client->card; ?></b></div>
						<p>Уровень: <b><?php echo getClientLevel($count_events); ?></b> <span>-<?php echo getClientLevel($count_events, 1); ?>%</span></p>
						<p>Репетиций: <b><?php echo $count_events; ?></b> / До след. уровня: <b><?php echo getClientLevel($count_events, 2); ?></b></p>
					</div>
					<div class="clear"></div>
					
					<?php
					
				break;
			}
			
		break;
	
		case 'event':
		
			switch ($subtask) {
	
				case 'new':
				
					$repaby = getInt('repaby', 'post');

					$tid = getInt('tid', 'post');
					$date = getVar('date', 'post');
					
					$template = loadObject("SELECT * FROM `event_templates` WHERE `id` = {$tid}");
					
					$rid = $template->rid;
					$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
					$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
					$day = $template->day;
					$dayWeek = getDayWeek($day, true);
					$start = $template->start;
					$end = $template->end;
					
					$price = json_decode($template->price);
					
					$count = ($start < 23) ? $end - $start : 7;
					
					$ends = array();
					
					$ends[] = (object) array('end' => $template->end, 'count' => $count, 'price' => $price);
					
					if ($start < 23) {
					
						while ($template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND (`date` = '{$date}' OR `date` = '0000-00-00') AND `day` = {$day} AND `start` = {$end} ORDER BY `date` DESC")) {
						
							// Проверка на существования события
							if (loadResult("SELECT `id` FROM `events` WHERE `rid` = {$rid} AND `date` = '{$date}' AND `start` = {$template->start}")) break;
						
							// Проверка на существования постоянки
							if ($weekly = loadObject("SELECT * FROM `event_weekly` WHERE `rid` = {$rid} AND `day` = {$day} AND `start` = {$template->start}")) {
								
								if (in_array($date, explode(",", $weekly->nodates)) == false) break;
							}
							
							$price = json_decode($template->price);
							
							$count = $template->end - $start;
							
							$ends[] = (object) array('end' => $template->end, 'count' => $count, 'price' => $price);
							
							$end = $template->end;
						}
					}
					else {
						
						?>
						<div class="title">Бронирование репетиции</div>
						<?php if ($repaby) { ?><div class="close" onclick="$('#iw').hide();">&times;</div><?php } ?>
						<div style="margin: 15px 0;
    line-height: 1.4;
    font-size: 16px;
    text-align: center;">
							Бронирование ночной репетиции только по договоренности с администратором по телефонам:<br/>
							<b>+375 33 358-29-79<br/>
							+375 29 658-29-79</b>
							<br/>
							<small>Ночные репетиции в день не принимаются</small>
						</div>
						<?php
						exit;
					}
					
					// Клиенты
					// $clients = loadObjectList("SELECT * FROM `clients` ORDER BY `name` ASC");
					$studio = loadObject("SELECT * FROM `studios` WHERE `id` = {$room->sid}");
					
					?>
					<div class="data"
					
						data-id="0"
						data-rid="<?php echo $rid; ?>"
						data-date="<?php echo $date; ?>"
						data-start="<?php echo $start; ?>"
					
					></div>
					
					<?php /* if ($repaby) { ?>
					<div class="title">Бронирование репетиции</div>
					<div class="close" onclick="$('#iw').hide();">&times;</div>
					<div class="place"><span class="studio"><?php echo $studio->name; ?></span>, <span class="room"><?php echo $room->name; ?></span></div>
					<?php } else { ?>
					<div class="title">Бронирование репетиции</div>
					<?php } */?>
					<div class="title">Бронирование репетиции</div>
					<?php if ($repaby) { ?><div class="close" onclick="$('#iw').hide();">&times;</div><?php } ?>
					<div class="place"><span class="studio"><?php echo $studio->name; ?></span>, <span class="room"><?php echo $room->name; ?></span></div>
					
					<div class="datetime">
						<div class="date"><b><?php echo date('d.m.Y', strtotime($date)); ?></b>, <?php echo $dayWeek; ?></div>
						<div class="time">
							<?php echo $start; ?>:<?php echo $min; ?> -
							<select name="end" onchange="changeEnd();">
							<?php foreach($ends as $end) {
								
								$price_0 += $end->price[0];
								$price_1 += $end->price[1];
								$price_2 += $end->price[2];
							?>
								<option value="<?php echo $end->end; ?>" data-h="<?php echo $end->count; ?>" data-price_0="<?php echo $price_0; ?>" data-price_1="<?php echo $price_1; ?>" data-price_2="<?php echo $price_2; ?>"><?php echo $end->end; ?>:<?php echo $min; ?></option>
							<?php } ?>
							</select>
						</div>
						<div class="clear"></div>
					</div>
					<div id="client" data-id="0"></div>
					<div class="client">
						<div class="add">
							<select name="client_type">
								<option value="1">Группа</option>
								<option value="2">Музыкант</option>
							</select>
							<input name="client_name" type="text" placeholder="Название / Имя"/>
							<div class="phone">
								<span>+375</span>
								<select name="client_phone_code">
									<option value="29">29</option>
									<option value="44">44</option>
									<option value="33">33</option>
									<option value="25">25</option>
								</select>
								<input name="client_phone_number" type="text" 
								maxlength="7" placeholder="XXXXXXX"/>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
							<div id="card">
								<label>№ скидочной карты</label>
								<input name="card" type="text" maxlength="4" placeholder="XXXX"/>
								<label style="width: 56px; padding: 0 0 0 10px;">Пароль</label>
								<input name="pass" type="text" maxlength="4" placeholder="XXXX"/>
								<input type="button" value="Войти" data-state="0"/>
								<div class="clear"></div>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="nm">
						<label><input name="nm[]" type="radio" value="0" checked onclick="setPrice();" /> Более 2-x</label>
						<label><input name="nm[]" type="radio" value="1" onclick="setPrice();"/>1 музыкант</label>
						<label><input name="nm[]" type="radio" value="2" onclick="setPrice();"/>2 музыканта</label>
					</div>
					<div id="rent">
						<p>Аренда оборудования</p>
						<div class="list">
						</div>
					</div>
					<div class="prices">
						<div class="price">
							<label>Стоимость</label>
							<p data-type="price"></p>
							<div class="clear"></div>
						</div>
						<div class="price dis">
							<label>Стоимость со скидкой</label>
							<p data-type="dis"></p>
							<div class="clear"></div>
						</div>
					</div>
					<div class="note">
						<textarea name="note" placeholder="Комментарий"></textarea>
					</div>
					<div class="actions">
						<p class="book" onclick="saveEvent(this);">Забронировать</p>
						<div class="clear"></div>
					</div>
					<?php
				
				break;
				
				case 'save':
				
					$repaby = getInt('repaby', 'post');
					
					$rid = getInt('rid', 'post');
					$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
					$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
					$date = getVar('date', 'post');
					$start = getInt('start', 'post');
					$end = getInt('end', 'post');
					$nm = getInt('nm', 'post');
					$client_type = getInt('client_type', 'post');
					$client_name = getVar('client_name', 'post');
					$client_phone = getVar('client_phone', 'post');
					$note = getVar('note', 'post');
					$cid = getInt('cid', 'post');
					$eqs = getEqs();
					$price = getVar('price', 'post');
					
					// бронь от имени клиента - только после проверки его карты
					if ($cid and (empty($_SESSION['booking_cid']) or $_SESSION['booking_cid'] != $cid)) {
						$cid = 0;
					}
					
					if (!$room or $start < 0 or $start > 23 or $end < 0 or $end > 24 or !cnt(eventHours($start, $end)) or cnt(eventHours($start, $end)) > 12 or !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
						header('HTTP/1.1 400 Bad Request');
						exit;
					}
					
					if ($cid) {
						
						$client = loadObject("SELECT * FROM `clients` WHERE `id` = {$cid}");
						$contacts = loadObjectList("SELECT * FROM `client_contacts` WHERE `cid` = {$cid}");
						$count_events = loadResult("SELECT COUNT(*) FROM `events` WHERE `cid` = {$cid} AND `state` = 2");
						
						$client_type = $client->type;
						$client_name = $client->name;
						$client_phone = $contacts[0]->phone;
						
						$card = $client->card;
					}
					
					$noteTxt = ($client_type == 1) ? "Группа" : "Музыкант";
					$noteTxt .= ": {$client_name}";
					$noteTxt .= ", {$client_phone}";
					$noteTxt .= ($card) ? "\n№ карты: {$card}" : "";
					$noteTxt .= ($note) ? "\nКомментарий: {$note}" : "";
					
					$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
					$studio = loadObject("SELECT * FROM `studios` WHERE `id` = {$room->sid}");
					
					// Проверка на занятое время
					$hours = eventHours($start, $end);
					
					$WHERE = "(".implode(' BETWEEN `start` AND (`end` -1) OR ', $hours)." BETWEEN `start` AND (`end` -1))";
					
					$isHave = (loadResult("SELECT `id` FROM `events` WHERE `rid` = {$rid} AND `date` = '{$date}' AND {$WHERE}")) ? true : false;
					
					if ($isHave == false) {
						
						$sqlNote = $noteTxt;
						if ($cid) { $sqlNote = $note; }
						
						if ($repaby) { $sqlNote .= "\nОт REPA.BY"; }
						
						// $repaby_state = ($repaby) ? 1 : 0;
						
						runSql("INSERT INTO `events` (`rid`, `date`, `start`, `end`, `cid`, `nm`, `note`, `eqs`, `price`, `state`, `repaby`) VALUES ({$rid}, '{$date}', '{$start}', '{$end}', {$cid}, '{$nm}', '{$sqlNote}', '{$eqs}', '{$price}', 3, '{$repaby}')"); $id = insertId();
						
						// Журнал
						$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
						$date = date('d.m.Y', strtotime($event->date));
						$log = "Добавление события (заявка). ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
						addLog($log);
					}
					
					if ($isHave) {
					?>
					<div class="result fail">
						<p>Извините, но это время уже недоступно!</p>
						Кто-то другой немного опередил вас...<br/><br/>
						<div class="links">
							<a href="/">Главная</a><br/>
							<a href="/music_school">Музыкальная школа</a><br/>
							<a href="/rehearsal_bases">Репетиционные студии</a><br/>
							<a href="/recording_studio">Студия звукозаписи</a><br/>
							<a href="/equipment_rental">Прокат оборудования</a><br/>
						</div>
					</div>
					<?php } else { ?>
					<div class="result success">
						<p>Ваша репетиция забронирована!</p>
						<!--В ближайшее время мы свяжемся с Вами по номеру <?php echo $client_phone; ?> для подтверждения.-->
						<div class="details">
							Место: <?php echo $studio->name; ?>, <?php echo $room->name; ?><br/>
							Дата: <?php echo date('d.m.Y', strtotime($date)); ?><br/>
							Время: <?php echo $start; ?>:<?php echo $min; ?> - <?php echo $end; ?>:<?php echo $min; ?><br/><br/>
							<?php echo $noteTxt; ?>
						</div>
						<div class="links">
						<?php if ($repaby) { ?>
							<a href="javascript:void(0);" onclick="location.reload();">Назад к расписанию</a><br/>
						<?php } else { ?>
							<a href="/">Главная</a><br/>
							<a href="/music_school">Музыкальная школа</a><br/>
							<a href="/rehearsal_bases">Репетиционные студии</a><br/>
							<a href="/recording_studio">Студия звукозаписи</a><br/>
							<a href="/equipment_rental">Прокат оборудования</a><br/>
						<?php } ?>
						</div>
					</div>
					<?php
					}
					
					
					// Отправить запись в REPA.BY
					if ($isHave == false and $repaby) {
						
						$data = (object) array(
							'sid' => $studio->id,
							'rid' => $rid,
							'date' => $date,
							'start' => $start.":".$min,
							'end' => $end.":".$min,
							'name' => $client_name,
							'phone' => $client_phone,
							'noteTxt' => $noteTxt,
							'price' => $price,
							'ip' => $_SERVER['REMOTE_ADDR']
						);
						
						$data = urlencode(json_encode($data, JSON_UNESCAPED_UNICODE));
						
						// file_get_contents("http://repa.by/update.php?cmd=10&data={$data}");
						repaNotify("https://repa.by/action?ext=update&view=bases&action=boom&data={$data}");
					}
					
				break;
				
			}
		
		break;
		
		case 'eqs':
			
			switch ($subtask) {
			
				case 'get':
				
					$rid = getInt('rid', 'post');
					$date = getVar('date', 'post');
					$start = getInt('start', 'post');
					$end = getInt('end', 'post');
					
					$sid = loadResult("SELECT `sid` FROM `rooms` WHERE `id` = {$rid}");
					
					// Оборудование
					$equipments = loadObjectList("SELECT * FROM `equipments` WHERE `sid` = {$sid} AND `count` > 0");
					
					if (cnt($equipments)) {
					
						$eqs_free = getFreeEqs($equipments, $sid, $date, $start, $end);
					
						foreach ($equipments as $eq) {
					
							if ($eqs_free[$eq->id] < 1) { continue; }
						?>
						<div class="item" data-id="<?php echo $eq->id; ?>" data-count="<?php echo $eqs_free[$eq->id]; ?>" data-price="<?php echo $eq->price; ?>" data-rent="0">
							<div class="count"><?php echo $eqs_free[$eq->id]; ?></div>
							<div class="actions">
								<span onclick="toRent(0, <?php echo $eq->id; ?>);">-</span>
								<span onclick="toRent(1, <?php echo $eq->id; ?>);">+</span>
							</div>
							<div class="name"><?php echo $eq->name; ?> <span>(<?php echo $eq->price; ?> / час)</span></div>
							<div class="rent">-</div>
							<p class="clear"></p>
						</div>
						<?php
						}
					
					}
				
				break;
			
			}
			
		break;
	}
	
	
?>