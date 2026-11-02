<?php
	
	defined('access') or die ('no access');
	
	$subtask = getVar('subtask', 'post');
	$option = getVar('option', 'post');
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	include_once path."/functions/events.php";
	
	switch ($subtask) {
	
		case 'event':
		
			switch ($option) {
	
				case 'new':
				
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
					
						while ($end > 0 and $end < 24 and $template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND (`date` = '{$date}' OR `date` = '0000-00-00') AND `day` = {$day} AND `start` = {$end} ORDER BY `date` DESC")) {
						
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
					
					// Клиенты
					$clients = loadObjectList("SELECT * FROM `clients` ORDER BY `name` ASC");
					
					?>
					<div class="data"
					
						data-id="0"
						data-rid="<?php echo $rid; ?>"
						data-date="<?php echo $date; ?>"
						data-start="<?php echo $start; ?>"
					
					></div>
					
					<div class="title">Создание события, <?php echo $room->name; ?></div>
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
					<div class="client">
						<input name="card" type="text" value="" placeholder="№ карты, имя" onchange="findClient(this.value);" onkeyup="findClient(this.value);"/>
						<select name="cid" onchange="getClient(this.value);"><option value="-1">Выбрать клиента</option><option value="0">Неопределенный</option>
						<?php foreach($clients as $client) {
							
							// $client->dis = 0;
							if ($client->card) {
								
								$count_events = clientEventsCount($client->id);
								$client->dis = ($client->dis > 0) ? $client->dis : getClientLevel($count_events, 1);
							}
							
							if ($client->type == 3) { $client->card = 9999; $client->dis = 30; }
						?>
							<option value="<?php echo $client->id; ?>" data-card="<?php echo $client->card; ?>" data-dis="<?php echo $client->dis; ?>"><?php echo $client->name; ?></option>
						<?php } ?>
						</select>
						<div class="add">
							<p>Новый клиент</p>
							<select name="client_type">
								<option value="1">Группа</option>
								<option value="2">Музыкант</option>
							</select>
							<input name="client_name" type="text" value="" placeholder="Название / Имя"/>
							<input name="client_phone" type="text" value="" placeholder="Телефон" onchange="findClientPhone(this.value);" onkeyup="findClientPhone(this.value);"/>
							<div class="clear"></div>
						</div>
						<div class="info">
						</div>
					</div>
					<div class="findByPhoneList"></div>
					<div class="nm">
						<label><input name="nm[]" type="radio" value="0" checked onclick="setPrice();" /> Более 2-x</label>
						<label><input name="nm[]" type="radio" value="1" onclick="setPrice();"/>1 музыкант</label>
						<label><input name="nm[]" type="radio" value="2" onclick="setPrice();"/>2 музыканта</label>
					</div>
					<div id="rent">
						<h3>Аренда оборудования</h3>
						<div class="list">
						</div>
						<div class="total"><h3>Стоимость оборудования</h3><span id="price-eqs">0</span><div class="clear"></div></div>
					</div>
					<div class="price">
						<h3>Стоимость репетиции <p>Без скидки</p></h3>
						<input name="price" type="text" onchange="changePrice(this.value);" disabled />
						<label id="price-edits">Правка админа <input type="checkbox" value="1" onchange="priceEdits();" /></label>
						<div class="clear"></div>
						<select name="pay_type">
							<option value="1">Наличные</option>
							<option value="2">Безнал</option>
						</select>
						<h3>Общая стоимость: <span id="price-total">0</span> руб.</h3>
						<label id="price-free">Бесплатно <input type="checkbox" value="1" onchange="priceFree();" /></label>
						<div class="clear"></div>
					</div>
					<div class="note">
						<textarea name="note" placeholder="Комментарий"></textarea>
					</div>
					<div class="actions">
						<p class="save" onclick="saveEvent(this);">Сохранить</p>
						<div class="clear"></div>
					</div>
					<?php
				
				break;
				
				case 'save':
					
					$apply = getInt('apply', 'post');
					
					$id = getInt('id', 'post');
					$rid = getInt('rid', 'post');
					$date = getVar('date', 'post');
					$start = getInt('start', 'post');
					$end = getInt('end', 'post');
					$cid = getInt('cid', 'post');
					$nm = getInt('nm', 'post');
					$client_type = getInt('client_type', 'post');
					$client_name = getVar('client_name', 'post');
					$client_phone = getVar('client_phone', 'post');
					$note = getVar('note', 'post');
					$eqs = getEqs();
					$price = getVar('price', 'post');
					$price_eqs = getVar('price_eqs', 'post');
					$pay_type = getInt('pay_type', 'post');
					$dis = getVar('dis', 'post');
					$p = getInt('p', 'post');
					$free = getInt('free', 'post');
					
					// Добавление клиента
					if (($client_name or $client_phone) and $cid <= 0) {
					
						$client_name = ($client_name) ? $client_name : $client_phone;
						
						runSql("INSERT INTO `clients` (`type`, `name`) VALUES ({$client_type}, '{$client_name}')");
						$cid = insertId();
						toSyncItem(1, 'clients', $cid);
						
						if ($client_name) {
						
							$contact_name = ($client_type == 2) ? $client_name : "";
							
							runSql("INSERT INTO `client_contacts` (`cid`, `name`, `phone`) VALUES ({$cid}, '{$contact_name}', '{$client_phone}')"); $contact_id = insertId();
							
							$contacts = [];
							$contacts[] = (object)[
								'name' => $contact_name,
								'phone' => $client_phone,
								'email' => '',
								'vk_id' => '',
								'vk_addr' => ''
							];
							$contacts = json_encode($contacts, JSON_UNESCAPED_UNICODE);
							runSql("UPDATE `clients` SET `contacts` = '{$contacts}' WHERE `id` = {$cid}");
							
							toSyncItem(1, 'client_contacts', $contact_id);
						}
							
						// Журнал
						$log = "Добавление клиента. {$client_name}. ID:{$cid}";
						addLog($log);
					}
					
					if ($id) {
						
						$SETSTATE = ($apply == 1) ? ", `state` = 0" : "";
						
						runSql("UPDATE `events` SET `end` = {$end}, `cid` = {$cid}, `nm` = {$nm}, `note` = '{$note}', `eqs` = '{$eqs}', `price_eqs` = '{$price_eqs}', `price` = '{$price}', `pay_type` = '{$pay_type}', `dis` = '{$dis}', `p` = '{$p}', `free` = '{$free}'{$SETSTATE} WHERE `id` = {$id}");
						toSyncItem(2, 'events', $id);
					}
					else {
						
						// runSql("INSERT INTO `events` (`rid`, `date`, `start`, `end`, `cid`, `nm`, `note`, `eqs`, `price`) VALUES ({$rid}, '{$date}', '{$start}', '{$end}', '{$cid}', '{$nm}', '{$note}', '{$eqs}', '{$price}')");
						/* ВРЕМЕННО */
						$state = (strtotime("{$date} {$end}:00") < time()) ? 2 : 0;
						runSql("INSERT INTO `events` (`rid`, `date`, `start`, `end`, `cid`, `nm`, `note`, `eqs`, `price_eqs`, `price`, `pay_type`, `dis`, `p`, `free`, `state`) VALUES ({$rid}, '{$date}', '{$start}', '{$end}', '{$cid}', '{$nm}', '{$note}', '{$eqs}', '{$price_eqs}', '{$price}', '{$pay_type}', '{$dis}', {$p}, {$free}, {$state})"); $event_id = insertId();
						$id = $event_id;
						toSyncItem(1, 'events', $event_id);
						/* ВРЕМЕННО */
					}
					
					$msg = ($event_id) ? "Событие добавлено" : "Изменения сохранены";
					$_SESSION['msg'] = $msg; $_SESSION['msg_type'] = 'ok';
					
					// Журнал
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					$date = date('d.m.Y', strtotime($event->date));
					$action_name = ($event_id) ? 'Добавление' : 'Редактирование';
					$log = "{$action_name} события. ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
					
					// Обновление расписания
					repaNotify("https://repa.by/action?ext=update&view=bases&action=boom-update&rid={$rid}");
					
				break;
				
				case 'get':
				
					$id = getInt('id', 'post');
					
					$event = loadObject("SELECT * FROM `events` WHERE `id` = {$id}");
					
					$rid = $event->rid;
					$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
					$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
					$date = $event->date;
					$day = date('w', strtotime($date));
					$dayWeek = getDayWeek($day, true);
					$start = $event->start;
					$end = $event->end;
					$cid = $event->cid;
					$nm = $event->nm;
					$note = $event->note;
					$eqs = $event->eqs;
					$price = $event->price;
					$price_eqs = $event->price_eqs;
					$pay_type = $event->pay_type;
					
					$sid = loadResult("SELECT `sid` FROM `rooms` WHERE `id` = {$rid}");
					
					$this_count = ($start < 23) ? $end - $start : 7;
					
					// if ($event->state != 2) {
					if ($event->state != 999) {
					
						$template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND (`date` = '{$date}' OR `date` = '0000-00-00') AND `day` = {$day} AND `start` = {$start} ORDER BY `date` DESC");
						
						$count = ($start < 23) ? $template->end - $start : 7;
						
						$ends = array();
						$ends[] = (object) array('end' => $template->end, 'count' => $count, 'price' => json_decode($template->price));
						if ($start < 23) {
						
							$this_end = $template->end;
						
							while ($this_end > 0 and $this_end < 24 and $template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND (`date` = '{$date}' OR `date` = '0000-00-00') AND `day` = {$day} AND `start` = {$this_end} ORDER BY `date` DESC")) {
							
								// Проверка на существования события
								if (loadResult("SELECT `id` FROM `events` WHERE `rid` = {$rid} AND `date` = '{$date}' AND `start` = {$template->start}")) break;
								
								$count = $template->end - $start;
								
								$ends[] = (object) array('end' => $template->end, 'count' => $count, 'price' => json_decode($template->price));
								
								$this_end = $template->end;
							}
						}
					}
					else {
						
						$count = ($start < 23) ? $end - $start : 7;
					}
					
					// Клиенты
					$clients = loadObjectList("SELECT * FROM `clients` ORDER BY `name` ASC");
					
					// $disabled = ($event->state == 2 and strtotime("{$date} {$event->end}:{$min}") + 86400 < time() and $event->pay >= $event->price and $event->price > 0) ? 'disabled' : '';
					$disabled = ($event->state == 2 and $event->pay >= $event->price and $event->price > 0) ? 'disabled' : '';
					
					?>
					<div class="data"
					
						data-id="<?php echo $id; ?>"
						data-rid="<?php echo $rid; ?>"
						data-date="<?php echo $date; ?>"
						data-start="<?php echo $start; ?>"
					
					></div>
					
					<div class="title"><?php if ($event->state < 2) { ?>Редактирование события<?php } else { ?>Прошедшее событие<?php } ?>, <?php echo $room->name; ?>
					<?php if ($event->state == 5) { ?><div id="event-skipped">неявка</div><?php } ?> 
					
						<p style="font-size: 12px; line-height: 10px; position: absolute; color: #2493d8;"><?php echo date('d.m.Y в H:i', strtotime($event->created)); ?></p>
					</div>
					<div class="datetime">
						<div class="date"><b><?php echo date('d.m.Y', strtotime($date)); ?></b>, <?php echo $dayWeek; ?></div>
						<div class="time">
						<?php // if ($event->state != 2) { ?>
						<?php if ($event->state != 999) { ?>
							<?php echo $start; ?>:<?php echo $min; ?> -
							<select name="end" onchange="changeEnd();" <?php echo $disabled; ?>>
							<?php foreach($ends as $end) {
								
								$price_0 += $end->price[0];
								$price_1 += $end->price[1];
								$price_2 += $end->price[2];
							?>
								<option value="<?php echo $end->end; ?>" data-h="<?php echo $end->count; ?>" data-price_0="<?php echo $price_0; ?>" data-price_1="<?php echo $price_1; ?>" data-price_2="<?php echo $price_2; ?>" <?php echo ($end->end == $event->end) ? "selected" : ""; ?>><?php echo $end->end; ?>:<?php echo $min; ?></option>
							<?php } ?>
							</select>
						<?php } else { ?>
						<?php echo $start; ?>:<?php echo $min; ?> - <?php echo $end; ?>:<?php echo $min; ?> 
						<?php } ?>
						</div>
						<div class="clear"></div>
					</div>
					<div class="client">
						
						<?php
						
						if ($cid > 0) {
						
							$client = loadObject("SELECT * FROM `clients` WHERE `id` = {$cid}");
							
							$debt = round(loadResult("SELECT SUM(`price` - `pay`) FROM `events` WHERE (`price` - `pay`) > 0 AND `cid` = {$cid} AND `state` > 1 AND `date` >= '2017-07-01'"), 2);
							
							if ($debt > 0) {
								
								$paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` IN (SELECT `id` FROM `events` WHERE `cid` = {$cid})");
								$debt -= $paid;
							}
							
							$contacts = loadObjectList("SELECT * FROM `client_contacts` WHERE `cid` = {$cid}");
							$count_events = clientEventsCount($client->id);
							
							$discount = ($client->card) ? "Скидка ".getClientLevel($count_events, 1)."%" : "Без скидки";
							if ($client->type == 3) { $discount = "Скидка 30%"; }
							if ($client->dis > 0) { $discount = "Скидка {$client->dis}%"; }
							
							$contact_list = array();
							if (cnt($contacts)) {
								
								foreach ($contacts as $contact) {
								
									$string = "<b>{$contact->phone}</b>";
									$string .= ($contact->name) ? " ({$contact->name})" : "";
									
									$contact_list[] = $string;
								}
							}
						}
						
						?>
						<input name="card" type="text" value="<?php echo $client->card; ?>" placeholder="№ карты, имя" onchange="findClient(this.value);" onkeyup="findClient(this.value);" <?php echo $disabled; ?>/>
						<select name="cid" onchange="getClient(this.value);" <?php echo $disabled; ?>><option value="-1">Выбрать клиента</option><option value="0" <?php echo ($event->cid == 0) ? "selected" : ""; ?>>Неопределенный</option>
						<?php foreach($clients as $thisclient) {
							
							// $thisclient->dis = 0;
							if ($thisclient->card) {
								
								$count_events = clientEventsCount($thisclient->id);
								$thisclient->dis = ($thisclient->dis > 0) ? $thisclient->dis : getClientLevel($count_events, 1);
							}
							
							if ($thisclient->type == 3) { $thisclient->card = 9999; $thisclient->dis = 30; }
						?>
							<option value="<?php echo $thisclient->id; ?>" data-card="<?php echo $thisclient->card; ?>"  data-dis="<?php echo $thisclient->dis; ?>" <?php echo ($thisclient->id == $cid) ? "selected" : ""; ?>><?php echo $thisclient->name; ?></option>
						<?php } ?>
						</select>
						<div class="add" style="display: <?php echo ($event->state == 3 and $event->cid == 0) ? "block" : "none"; ?>;">
							<p>Новый клиент</p>
							<select name="client_type">
								<option value="1">Группа</option>
								<option value="2">Музыкант</option>
							</select>
							<input name="client_name" type="text" value="" placeholder="Название / Имя"/>
							<input name="client_phone" type="text" value="" placeholder="Телефон" onchange="findClientPhone(this.value);" onkeyup="findClientPhone(this.value);"/>
							<div class="clear"></div>
						</div>
						<div class="info" style="display: block;">
							<?php if ($client->note) { ?><i><?php echo $client->note; ?></i><?php } ?>
							<?php if (cnt($contact_list)) { ?>
							<p>Контакты <?php if ($debt > 0) { ?><span class="debt">Долг: <b><?php echo $debt; ?></b></span><?php } ?></p>
							<?php echo implode(", ", $contact_list); ?>
							<?php } ?>
						</div>
					</div>
					<div class="findByPhoneList"></div>
					<div class="nm">
						<label><input name="nm[]" type="radio" value="0" <?php echo ($nm == 0) ? "checked" : ""; ?> onclick="setPrice();" <?php echo $disabled; ?>/> Более 2-x</label>
						<label><input name="nm[]" type="radio" value="1" <?php echo ($nm == 1) ? "checked" : ""; ?> onclick="setPrice();" <?php echo $disabled; ?>/>1 музыкант</label>
						<label><input name="nm[]" type="radio" value="2" <?php echo ($nm == 2) ? "checked" : ""; ?> onclick="setPrice();" <?php echo $disabled; ?>/>2 музыканта</label>
					</div>
					<div id="rent">
						<h3>Аренда оборудования</h3>
						<div class="list">
							<?php
							
							// Оборудование
							$equipments = loadObjectList("SELECT * FROM `equipments` WHERE `sid` = {$sid} AND `count` > 0");
							
							if (cnt($equipments)) {
							
								if (!$disabled) {
								// if ($event->state != 2) {
								
									// Свободное оборудование
									$eqs_free = getFreeEqs($equipments, $sid, $date, $start, $event->end);
									
									// Забронированное в этом событии
									$eqs = json_decode($eqs);
									$eqs_this = array();
									foreach ($eqs as $eq) { $eqs_this[$eq->id] = $eq->count; }
									
									foreach ($equipments as $eq) {
						
										if ($eqs_free[$eq->id] < 1 and $eqs_this[$eq->id] < 1) { continue; }
										
									?>
									<div class="item" data-id="<?php echo $eq->id; ?>" data-count="<?php echo $eqs_free[$eq->id]; ?>" data-price="<?php echo $eq->price; ?>" data-rent="<?php echo $eqs_this[$eq->id]; ?>">
										<div class="count"><?php echo $eqs_free[$eq->id]; ?></div>
										<div class="actions">
											<span onclick="toRent(0, <?php echo $eq->id; ?>);">-</span>
											<span onclick="toRent(1, <?php echo $eq->id; ?>);">+</span>
										</div>
										<div class="name"><?php echo $eq->name; ?> <span>(<?php echo $eq->price; ?> / час)</span></div>
										<div class="rent">
										<?php if ($eqs_this[$eq->id]) { ?>
										+ <?php echo $eqs_this[$eq->id] * $eq->price * $this_count; ?> (<?php echo $eqs_this[$eq->id]; ?>)
										<?php } else { ?>-<?php } ?>
										</div>
										<p class="clear"></p>
									</div>
									<?php
									}
								}
								else {
								
									// Забронированное в этом событии
									$eqs = json_decode($eqs);
									$eqs_this = array();
									foreach ($eqs as $eq) { $eqs_this[$eq->id] = $eq->count; }
									
									foreach ($equipments as $eq) {
									?>
									<div class="item" data-id="<?php echo $eq->id; ?>" data-rent="<?php echo $eqs_this[$eq->id] ?: 0; ?>">
										<div class="name"><?php echo $eq->name; ?> <span>(<?php echo $eq->price; ?> / час)</span></div>
										<div class="rent">
										<?php if ($eqs_this[$eq->id]) { ?>
										+ <?php echo $eqs_this[$eq->id] * $eq->price * $this_count; ?> (<?php echo $eqs_this[$eq->id]; ?>)
										<?php } else { ?>-<?php } ?>
										</div>
										<p class="clear"></p>
									</div>
									<?php
									}
								}
							}
							?>
						</div>
						<div class="total"><h3>Стоимость оборудования</h3><span id="price-eqs"><?php echo $price_eqs; ?></span><div class="clear"></div></div>
					</div>
					<div class="price">
						<h3>Стоимость репетиции <p><?php echo $discount; ?></p></h3>
						<?php if ($event->state == 2 and strtotime("{$date} {$event->end}:{$min}") + 86400 < time()) { ?>
						<input name="price" type="text" value="<?php echo $price; ?>" onchange="changePrice(this.value);" disabled />
						<?php } else { ?>
						<input name="price" type="text" value="<?php echo $price; ?>" onchange="changePrice(this.value);" <?php echo ($event->state != 2 and $event->p != 1) ? "disabled" : ""; ?>/>
						<label id="price-edits">Правка админа <input type="checkbox" value="1" <?php echo ($event->p == 1) ? "checked disabled" : ""; ?> onchange="priceEdits();" /></label>
						<?php } ?>
						<div class="clear"></div>
						<select name="pay_type">
							<option value="1"<?php echo ($pay_type == 1) ? " selected" : ""; ?>>Наличные</option>
							<option value="2"<?php echo ($pay_type == 2) ? " selected" : ""; ?>>Безнал</option>
						</select>
						<h3>Общая стоимость: <span id="price-total"><?php echo ($price+$price_eqs); ?></span> руб.</h3>
						<label id="price-free">Бесплатно <input type="checkbox" value="1" <?php echo ($event->free == 1) ? "checked" : ""; ?> onchange="priceFree();" /></label>
						<div class="clear"></div>
					</div>
					<div class="note">
						<textarea name="note" placeholder="Комментарий"><?php echo $note; ?></textarea>
					</div>
					<div class="actions">
						<?php if ($event->state == 3) { ?>
						<p class="apply" onclick="saveEvent(this, 1);">Подтвердить</p>
						<?php } else { ?>
						<p class="save" onclick="saveEvent(this);">Сохранить</p>
						<?php if (isHaveWeekly($event) == false) { ?>
						<?php if ($event->pay == $event->price) { ?><p class="post" onclick="weeklyEvent(this);">Добавить в постоянные</p><?php } ?>
						<?php } ?>
						<?php } ?>
						<?php // if ($event->state < 2 and ($date != date('Y-m-d'))) { ?>
						<p class="delete" onclick="deleteEvent(this);" style="float: left;">Удалить</p>
						<?php // } ?>
						
						<?php if ($event->state == 0 and time() > strtotime($date)) { ?>
						<p class="cancel" onclick="cancelEvent(this, 0);" style="float: left;">Отмена</p>
						<?php } ?>
						<?php if ($event->state == 2 or $event->state == 1) { ?>
						<?php if ($event->pay == 0) { ?>
						<p class="cancel" onclick="cancelEvent(this, 1);" style="float: left;">Неявка</p>
						<p class="cancel" onclick="cancelEvent(this, 0);" style="float: left;">Отмена</p>
						<?php } ?>
						
						<div class="pay">
							<input name="pay" type="text" value="<?php echo $event->pay; ?>" />
							<p class="save" onclick="payEvent(this);">Оплачено</p>
						</div>
						<?php } ?>
						<?php if ($event->state == 0) { ?>
						<div class="pay">
							<input name="pay" type="text" value="<?php echo $event->pay; ?>" />
							<p class="save" onclick="payEvent(this);">Предоплата</p>
						</div>
						<?php } ?>
						<div class="clear"></div>
					</div>
					<!--
					<div class="actions" style="background: #fff; padding: 0 10px 15px 10px;">
						<p class="save" onclick="saveEvent(this);">Сохранить</p>
						<p class="delete" style="float: left;" onclick="deleteEvent(this);">Удалить</p>
						<p class="cancel" onclick="cancelEvent(this, 1);">Не пришли</p>
						<p class="cancel" onclick="cancelEvent(this, 0);">Отменили</p>
						<div class="clear" style="margin: 10px;"></div>
						<?php if ($event->state == 3) { ?>
						<p class="apply" onclick="applyEvent(this);">Подтвердить</p>
						<?php } ?>
						<?php if (isHaveWeekly($event) == false) { ?>
						<p class="post" onclick="weeklyEvent(this);">Добавить в постоянные</p>
						<?php } ?>
						<div class="pay">
							<input name="pay" type="text" value="<?php echo $event->pay; ?>" />
							<p class="save" onclick="payEvent(this);">Оплачено</p>
						</div>
						<div class="clear"></div>
					</div>
					-->
					<?php
				
				break;
				
				case 'apply':
				
					$id = getInt('id', 'post');
					
					runSql("UPDATE `events` SET `state` = 0 WHERE `id` = {$id}");
					toSyncItem(2, 'events', $id);
					
					$_SESSION['msg'] = "Событие подтверждено"; $_SESSION['msg_type'] = 'ok';
					?>
					location.assign('/admin/tablerooms');
					<?php
					
					// Журнал
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					$date = date('d.m.Y', strtotime($event->date));
					$log = "Подтверждение события. ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
					
					// Обновление расписания
					repaNotify("https://repa.by/action?ext=update&view=bases&action=boom-update&rid={$event->rid}");
				
				break;
				
				case 'pay':
				
					$id = getInt('id', 'post');
					$pay = str_replace(',', '.', getVar('pay', 'post'));
					
					runSql("UPDATE `events` SET `pay` = '{$pay}', `pay_date` = NOW() WHERE `id` = {$id}");
					toSyncItem(2, 'events', $id);
					
					echo getMsg("Сумма оплаты сохранена", "ok", "location.assign('/admin/tablerooms');"); 
					
					// Журнал
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					$date = date('d.m.Y', strtotime($event->date));
					$log = "Оплата события. ID:{$id}. Сумма: {$pay}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
				
				break;
				
				case 'note':
				
					$id = getInt('id', 'post');
					$note = getVar('note', 'post');
					
					runSql("UPDATE `events` SET `note` = '{$note}' WHERE `id` = {$id}");
					toSyncItem(2, 'events', $id);
					
					echo getMsg("Комментарий сохранен", "ok", ""); 
					
					// Журнал
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					$date = date('d.m.Y', strtotime($event->date));
					$log = "Сохранение комментария. ID:{$id}. Текст: {$note}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
					
					// Обновление расписания
					repaNotify("https://repa.by/action?ext=update&view=bases&action=boom-update&rid={$event->rid}");
				
				break;
				
				case 'delete':
				
					$id = getInt('id', 'post');
					
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					
					runSql("DELETE FROM `events` WHERE `id` = {$id}");
					toSyncItem(0, 'events', $id);
					
					$_SESSION['msg'] = "Событие удалено"; $_SESSION['msg_type'] = 'ok';
					
					// Журнал
					$date = date('d.m.Y', strtotime($event->date));
					$log = "Удаление события. ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
					
					// Обновление расписания
					repaNotify("https://repa.by/action?ext=update&view=bases&action=boom-update&rid={$event->rid}");
				
				break;
				
				case 'cancel':
				
					$id = getInt('id', 'post');
					$type = getInt('type', 'post');
					
					switch ($type) {
						case 0: $state = 4; break; // Отменили
						case 1: $state = 5; break; // Не пришли
					}
					
					// Уменьшаем стоимость репетиции
					$event_price = loadResult("SELECT `price` FROM `events` WHERE `id` = {$id}");
					$price = round(($event_price / 100 * 25), 2);
					
					runSql("UPDATE `events` SET `state` = '{$state}', `price` = '{$price}', `price_eqs` = 0, `eqs` = '[]' WHERE `id` = {$id}");
					toSyncItem(2, 'events', $id);
					
					// Исключение из постоянки
					$item = loadObject("SELECT * FROM `events` WHERE `id` = {$id}");
					$day = date('w', strtotime($item->date));
					
					$weekly = loadObject("SELECT `id`,`nodates` FROM `event_weekly` WHERE `day` = {$day} AND `start` = {$item->start} AND `end` = {$item->end} AND `cid` = {$item->cid}");
					
					if ($weekly) {
					
						$nodates = explode(",", $weekly->nodates);
						$nodates = array_diff($nodates, array(''));
						array_push($nodates, $item->date);
						$nodates = implode(",", $nodates);
						
						runSql("UPDATE `event_weekly` SET `nodates` = '{$nodates}' WHERE `id` = {$weekly->id}");
						toSyncItem(2, 'event_weekly', $weekly->id);
					}
					
					$_SESSION['msg'] = "Статус события изменен"; $_SESSION['msg_type'] = 'ok';
					
					// Журнал
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					$date = date('d.m.Y', strtotime($event->date));
					$action_name = ($state == 4) ? 'Отмена события' : 'Неявка на событие';
					$log = "{$action_name}. ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
					
					// Обновление расписания
					repaNotify("https://repa.by/action?ext=update&view=bases&action=boom-update&rid={$event->rid}");
				
				break;
				
				case 'weekly':
				
					$id = getInt('id', 'post');
					
					$event = loadObject("SELECT * FROM `events` WHERE `id` = {$id}");
					
					$day = date('w', strtotime($event->date));
					
					if (isHaveWeekly($event) == true) {
				
						echo getMsg("Это время недоступно", "error", ""); 
					}
					else {
						
						$fields = array();
						
						foreach ($event as $name => $value) {
							
							// в event_weekly есть не все колонки events
							if (!in_array($name, array('rid', 'start', 'end', 'cid', 'nm', 'note', 'eqs', 'price_eqs', 'price', 'dis'))) continue;
							
							$fields[$name] = $value;
						}
					
						$sql = "INSERT INTO `event_weekly` (`day`";
						foreach ($fields as $name => $value) { $sql .= ", `{$name}`"; }
						$sql .= ") VALUES ({$day}";
						foreach ($fields as $name => $value) { $sql .= ", '".dbEscape($value)."'"; }
						$sql .= ")";
						
						runSql($sql);
						
						$_SESSION['msg'] = "Событие добавлено в постоянные"; $_SESSION['msg_type'] = 'ok';
						?>location.reload();<?php
					
						// Журнал
						$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
						$day_week = getDayWeek($event->day);
						$log = "Добавление события в постоянные. ID:{$id}. Сумма: {$pay}. {$event->room}. {$day_week}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
						addLog($log);
					}
				
				break;
				
				case 'weekly_get':
				
					$id = getInt('id', 'post');
					$date = getVar('date', 'post');
					
					$event = loadObject("SELECT * FROM `event_weekly` WHERE `id` = {$id}");
					
					$rid = $event->rid;
					$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$rid}");
					$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
					$day = $event->day;
					$dayWeek = getDayWeek($day, true);
					$start = $event->start;
					$end = $event->end;
					$cid = $event->cid;
					$nm = $event->nm;
					$note = $event->note;
					$eqs = $event->eqs;
					$price = $event->price;
					$price_eqs = $event->price_eqs;
					
					$sid = loadResult("SELECT `sid` FROM `rooms` WHERE `id` = {$rid}");
					
					$template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND `date` = '0000-00-00' AND `day` = {$day} AND `start` = {$start} ORDER BY `date` DESC");
					
					$count = ($start < 23) ? $template->end - $start : 7;
					
					$ends = array();
					$ends[] = (object) array('end' => $template->end, 'count' => $count, 'price' => json_decode($template->price));
					if ($start < 23) {
					
						$this_end = $template->end;
					
						while ($this_end > 0 and $this_end < 24 and $template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND `date` = '0000-00-00' AND `day` = {$day} AND `start` = {$this_end} ORDER BY `date` DESC")) {
						
							// Проверка на существования события
							if (loadResult("SELECT `id` FROM `event_weekly` WHERE `rid` = {$rid} AND `day` = '{$day}' AND `start` = {$template->start}")) break;
							
							$count = $template->end - $start;
							
							$ends[] = (object) array('end' => $template->end, 'count' => $count, 'price' => json_decode($template->price));
							
							$this_end = $template->end;
						}
					}
					
					// Клиенты
					$clients = loadObjectList("SELECT * FROM `clients` ORDER BY `name` ASC");
					
					?>
					<div class="data"
					
						data-id="<?php echo $id; ?>"
						data-rid="<?php echo $rid; ?>"
						data-day="<?php echo $day; ?>"
						data-start="<?php echo $start; ?>"
					
					></div>
					
					<div class="title">Постоянное событие, <?php echo $room->name; ?></div>
					<div class="datetime">
						<div class="date"><b><?php echo date('d.m.Y', strtotime($date)); ?></b>, <?php echo $dayWeek; ?></div>
						<div class="time">
						<?php echo $start; ?>:<?php echo $min; ?> - <?php echo $end; ?>:<?php echo $min; ?>
						</div>
						<div class="clear"></div>
					</div>
					<div class="client">
						
						<?php
						
						$client = loadObject("SELECT * FROM `clients` WHERE `id` = {$cid}");
						
						$debt = round(loadResult("SELECT SUM(`price` - `pay`) FROM `events` WHERE (`price` - `pay`) > 0 AND `cid` = {$cid} AND `state` > 1 AND `date` >= '2017-07-01'"), 2);
						
						if ($debt > 0) {
							
							$paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` IN (SELECT `id` FROM `events` WHERE `cid` = {$cid})");
							$debt -= $paid;
						}
						
						$contacts = loadObjectList("SELECT * FROM `client_contacts` WHERE `cid` = {$cid}");
						$count_events = clientEventsCount($client->id);
						
						$discount = ($client->card) ? "Скидка ".getClientLevel($count_events, 1)."%" : "Без скидки";
						if ($client->type == 3) { $discount = "Скидка 30%"; } 
						if ($client->dis > 0) { $discount = "Скидка {$client->dis}%"; }
						
						$contact_list = array();
						if (cnt($contacts)) {
							
							foreach ($contacts as $contact) {
							
								$string = "<b>{$contact->phone}</b>";
								$string .= ($contact->name) ? " ({$contact->name})" : "";
								
								$contact_list[] = $string;
							}
						}
					
						?>
						<input name="card" type="text" value="<?php echo $client->card; ?>" placeholder="№ карты, имя"  disabled />
						<select name="cid" disabled>
							<option value="<?php echo $client->id; ?>"><?php echo $client->name; ?></option>
						</select>
						<div class="info" style="display: block;">
							<?php if ($client->note) { ?><i><?php echo $client->note; ?></i><?php } ?>
							<?php if (cnt($contact_list)) { ?>
							<p>Контакты <?php if ($debt > 0) { ?><span class="debt">Долг: <b><?php echo $debt; ?></b></span><?php } ?></p>
							<?php echo implode(", ", $contact_list); ?>
							<?php } ?>
						</div>
					</div>
					<div class="nm">
						<label><input name="nm[]" type="radio" value="0" <?php echo ($nm == 0) ? "checked" : ""; ?> disabled /> Более 2-x</label>
						<label><input name="nm[]" type="radio" value="1" <?php echo ($nm == 1) ? "checked" : ""; ?> disabled />1 музыкант</label>
						<label><input name="nm[]" type="radio" value="2" <?php echo ($nm == 2) ? "checked" : ""; ?> disabled />2 музыканта</label>
					</div>
					<div id="rent">
						<h3>Аренда оборудования</h3>
						<div class="list">
							<?php
							
							// Оборудование
							$equipments = loadObjectList("SELECT * FROM `equipments` WHERE `sid` = {$sid} AND `count` > 0");
							
							if (cnt($equipments)) {
								
								// Свободное оборудование
								$eqs_free = getFreeEqs($equipments, $sid, $day, $start, $event->end, true);
								
								// Забронированное в этом событии
								$eqs = json_decode($eqs);
								$eqs_this = array();
								foreach ($eqs as $eq) { $eqs_this[$eq->id] = $eq->count; }
								
								foreach ($equipments as $eq) {
					
									if ($eqs_free[$eq->id] < 1 and $eqs_this[$eq->id] < 1) { continue; }
								?>
								<div class="item" data-id="<?php echo $eq->id; ?>" data-count="<?php echo $eqs_free[$eq->id]; ?>" data-price="<?php echo $eq->price; ?>" data-rent="<?php echo $eqs_this[$eq->id]; ?>">
									<div class="name"><?php echo $eq->name; ?> <span>(<?php echo $eq->price; ?> / час)</span></div>
									<div class="rent">
									<?php if ($eqs_this[$eq->id]) { ?>
									+ <?php echo $eqs_this[$eq->id] * $eq->price * $count; ?> (<?php echo $eqs_this[$eq->id]; ?>)
									<?php } else { ?>-<?php } ?>
									</div>
									<p class="clear"></p>
								</div>
								<?php
								}
							}
							?>
						</div>
						<div class="total"><h3>Стоимость оборудования</h3><span id="price-eqs"><?php echo $price_eqs; ?></span><div class="clear"></div></div>
					</div>
					<div class="price">
						<h3>Стоимость репетиции <p><?php echo $discount; ?></p></h3>
						<input name="price" type="text" value="<?php echo $price; ?>" disabled />
						<div class="clear"></div>
						<h3>Общая стоимость: <span id="price-total"><?php echo ($price+$price_eqs); ?></span> руб.</h3>
						<div class="clear"></div>
					</div>
					<div class="note">
						<textarea name="note" placeholder="Комментарий"><?php echo $note; ?></textarea>
					</div>
					<div class="actions">
						<p class="save" onclick="saveWeeklyEvent(<?php echo $event->id; ?>, '<?php echo $date; ?>', this);">Сохранить</p>
						<p class="nodate" onclick="nodateWeekly(<?php echo $event->id; ?>, '<?php echo $date; ?>');">Исключить из этой даты</p>
						<div class="clear"></div>
					</div>
					<?php
				
				break;
				
				case 'weekly_save':
				
					$id = getInt('id', 'post');
					$date = getVar('date', 'post');
					$note = getVar('note', 'post');
					
					$event = loadObject("SELECT * FROM `event_weekly` WHERE `id` = {$id}");
					
					$fields = array();
					foreach ($event as $name => $value) {
						
						if ($name == 'id' or $name == 'day' or $name == 'note' or $name == 'nodates' or $name == 'state') continue;
						
						$fields[$name] = $value;
					}
				
					$sql = "INSERT INTO `events` (`date`, `note`";
					foreach ($fields as $name => $value) { $sql .= ", `{$name}`"; }
					$sql .= ") VALUES ('{$date}', '{$note}'";
					foreach ($fields as $name => $value) { $sql .= ", '".dbEscape($value)."'"; }
					$sql .= ")";
					
					runSql($sql); $event_id = insertId();
					toSyncItem(1, 'events', $event_id);
					
					$_SESSION['msg'] = "Событие сохранено"; $_SESSION['msg_type'] = 'ok';
					?>location.reload();<?php
					
					// Журнал
					$id = $event_id;
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					$date = date('d.m.Y', strtotime($event->date));
					$log = "Добавление события из постоянки. ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
				
				break;
				
				case 'weekly_nodate':
				
					$id = getInt('id', 'post');
					$date = getVar('date', 'post');
					
					$nodates = loadResult("SELECT `nodates` FROM `event_weekly` WHERE `id` = {$id}");
					
					$nodates = explode(",", $nodates);
					$nodates = array_diff($nodates, array(''));
					array_push($nodates, $date);
					$nodates = implode(",", $nodates);
					
					runSql("UPDATE `event_weekly` SET `nodates` = '{$nodates}' WHERE `id` = {$id}");
					toSyncItem(2, 'event_weekly', $id);
					
					$_SESSION['msg'] = "Это событие исключено"; $_SESSION['msg_type'] = 'ok';
					?>location.reload();<?php
					
					// Журнал
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `event_weekly` AS a WHERE a.`id` = {$id}");
					$day_week = getDayWeek($event->day);
					$log = "Исключение постоянного события. ID:{$id}. {$event->room}. {$day_week}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
				
				break;
			}
		
		break;
		
		case 'eqs':
			
			switch ($option) {
	
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
		
		case 'client':
			
			switch ($option) {
	
				case 'get':
				
					$id = getInt('id', 'post');
					
					$client = loadObject("SELECT * FROM `clients` WHERE `id` = {$id}");
					
					$debt = round(loadResult("SELECT SUM(`price` - `pay`) FROM `events` WHERE (`price` - `pay`) > 0 AND `cid` = {$id} AND `date` >= '2017-07-01'"), 2);
					
					if ($debt > 0) {
						
						$paid = loadResult("SELECT SUM(`sum`) FROM `incomes` WHERE `eid` IN (SELECT `id` FROM `events` WHERE `cid` = {$id})");
						$debt -= $paid;
					}
					
					$contacts = loadObjectList("SELECT * FROM `client_contacts` WHERE `cid` = {$id}");
					
					$contact_list = array();
					if (cnt($contacts)) {
						
						foreach ($contacts as $contact) {
						
							$string = "<b>{$contact->phone}</b>";
							$string .= ($contact->name) ? " ({$contact->name})" : "";
							
							$contact_list[] = $string;
						}
					}
				
					?>
					<?php if ($client->note) { ?><i><?php echo $client->note; ?></i><?php } ?>
					<?php if (cnt($contact_list)) { ?>
					<p>Контакты <?php if ($debt > 0) { ?><span class="debt">Долг: <b><?php echo $debt; ?></b></span><?php } ?></p>
					<?php echo implode(", ", $contact_list); ?>
					<?php } ?>
					<?php
				
				break;
	
				case 'findbyphone':
				
					$number = getVar('number', 'post');
					
					$number = preg_replace('~[^0-9]+~','',$number);
					
					if ($number) {
					
						$items = loadObjectList("SELECT `cid`, (SELECT `name` FROM `clients` WHERE `id` = `cid`) AS `client` FROM `client_contacts` WHERE REPLACE(REPLACE(REPLACE(`phone`, '+', ''), ' ', ''), '-', '') = '{$number}'");
						
						if (cnt($items)) {
						?>
						<p>Найденные клиенты:</p>
						<?php foreach ($items as $item) { ?>
						<div class="item" onclick="getClient(<?php echo $item->cid; ?>); $('#event select[name=cid]').val(<?php echo $item->cid; ?>); $(this).parent().hide();"><?php echo $item->client; ?></div>
						<?php } ?>
						<?php
						}
					}
				
				break;
			}
			
		break;
		
		case 'requests';
			
			switch ($option) {
	
				case 'check':
				
					$count = loadResult("SELECT COUNT(*) FROM `events` WHERE `state` = 3");
					
					if ($count) {
					?>
					$('#requests > p span').text('<?php echo $count; ?>').show();
					$('#requests').data('count', <?php echo $count; ?>);
					<?php
					} else {
					?>
					$('#requests > p span').text('0').hide();
					$('#requests').data('count', 0);
					<?php
					}
					
				
				break;
				
				case 'get':
				
					$events = loadObjectList("SELECT * FROM `events` WHERE `state` = 3");
					
					if (cnt($events)) {
					foreach ($events as $event) {
						
						$date = date('d.m.Y', strtotime($event->date));
						
						$room = loadObject("SELECT * FROM `rooms` WHERE `id` = {$event->rid}");
						$min = str_pad($room->min, 2, '0', STR_PAD_LEFT);
						$studio = loadObject("SELECT * FROM `studios` WHERE `id` = {$room->sid}");
					
						if ($event->cid) {
						
							$client = loadObject("SELECT * FROM `clients` WHERE `id` = {$event->cid}");
					
							$contacts = loadObjectList("SELECT * FROM `client_contacts` WHERE `cid` = {$event->cid}");
							
							$contact_list = array();
							if (cnt($contacts)) {
								
								foreach ($contacts as $contact) {
								
									$string = "{$contact->phone}";
									$string .= ($contact->name) ? " ({$contact->name})" : "";
									
									$contact_list[] = $string;
								}
							}
							
							$client_info = "<b>{$client->name}</b><br/>".implode(", ", $contact_list);
						}
						else {
							
							$client_info = $event->note;
						}
						
					?>
					<div class="item" data-id="<?php echo $event->id; ?>">
			
						<div class="datetime">
							<a href="<?php echo admin; ?>/tablerooms?date=<?php echo $date; ?>&eid=<?php echo $event->id; ?>"><div class="date"><?php echo $date; ?></div><div class="time"><?php echo $event->start; ?>:<?php echo $min; ?> - <?php echo $event->end; ?>:<?php echo $min; ?></div></a>
							<div class="clear"></div>
						</div>
						<div class="place"><b><?php echo $studio->name; ?></b>, <?php echo $room->name; ?></div>
						<div class="from"><?php echo $client_info; ?></div>
						<div class="actions">
							<p class="apply" onclick="<?php if ($event->cid > 0) { ?>applyRequest(<?php echo $event->id; ?>);<?php } else { ?>location.assign('<?php echo admin; ?>/tablerooms?date=<?php echo $date; ?>&eid=<?php echo $event->id; ?>')<?php } ?>">Подтвердить</p>
							<p class="cancel" onclick="cancelRequest(<?php echo $event->id; ?>);">Отклонить</p>
							<div class="clear"></div>
						</div>
						
					</div>
					<?php
					}
					}
				
				break;
				
			}
		
		break;
		
		case 'request';
			
			switch ($option) {
	
				case 'apply':
				
					$id = getInt('id', 'post');
					
					runSql("UPDATE `events` SET `state` = 0 WHERE `id` = {$id}");
					toSyncItem(2, 'events', $id);
					
					echo getMsg("Заявка подтверждена", "ok", ""); 
					?>
					$('#requests .item[data-id="<?php echo $id; ?>"]').remove();
					if ($('#requests .item').size() == 0) { hideRequests(); }
					checkRequests();
					<?php
					
					// Журнал
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					$date = date('d.m.Y', strtotime($event->date));
					$log = "Подтверждение события. ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
					
					// Обновление расписания
					repaNotify("https://repa.by/action?ext=update&view=bases&action=boom-update&rid={$event->rid}");
				
				break;
	
				case 'cancel':
				
					$id = getInt('id', 'post');
					
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `events` AS a WHERE a.`id` = {$id}");
					
					runSql("DELETE FROM `events` WHERE `id` = {$id}");
					toSyncItem(0, 'events', $id);
					
					echo getMsg("Заявка отклонена", "error", ""); 
					?>
					$('#requests .item[data-id="<?php echo $id; ?>"]').remove();
					if ($('#requests .item').size() == 0) { hideRequests(); }
					checkRequests();
					<?php
					
					// Журнал
					$date = date('d.m.Y', strtotime($event->date));
					$log = "Отклонение заявки (удаление события). ID:{$id}. {$event->room}. {$date}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
					
					// Обновление расписания
					repaNotify("https://repa.by/action?ext=update&view=bases&action=boom-update&rid={$event->rid}");
				
				break;
				
			}
		
		break;
		
		case 'sync':
			
			switch ($option) {
	
				case 'check':
					
					if (admin == '') {
						
						$count_tasks = (int) json_decode(file_get_contents("http://boomroom.by/api.php?task=getCountTasks"));
						
						if ($count_tasks > 0) {
							
						?>
						if ($('#sync').is(':hidden')) { $('#sync').show(); }
						$.ajax({ url: '/sync.php', type: 'post', data: {}, dataType: 'script' });
						$('#sync').data('count', '<?php echo $count_tasks; ?>');
						<?php
						}
						else {
						?>
						$('#sync').hide();
						<?php
						}
					}
					
				break;
			}
			
		break;
	}
	
	
?>