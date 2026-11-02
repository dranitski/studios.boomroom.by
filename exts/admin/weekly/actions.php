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
					
					$template = loadObject("SELECT * FROM `event_templates` WHERE `id` = {$tid}");
					
					$rid = $template->rid;
					$day = $template->day;
					$dayWeek = getDayWeek($day, true);
					$start = $template->start;
					$end = $template->end;
					
					$price = json_decode($template->price);
					
					$count = ($start < 23) ? $end - $start : 7;
					
					$ends = array();
					
					$ends[] = (object) array('end' => $template->end, 'count' => $count, 'price' => $price);
					
					if ($start < 23) {
					
						while ($template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND `date` = '0000-00-00' AND `day` = {$day} AND `start` = {$end} ORDER BY `date` DESC")) {
						
							// Проверка на существования события
							if (loadResult("SELECT `id` FROM `event_weekly` WHERE `rid` = {$rid} AND `day` = {$day} AND `start` = {$template->start}")) break;
							
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
						data-day="<?php echo $day; ?>"
						data-start="<?php echo $start; ?>"
					
					></div>
					
					<div class="title">Создание постоянного события</div>
					<div class="datetime">
						<div class="date"><b><?php echo $dayWeek; ?></b></div>
						<div class="time">
							<?php echo $start; ?>:00 -
							<select name="end" onchange="changeEnd();">
							<?php foreach($ends as $end) {
								
								$price_0 += $end->price[0];
								$price_1 += $end->price[1];
								$price_2 += $end->price[2];
							?>
								<option value="<?php echo $end->end; ?>" data-h="<?php echo $end->count; ?>" data-price_0="<?php echo $price_0; ?>" data-price_1="<?php echo $price_1; ?>" data-price_2="<?php echo $price_2; ?>"><?php echo $end->end; ?>:00</option>
							<?php } ?>
							</select>
						</div>
						<div class="clear"></div>
					</div>
					<div class="client">
						<input name="card" type="text" value="" placeholder="№ карты" onchange="findClient(this.value);" onkeyup="findClient(this.value);"/>
						<select name="cid" onchange="getClient(this.value);"><option value="0">Выбрать клиента</option>
						<?php foreach($clients as $client) {
							
							$client->dis = 0;
							if ($client->card) {
								
								$count_events = loadResult("SELECT COUNT(*) FROM `events` WHERE `cid` = {$client->id} AND `state` = 2");
								$client->dis = getClientLevel($count_events, 1);
							}
							
							if ($client->type == 3) { $client->card = 9999; $client->dis = 30; }
						?>
							<option value="<?php echo $client->id; ?>" data-card="<?php echo $client->card; ?>" data-dis="<?php echo $client->dis; ?>"><?php echo $client->name; ?></option>
						<?php } ?>
						</select>
						<div class="info">
						</div>
					</div>
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
						<h3>Стоимость репетиции<p>Без скидки</p></h3>
						<input name="price" type="text" value=""/>
						<div class="clear"></div>
						<h3>Общая стоимость: <span id="price-total">0</span> руб.</h3>
						<div class="clear"></div>
					</div>
					<div class="note">
						<textarea name="note" placeholder="Комментарий"></textarea>
					</div>
					<div class="actions">
						<p class="save" onclick="saveWeekly(this);">Сохранить</p>
						<div class="clear"></div>
					</div>
					<?php
				
				break;
				
				case 'save':
					
					$id = getInt('id', 'post');
					$rid = getInt('rid', 'post');
					$day = getInt('day', 'post');
					$start = getInt('start', 'post');
					$end = getInt('end', 'post');
					$cid = getInt('cid', 'post');
					$nm = getInt('nm', 'post');
					$note = getVar('note', 'post');
					$eqs = getEqs();
					$price = getVar('price', 'post');
					$price_eqs = getVar('price_eqs', 'post');
					$dis = getVar('dis', 'post');
					
					if ($id) {
						
						runSql("UPDATE `event_weekly` SET `end` = {$end}, `cid` = {$cid}, `nm` = {$nm}, `note` = '{$note}', `eqs` = '{$eqs}', `price_eqs` = '{$price_eqs}', `price` = '{$price}', `dis` = '{$dis}' WHERE `id` = {$id}");
						toSyncItem(2, 'event_weekly', $id);
					}
					else {
						
						runSql("INSERT INTO `event_weekly` (`rid`, `day`, `start`, `end`, `cid`, `nm`, `note`, `eqs`, `price_eqs`, `price`, `dis`) VALUES ({$rid}, '{$day}', '{$start}', '{$end}', '{$cid}', '{$nm}', '{$note}', '{$eqs}', '{$price_eqs}', '{$price}', '{$dis}')");
						$event_id = insertId();
						$id = $event_id;
						toSyncItem(1, 'event_weekly', $id);
					}
					
					$msg = ($event_id) ? "Событие добавлено" : "Изменения сохранены";
					$_SESSION['msg'] = $msg; $_SESSION['msg_type'] = 'ok';
					
					// Журнал
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `event_weekly` AS a WHERE a.`id` = {$id}");
					$day_week = getDayWeek($event->day);
					$action_name = ($event_id) ? 'Добавление постоянного' : 'Редактирование постоянного';
					$log = "{$action_name} события. ID:{$id}. {$event->room}. {$day_week}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
					
				break;
				
				case 'get':
				
					$id = getInt('id', 'post');
					
					$event = loadObject("SELECT * FROM `event_weekly` WHERE `id` = {$id}");
					
					$rid = $event->rid;
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
					
					$this_count = ($start < 23) ? $end - $start : 7;
					
					$template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND `date` = '0000-00-00' AND `day` = {$day} AND `start` = {$start} ORDER BY `date` DESC");
					
					$count = ($start < 23) ? $template->end - $start : 7;
					
					$ends = array();
					$ends[] = (object) array('end' => $template->end, 'count' => $count, 'price' => json_decode($template->price));
					if ($start < 23) {
					
						$this_end = $template->end;
					
						while ($template = loadObject("SELECT * FROM `event_templates` WHERE `rid` = {$rid} AND `date` = '0000-00-00' AND `day` = {$day} AND `start` = {$this_end} ORDER BY `date` DESC")) {
						
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
					
					<div class="title">Редактирование постоянного события</div>
					<div class="datetime">
						<div class="date"><b><?php echo $dayWeek; ?></b></div>
						<div class="time">
						<?php echo $start; ?>:00 -
						<select name="end" onchange="changeEnd();">
						<?php foreach($ends as $end) {
							
							$price_0 += $end->price[0];
							$price_1 += $end->price[1];
							$price_2 += $end->price[2];
						?>
							<option value="<?php echo $end->end; ?>" data-h="<?php echo $end->count; ?>" data-price_0="<?php echo $price_0; ?>" data-price_1="<?php echo $price_1; ?>" data-price_2="<?php echo $price_2; ?>" <?php echo ($end->end == $event->end) ? "selected" : ""; ?>><?php echo $end->end; ?>:00</option>
						<?php } ?>
						</select>
						</div>
						<div class="clear"></div>
					</div>
					<div class="client">
						
						<?php
						
						$client = loadObject("SELECT * FROM `clients` WHERE `id` = {$cid}");
						$contacts = loadObjectList("SELECT * FROM `client_contacts` WHERE `cid` = {$cid}");
						$count_events = loadResult("SELECT COUNT(*) FROM `events` WHERE `cid` = {$client->id} AND `state` = 2");
						
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
						<input name="card" type="text" value="<?php echo $client->card; ?>" placeholder="№ карты" onchange="findClient(this.value);" onkeyup="findClient(this.value);"/>
						<select name="cid" onchange="getClient(this.value);"><option value="0">Выбрать клиента</option>
						<?php foreach($clients as $thisclient) {
							
							// $thisclient->dis = 0;
							if ($thisclient->card) {
								
								$count_events = loadResult("SELECT COUNT(*) FROM `events` WHERE `cid` = {$thisclient->id} AND `state` = 2");
								$thisclient->dis = ($thisclient->dis > 0) ? $thisclient->dis : getClientLevel($count_events, 1);
							}
							
							if ($thisclient->type == 3) { $thisclient->card = 9999; $thisclient->dis = 30; }
						?>
							<option value="<?php echo $thisclient->id; ?>" data-card="<?php echo $thisclient->card; ?>"  data-dis="<?php echo $thisclient->dis; ?>" <?php echo ($thisclient->id == $cid) ? "selected" : ""; ?>><?php echo $thisclient->name; ?></option>
						<?php } ?>
						</select>
						<div class="info" style="display: block;">
							<?php if ($client->note) { ?><i><?php echo $client->note; ?></i><?php } ?>
							<?php if (cnt($contact_list)) { ?>
							<p>Контакты</p>
							<?php echo implode(", ", $contact_list); ?>
							<?php } ?>
						</div>
					</div>
					<div class="nm">
						<label><input name="nm[]" type="radio" value="0" <?php echo ($nm == 0) ? "checked" : ""; ?> onclick="setPrice();"/> Более 2-x</label>
						<label><input name="nm[]" type="radio" value="1" <?php echo ($nm == 1) ? "checked" : ""; ?> onclick="setPrice();"/>1 музыкант</label>
						<label><input name="nm[]" type="radio" value="2" <?php echo ($nm == 2) ? "checked" : ""; ?> onclick="setPrice();"/>2 музыканта</label>
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
							?>
						</div>
						<div class="total"><h3>Стоимость оборудования</h3><span id="price-eqs"><?php echo $price_eqs; ?></span><div class="clear"></div></div>
					</div>
					<div class="price">
						<h3>Стоимость репетиции <p><?php echo $discount; ?></p></h3>
						<input name="price" type="text" value="<?php echo $price; ?>"/>
						<div class="clear"></div>
						<h3>Общая стоимость: <span id="price-total"><?php echo ($price+$price_eqs); ?></span> руб.</h3>
						<div class="clear"></div>
					</div>
					<div class="note">
						<textarea name="note" placeholder="Комментарий"><?php echo $note; ?></textarea>
					</div>
					<div class="actions">
						<p class="save" onclick="saveWeekly(this);">Сохранить</p>
						<p class="delete" onclick="deleteWeekly(this);">Удалить</p>
						<div class="clear"></div>
					</div>
					<?php
				
				break;
				
				case 'delete':
				
					$id = getInt('id', 'post');
					
					$event = loadObject("SELECT a.*, (SELECT `name` FROM `clients` WHERE `id` = a.`cid`) AS `client`, (SELECT `name` FROM `rooms` WHERE `id` = a.`rid`) AS `room` FROM `event_weekly` AS a WHERE a.`id` = {$id}");
					
					runSql("DELETE FROM `event_weekly` WHERE `id` = {$id}");
					toSyncItem(0, 'event_weekly', $id);
					
					$_SESSION['msg'] = "Постоянное событие удалено"; $_SESSION['msg_type'] = 'ok';
					
					// Журнал
					$day_week = getDayWeek($event->day);
					$log = "Удаление постоянного события. ID:{$id}. {$event->room}. {$day_week}, {$event->start}:00 - {$event->end}:00. Клиент: {$event->client} [id:{$event->cid}]";
					addLog($log);
				
				break;
			}
		
		break;
		
		case 'eqs':
			
			switch ($option) {
	
				case 'get':
				
					$rid = getInt('rid', 'post');
					$day = getInt('day', 'post');
					$start = getInt('start', 'post');
					$end = getInt('end', 'post');
					
					$sid = loadResult("SELECT `sid` FROM `rooms` WHERE `id` = {$rid}");
					
					// Оборудование
					$equipments = loadObjectList("SELECT * FROM `equipments` WHERE `sid` = {$sid} AND `count` > 0");
					
					if (cnt($equipments)) {
					
						$eqs_free = getFreeEqs($equipments, $sid, $day, $start, $end, true);
					
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