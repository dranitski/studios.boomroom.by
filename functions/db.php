<?php
	
	defined('access') or die ('no access');
	
	// Подключение к базе данных
	
	// PHP 8.1+ бросает исключения на ошибки mysqli по умолчанию; код ниже проверяет ошибки сам
	mysqli_report(MYSQLI_REPORT_OFF);
	
	$dbh = @mysqli_connect(_DB_HOST, _DB_USER, _DB_PASS, _DB_NAME);
	
	if (!$dbh) {
		error_log('MySQL connect error: '.mysqli_connect_error());
		die("Нет соединения с MySQL.");
	}
	
	mysqli_set_charset($dbh, 'utf8');
	
	$countSqls = 0;
	
	function dbEscape($value) {
		
		return mysqli_real_escape_string($GLOBALS['dbh'], (string) $value);
	}
	
	function getQuery($sql) {
		
		$GLOBALS['countSqls']++;
		return mysqli_query($GLOBALS['dbh'], $sql);
	}
	
	// Ошибка запроса пишется в лог, пользователю показывается только факт ошибки
	function dbError($sql) {
		
		$error = mysqli_error($GLOBALS['dbh']);
		
		if ($error) {
			error_log("MySQL error: {$error}; query: {$sql}");
			echo 'Ошибка базы данных';
			return true;
		}
		
		return false;
	}
	
	function getNumRows($sql) {
		
		$cur = getQuery($sql);
		
		if (dbError($sql)) return null;
		
		return mysqli_num_rows($cur);
	}
	
	function loadResult($sql) {
		
		$cur = getQuery($sql);
		
		if (dbError($sql)) return null;
		
		$ret = null;
		if ($row = mysqli_fetch_row($cur)) {
			$ret = $row[0];
		}
		
		mysqli_free_result($cur);
		return $ret;
	}
	
	function loadResultArray($sql) {
		
		$cur = getQuery($sql);
		
		if (dbError($sql)) return array();
		
		$array = array();
		while ($row = mysqli_fetch_row($cur)) {
			$array[] = $row[0];
		}
		
		mysqli_free_result($cur);
		return $array;
	}
	
	function loadAssoc($sql) {
		
		$cur = getQuery($sql);
		
		if (dbError($sql)) return null;
		
		$ret = null;
		if ($array = mysqli_fetch_assoc($cur)) {
			$ret = $array;
		}
		
		mysqli_free_result($cur);
		return $ret;
	}
	
	function loadAssocList($sql) {
		
		$cur = getQuery($sql);
		
		if (dbError($sql)) return array();
		
		$array = array();
		while ($row = mysqli_fetch_assoc($cur)) {
			$array[] = $row;
		}
		
		mysqli_free_result($cur);
		return $array;
	}
	
	function loadObject($sql) {
		
		$cur = getQuery($sql);
		
		if (dbError($sql)) return null;
		
		$ret = null;
		if ($object = mysqli_fetch_object($cur)) {
			$ret = $object;
		}
		
		mysqli_free_result($cur);
		return $ret;
	}
	
	function loadObjectList($sql) {
		
		$cur = getQuery($sql);
		
		if (dbError($sql)) return array();
		
		$array = array();
		while ($row = mysqli_fetch_object($cur)) {
			$array[] = $row;
		}
		
		mysqli_free_result($cur);
		return $array;
	}
	
	function runSql($sql) {
		
		getQuery($sql);
		
		if (dbError($sql)) return null;
		
		return true;
	}
	
	function insertId() {
		
		return mysqli_insert_id($GLOBALS['dbh']);
	}
	
?>
