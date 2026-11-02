<?php
	
	defined('access') or die ('no access');
	
	// Подключение к базе данных
	
	$host			= _DB_HOST; // адрес базы данных
	$database		= _DB_NAME; // имя базы данных
	$user			= _DB_USER; // пользователь баз данных
	$pswd			= _DB_PASS; // пароль
	
	$dbh = mysql_connect($host, $user, $pswd) or die("Нет соединения с MySQL.");
	mysql_select_db($database) or die("Нет соединения с базой данных.");
	
	mysql_query("SET NAMES 'utf8'", $dbh);
	
	$countSqls = 0;
	
	function getQuery($sql) {
		
		$GLOBALS['countSqls']++;
		return mysql_query($sql); 
		
	}
	
	function getNumRows($sql) {
		
		$num = getQuery($sql);
		
		if (mysql_error()) {
			echo 'Ошибка: '.mysql_error();
		} else {
			return mysql_num_rows($num);
		}
	}
	
	function loadResult($sql) {
	
		$cur = getQuery($sql);
			
		$ret = null;
		if ($row = mysql_fetch_row( $cur )) {
			$ret = $row[0];
		}
		
		if (mysql_error()) {
			echo 'Ошибка: '.mysql_error();
		} else {
			mysql_free_result( $cur );
			return $ret;
		}
	}
	
	function loadResultArray($sql) {
	
		$cur = getQuery($sql);
		
		$array = array();
		while ($row = mysql_fetch_row($cur)) {
			$array[] = $row[0];
		}
		if (mysql_error()) {
			echo 'Ошибка: '.mysql_error();
		} else {
			mysql_free_result( $cur );
			return $array;
		}
	}
	
	function loadAssoc($sql) {
		
		$cur = getQuery($sql);
	
		$ret = null;
		if ($array = mysql_fetch_assoc( $cur )) {
			$ret = $array;
		}
		
		if (mysql_error()) {
			echo 'Ошибка: '.mysql_error();
		} else {
			mysql_free_result( $cur );
			return $ret;
		}
	}
	
	function loadAssocList($sql) {
		
		$cur = getQuery($sql);
		
		$array = array();
		while ($row = mysql_fetch_assoc($cur)) {
			$array[] = $row;
		}
		if (mysql_error()) {
			echo 'Ошибка: '.mysql_error();
		} else {
			mysql_free_result( $cur );
			return $array;
		}
	}
	
	function loadObject($sql) {
	
		$cur = getQuery($sql);
	
		$ret = null;
		if ($object = mysql_fetch_object( $cur )) {
			$ret = $object;
		}
		
		if (mysql_error()) {
			echo 'Ошибка: '.mysql_error();
		} else {
			mysql_free_result( $cur );
			return $ret;
		}
	}

	function loadObjectList($sql) {
		
		$cur = getQuery($sql);
		
		$array = array();
		while ($row = mysql_fetch_object($cur)) {
			$array[] = $row;
		}
		if (mysql_error()) {
			echo 'Ошибка: '.mysql_error();
		} else {
			mysql_free_result( $cur );
			return $array;
		}
	}
	
	function runSql($sql) {
	
		getQuery($sql);
		
		if (mysql_error()) {
			echo 'Ошибка: '.mysql_error();
		}
		else {
			return true;
		}
	}
	
	function insertId() {
	
		return mysql_insert_id();
	}

	
?>