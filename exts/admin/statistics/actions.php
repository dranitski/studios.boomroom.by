<?php
	
	defined('access') or die ('no access');
	
	$subtask = getVar('subtask', 'post');
	$option = getVar('option', 'post');
	
	// Подключение необходимых функций
	include_once path."/functions/date.php";
	include_once path."/functions/statistics.php";
	
	switch ($subtask) {
	
		case 'chart':
		
			$id		= getInt('id', 'post');
			$start	= getVar('start', 'post');	$date_start = date('d.m.Y', strtotime($start));
			$end	= getVar('end', 'post');	$date_end = date('d.m.Y', strtotime($end));
			$group	= getInt('group', 'post');
			$ord	= getInt('ord', 'post');
			$sort	= getInt('sort', 'post');
			
			$sql_date_start = date('Y-m-d', strtotime($date_start));
			$sql_date_end = date('Y-m-d', strtotime($date_end));
		
			switch ($option) {
	
				case 'all':
					
					// Все счета
					$events = loadObjectList("SELECT * FROM `events` WHERE `date` BETWEEN '{$sql_date_start}' AND '{$sql_date_end}'");
					
					$events_state_0 = 0;
					$events_state_1 = 0;
					$events_state_2 = 0;
					$events_state_3 = 0;
					
					// Количества по статусам
					foreach ($events as $event) {
						
						$date = date('d.m.Y', strtotime($event->date));
						$events_dates[] = $date;
						
						switch ($event->state) {
							
							case 0: $events_state_0++; break;
							case 1: $events_state_1++; break;
							case 2: $events_state_2++; break;
							case 3: $events_state_3++; break;
						}
					}
					
					$events_dates = array_count_values($events_dates);
					
					$labels	= getLabelsCountsList($events_dates, $date_start, $date_end, $group, 1);
					$counts	= getLabelsCountsList($events_dates, $date_start, $date_end, $group, 2);
					
					?>
					<div class="chart" style="height: 200px;"></div>
					
					<h3>Доли статусов</h3>
					<div class="parts">
						<div class="pie" style="width: 50%;"></div>
						<div class="info" style="width: 50%;">
							
							<div class="part">
								<div class="label pl-a"></div>
								<div class="name">Будущие</div>
								<div class="value"><?php echo $events_state_0; ?></div>
								<div class="pc"><?php echo round($events_state_0 / max(1, array_sum((array) $counts)) * 100); ?>%</div>
								<div class="clear"></div>
							</div>
							
							<div class="part">
								<div class="label pl-d"></div>
								<div class="name">Идет сейчас</div>
								<div class="value"><?php echo $events_state_1; ?></div>
								<div class="pc"><?php echo round($events_state_1 / max(1, array_sum((array) $counts)) * 100); ?>%</div>
								<div class="clear"></div>
							</div>
							
							<div class="part">
								<div class="label pl-f"></div>
								<div class="name">Прошедшие</div>
								<div class="value"><?php echo $events_state_2; ?></div>
								<div class="pc"><?php echo round($events_state_2 / max(1, array_sum((array) $counts)) * 100); ?>%</div>
								<div class="clear"></div>
							</div>
							
							<div class="part">
								<div class="label pl-c"></div>
								<div class="name">Неподтвержденные</div>
								<div class="value"><?php echo $events_state_3; ?></div>
								<div class="pc"><?php echo round($events_state_3 / max(1, array_sum((array) $counts)) * 100); ?>%</div>
								<div class="clear"></div>
							</div>
							
							<div class="part">
								<div class="label"></div>
								<div class="name">Всего</div>
								<div class="value"><?php echo cnt($events); ?></div>
								<div class="pc">100%</div>
								<div class="clear"></div>
							</div>
							
						</div>
					</div>
					
					<?php
	
						// Сортировка списков
						$labels_list = $labels;
						$counts_list = $counts;
						
						switch ($ord) {
							
							case 1:
							
								if ($sort == 2) { krsort($labels_list); }
								
								$list = array(); foreach ($labels_list as $n => $label) { $list[] = (object) array('label' => $label, 'count' => $counts_list[$n]); }
						
							break;
							
							case 2:
								
								$plist = array(); foreach ($labels_list as $n => $label) { $plist[$label] = $counts_list[$n]; }
							
								if ($sort == 1) { asort($plist); } else { arsort($plist); }
								
								$list = array(); foreach ($plist as $label => $count) { $list[] = (object) array('label' => $label, 'count' => $count); }
						
							break;
						}
						
					?>
					
					<div class="list">
					<?php if (cnt($list)) { ?>
						<div class="filter">
							<div class="w50">№</div>
							<div class="w200"><a href="javascript:void(0);" onclick="setOrd(<?php echo $id; ?>, 1);">Период</a></div>
							<div><a href="javascript:void(0);" onclick="setOrd(<?php echo $id; ?>, 2);">Количество</a></div>
							<p class="clear"></p>
						</div>
					<?php foreach ($list as $n => $item) { ?>
						<div class="item">
							<div class="num"><?php echo $n+1; ?></div>
							<div class="period"><?php echo $item->label; ?></div>
							<div class="count"><?php echo $item->count; ?></div>
							<div class="clear"></div>
						</div>
					<?php } ?>	
						<div class="result">
							<div class="label">Всего</div>
							<div class="date">с <?php echo $date_start; ?> по <?php echo $date_end; ?></div>
							<div class="sum"><?php echo array_sum((array) $counts);?></div>
							<div class="clear"></div>
						</div>
					<?php } ?>	
					</div>
					
					<script type="text/javascript">
						new Chartist.Line('#ch<?php echo $id; ?> .chart', {
							labels: ['<?php echo implode("', '", $labels); ?>'],
							series: [
								[{
									meta: 'Событий',
									value: <?php echo implode("},{meta: 'Событий', value: ", $counts); ?>
								}]
							   // [<?php echo implode(", ", $counts); ?>]
							]
						}, {
							// high: 10,
							low: 0,
							showArea: true,
							height: 200,
							fullWidth: false,
							plugins: [
							    Chartist.plugins.tooltip()
							]
						});
						
						// Доли
						// var data = {
							// series: [value1, value2 ...]
						// };
						var data = { series: [
							{
								value: <?php echo $events_state_0; ?>,
								name: 'Будущие',
								className: 'ct-series-a'
							}, {
								value: <?php echo $events_state_1; ?>,
								name: 'Идет сейчас',
								className: 'ct-series-d'
							}, {
								value: <?php echo $events_state_2; ?>,
								name: 'Прошедшие',
								className: 'ct-series-f'
							}, {
								value: <?php echo $events_state_3; ?>,
								name: 'Неподтвержденные',
								className: 'ct-series-c'
							}
						]};
						
						var sum = function(a, b) { return a + b };

						new Chartist.Pie('#ch<?php echo $id; ?> .pie', data, {
							labelInterpolationFnc: function(value) {
								return Math.round(value / <?php echo cnt($events); ?> * 100) + '%';
								// return Math.round(value / data.series.reduce(sum) * 100) + '%';
								// return value;
							}
						});
					</script>
					<?php
					
				break;
				
				case 'studios':
				
					$sid = getInt('sid', 'post');
				
					// Все счета
					$events = loadObjectList("SELECT * FROM `events` WHERE `date` BETWEEN '{$sql_date_start}' AND '{$sql_date_end}'");
					
					// Список менеджеров
					$studios = loadObjectList("SELECT * FROM `studios`");
					
					foreach ($studios as $studio) {
					
						$events_dates[$studio->id] = array();
						
						$events_state_0[$studio->id] = 0;
						$events_state_1[$studio->id] = 0;
						$events_state_2[$studio->id] = 0;
						$events_state_3[$studio->id] = 0;
						
						$rooms = loadObjectList("SELECT * FROM `rooms` WHERE `sid` = {$studio->id}");
						$rids = array();
						foreach ($rooms as $room) { $rids[] = $room->id; }
						
						// Количества по статусам
						foreach ($events as $event) {
							
							if (in_array($event->rid, $rids)) {
								
								$date = date('d.m.Y', strtotime($event->date));
								$events_dates[$studio->id][] = $date;
								
								switch ($event->state) {
									
									case 0: $events_state_0[$studio->id]++; break;
									case 1: $events_state_1[$studio->id]++; break;
									case 2: $events_state_2[$studio->id]++; break;
									case 3: $events_state_3[$studio->id]++; break;
								}
							}
						}
						
						$events_dates[$studio->id] = array_count_values($events_dates[$studio->id]);
						
						$labels[$studio->id]	= getLabelsCountsList($events_dates[$studio->id], $date_start, $date_end, $group, 1);
						$counts[$studio->id]	= getLabelsCountsList($events_dates[$studio->id], $date_start, $date_end, $group, 2);
						
					}			
					
					?>
					<div class="chart" style="height: 200px;"></div>
					
					<h3>Доли студий</h3>
					<div class="parts">
						<div class="pie" style="width: 50%;"></div>
						<div class="info" style="width: 50%;">
							
						<?php
						
							foreach ($studios as $n => $studio) {
							
							switch ($n) { case 0: $char = 'a'; break; case 1: $char = 'b'; break; case 2: $char = 'c'; break; case 3: $char = 'd'; break; case 4: $char = 'e'; break; }
						?>
							<div class="part">
								<div class="label pl-<?php echo $char; ?>"></div>
								<div class="name"><?php echo $studio->name; ?></div>
								<div class="value"><?php echo array_sum((array) $counts[$studio->id]); ?></div>
								<div class="pc"><?php echo round(array_sum((array) $counts[$studio->id]) / max(1, cnt((array) $events)) * 100); ?>%</div>
								<div class="clear"></div>
							</div>
						<?php } ?>
							
							<div class="part">
								<div class="label"></div>
								<div class="name">Всего</div>
								<div class="value"><?php echo cnt($events); ?></div>
								<div class="pc">100%</div>
								<div class="clear"></div>
							</div>
							
						</div>
					</div>
					
					<?php
					
					$labels_list = $labels[$studios[0]->id];
					
					switch ($ord) {
						
						case 1:
						
							if ($sort == 2) { krsort($labels_list); }
					
						break;
					}
					
					?>
					
					<div class="list">
						<div class="filter">
							<div class="w50">№</div>
							<div class="w200"><a href="javascript:void(0);" onclick="setOrd(<?php echo $id; ?>, 1);">Период</a></div>
							<?php foreach ($studios as $studio) { ?>
							<div class="w200"><?php echo $studio->name; ?></div>
							<?php } ?>
							<p class="clear"></p>
						</div>
					<?php foreach ($labels_list as $n => $label) { ?>
						<div class="item">
							<div class="num"><?php echo $n+1; ?></div>
							<div class="period"><?php echo $label; ?></div>
							<?php foreach ($studios as $studio) { ?>
							<div class="count w200"><?php echo $counts[$studio->id][$n]; ?></div>
							<?php } ?>
							<div class="clear"></div>
						</div>
					<?php } ?>	
						<div class="result">
							<div class="label">Всего</div>
							<div class="date">с <?php echo $date_start; ?> по <?php echo $date_end; ?></div>
							<?php foreach ($studios as $studio) { ?>
							<div class="sum w200"><?php echo array_sum((array) $counts[$studio->id]); ?></div>
							<?php } ?>
							<div class="clear"></div>
						</div>
					</div>
					
					<?php foreach ($studios as $studio) { ?>
					<h3><?php echo $studio->name; ?></h3>
					<div class="parts">
						<div class="pie pie<?php echo $studio->id; ?>" style="width: 50%;"></div>
						<div class="info" style="width: 50%;">
							
							<div class="part">
								<div class="label pl-a"></div>
								<div class="name">Будущие</div>
								<div class="value"><?php echo $events_state_0[$studio->id]; ?></div>
								<div class="pc"><?php echo ($events_state_0[$studio->id]) ? round($events_state_0[$studio->id] / max(1, array_sum((array) $counts[$studio->id])) * 100) : 0; ?>%</div>
								<div class="clear"></div>
							</div>
							
							<div class="part">
								<div class="label pl-d"></div>
								<div class="name">Идет сейчас</div>
								<div class="value"><?php echo $events_state_1[$studio->id]; ?></div>
								<div class="pc"><?php echo ($events_state_1[$studio->id]) ? round($events_state_1[$studio->id] / max(1, array_sum((array) $counts[$studio->id])) * 100) : 0; ?>%</div>
								<div class="clear"></div>
							</div>
							
							<div class="part">
								<div class="label pl-f"></div>
								<div class="name">Прошедшие</div>
								<div class="value"><?php echo $events_state_2[$studio->id]; ?></div>
								<div class="pc"><?php echo ($events_state_2[$studio->id]) ? round($events_state_2[$studio->id] / max(1, array_sum((array) $counts[$studio->id])) * 100) : 0; ?>%</div>
								<div class="clear"></div>
							</div>
							
							<div class="part">
								<div class="label pl-c"></div>
								<div class="name">Неподтвержденные</div>
								<div class="value"><?php echo $events_state_3[$studio->id]; ?></div>
								<div class="pc"><?php echo ($events_state_3[$studio->id]) ? round($events_state_3[$studio->id] / max(1, array_sum((array) $counts[$studio->id])) * 100) : 0; ?>%</div>
								<div class="clear"></div>
							</div>
							
							<div class="part">
								<div class="label"></div>
								<div class="name">Всего</div>
								<div class="value"><?php echo array_sum((array) $counts[$studio->id]); ?></div>
								<div class="pc">100%</div>
								<div class="clear"></div>
							</div>
							
						</div>
					</div>
					<?php }  ?>
					
					<script type="text/javascript">
						new Chartist.Line('#ch<?php echo $id; ?> .chart', {
							labels: ['<?php echo implode("', '", $labels[$studios[0]->id]); ?>'],
							series: [
							<?php foreach ($studios as $n => $studio) { if ($n != 0) { echo ", "; } if ($sid and $sid != $studio->id) { continue; } ?>
								 [{meta: '<?php echo $studio->name; ?>', value: <?php echo implode("},{meta: '{$studio->name}', value: ", $counts[$studio->id]); ?>}]
							<?php } ?>
							]
						}, {
							// high: 10,
							low: 0,
							showArea: false,
							height: 200,
							fullWidth: false,
							plugins: [
							    Chartist.plugins.tooltip()
							]
						});
						
						// Доли
						var data = {
							series: [<?php foreach ($studios as $n => $studio) { if ($n != 0) { echo ", "; } ?>
								<?php echo array_sum((array) $counts[$studio->id]); ?>
							<?php } ?>]
						};

						var sum = function(a, b) { return a + b };

						new Chartist.Pie('#ch<?php echo $id; ?> .pie', data, {
							labelInterpolationFnc: function(value) {
								return Math.round(value / <?php echo cnt($events); ?> * 100) + '%';
							}
						});
						
						<?php foreach ($studios as $studio) { ?>
						// Доли
						// var data = {
							// series: [<?php echo $events_state_0[$studio->id]; ?>, <?php echo $events_state_1[$studio->id]; ?>, <?php echo $events_state_2[$studio->id]; ?>, <?php echo $events_state_4[$studio->id]; ?>, <?php echo $events_state_5[$studio->id]; ?>, <?php echo $events_state_6[$studio->id]; ?>]
							
						// };
						
						var data = { series: [
							{
								value: <?php echo $events_state_0[$studio->id]; ?>,
								name: 'Будущие',
								className: 'ct-series-a'
							}, {
								value: <?php echo $events_state_1[$studio->id]; ?>,
								name: 'Идет сейчас',
								className: 'ct-series-d'
							}, {
								value: <?php echo $events_state_2[$studio->id]; ?>,
								name: 'Прошедшие',
								className: 'ct-series-f'
							}, {
								value: <?php echo $events_state_3[$studio->id]; ?>,
								name: 'Неподтвержденные',
								className: 'ct-series-c'
							}
						]};

						new Chartist.Pie('#ch<?php echo $id; ?> .pie<?php echo $studio->id?>', data, {
							labelInterpolationFnc: function(value) {
								return Math.round(value / <?php echo array_sum((array) $counts[$studio->id]); ?> * 100) + '%';
								// return value;
							}
						});
						<?php } ?>
					</script>
					<?php
				
				break;
			}
		
		break;
	}
	
	
?>