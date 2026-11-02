<?php

	defined('access') or die ('no access');
	
	$orderby	= getVar('orderby', 'get');
	$sort		= getVar('sort', 'get');
	
	if (!$sort) { $sort =  'desc'; }
	if ($sort == 'asc') { $sort = 'desc'; } else { $sort = 'asc'; }
	
	switch ($orderby) {
		
		case 'id':
		
			$orderby = "`id` {$sort}";
		break;
	
		case 'created':
		
			$orderby = "`created` {$sort}";
		break;
		
		default:
			
			$orderby = "`id` DESC";
	}
	
	$where = " WHERE `id` > 0";
	
	$date_d1 = getVar('date_d1', 'get'); if ($date_d1 == 'null') { $date_d1 = null; }
	$date_d2 = getVar('date_d2', 'get'); if ($date_d2 == 'null') { $date_d2 = null; }
	
	if (!$date_d1 and !$date_d2) {
		
		// $date_d1 = date('01.m.Y');
		$date_d1 = date('d.m.Y');
		$date_d2 = date('d.m.Y');
	}
	$sql_date_d1 = date('Y-m-d 00:00:00', strtotime($date_d1));
	$sql_date_d2 = date('Y-m-d 23:59:59', strtotime($date_d2));
	
	if ($date_d1 and $date_d2) { $where .= " AND `created` BETWEEN '{$sql_date_d1}' AND '{$sql_date_d2}'"; }
	if ($date_d1 and $date_d2 == null) { $where .= " AND `created` >= '{$sql_date_d1}'"; }
	if ($date_d2 and $date_d1 == null) { $where .= " AND `created` <= '{$sql_date_d2}'"; }
	
	$sql = "SELECT *" .
			" FROM `log`".
			"{$where}".
			" ORDER BY {$orderby}";
	
	$log = loadObjectList($sql);
	
	// Устанавливаем HEAD
	$title = "Журнал действий";
	
	$tags['script'][] = "/plugins/datepick/datepick.js";
	$tags['script'][] = "/plugins/datepick/datepick-ru.js";
	$tags['style'][] = "/plugins/datepick/datepick.css";
?>