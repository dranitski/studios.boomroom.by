<?php
	
	defined('access') or die ('no access');
	
	// count() как в PHP 5.6/7: null -> 0, скаляр -> 1 (PHP 8 бросает TypeError, а код повсюду считает результаты запросов, которые могут быть null)
	function cnt($value) {
		
		if (is_array($value) or $value instanceof Countable) {
			return count($value);
		}
		
		return ($value === null) ? 0 : 1;
	}
	
	function getVar($name, $method, $html = 1, $sql = 0) {
		
		switch (strtoupper($method))
		{
			case 'GET' :
				$input = &$_GET;
				break;
			case 'POST' :
				$input = &$_POST;
				break;
			case 'FILES' :
				$input = &$_FILES;
				break;
			case 'COOKIE' :
				$input = &$_COOKIE;
				break;
			case 'ENV'    :
				$input = &$_ENV;
				break;
			case 'SERVER'    :
				$input = &$_SERVER;
				break;
			default:
				$input = &$_REQUEST;
				break;
		}
		
		// массивы (x[]=1) не принимаются
		$var = (isset($input[$name]) and is_scalar($input[$name])) ? trim((string) $input[$name]) : "";
		
		if ($html) {
			$var = strip_tags($var);
			// ENT_QUOTES и замена "\" на сущность делают значение безопасным внутри '...' в SQL
			$var = htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
			$var = str_replace('\\', '&#92;', $var);
		}
		
		if ($sql) {
			$var = dbEscape($var);
		}
		
		return $var;
		
	}
	
	// Оборудование события из POST: JSON [{"id":N,"count":N}], только целые числа
	function getEqs($name = 'eqs') {
		
		$data = json_decode(getVar($name, 'post', 0, 0));
		
		$eqs = array();
		if (is_array($data)) {
			foreach ($data as $eq) {
				if (is_object($eq) and isset($eq->id, $eq->count) and (int) $eq->id > 0 and (int) $eq->count > 0) {
					$eqs[] = array('id' => (int) $eq->id, 'count' => (int) $eq->count);
				}
			}
		}
		
		return json_encode($eqs);
	}
	
	// Поля формы из POST: JSON [{"name":..,"value":..}] (jQuery serializeArray); $sql - экранировать значения для SQL
	function getFormFields($name = 'data', $sql = true) {
		
		$data = json_decode(getVar($name, 'post', 0, 0));
		
		$fields = array();
		if (is_array($data)) {
			foreach ($data as $field) {
				if (is_object($field) and isset($field->name) and is_scalar($field->name)) {
					$value = (isset($field->value) and is_scalar($field->value)) ? (string) $field->value : '';
					$fields[] = (object) array('name' => (string) $field->name, 'value' => $sql ? dbEscape($value) : $value);
				}
			}
		}
		
		return $fields;
	}
	
	// Уведомление repa.by об изменении расписания; короткий таймаут, чтобы недоступный repa.by не держал PHP-воркер
	function repaNotify($url) {
		
		// сессия заблокирована до конца запроса: освобождаем ее, чтобы другие вкладки админа не ждали repa.by
		if (session_status() == PHP_SESSION_ACTIVE) {
			session_write_close();
		}
		
		$context = stream_context_create(array('http' => array('timeout' => 5)));
		$result = @file_get_contents($url, false, $context);
		
		if ($result === false) {
			error_log("repa.by notify failed: {$url}");
		}
		
		return $result;
	}
	
	function getInt($name, $method) {
		
		$int = getVar($name, $method, 0, 0);
		return (int)$int;
		
	}
	
	function insertBlock($name) {
		
		if (file_exists(path."/blocks/{$name}/index.php")) {
		
			include path."/blocks/{$name}/index.php";
			
		}
		else {
			
			echo "Блок с именем <b>{$name}</b> не существует.";
			
		}
		
	}
	
	function insertMsg() {
		
		$msg	= $_SESSION['msg'];
		$type	= $_SESSION['msg_type'];
		
		if (!$type) {
			$type = 'none';
		}
		
		$msg = "<div id=\"message\" class=\"{$type}Msg\">{$msg}</div>";
		
		if (isset($_SESSION['msg'])) {
			unset($_SESSION['msg']);
		}
	
		if (isset($_SESSION['msg_type'])) {
			unset($_SESSION['msg_type']);
		}
		
		return $msg;
		
	}
	
	function getMsg($msg = null, $type = null, $code = null) {
		
		$script = "";
		$script .= "$('#message').text('{$msg}');";
		$script .= "$('#message').fadeIn();";
		$script .= "$('#message').attr('class', '{$type}Msg');";
		$script .= "setTimeout(function() {";
		$script .= "$('#message').fadeOut(1500);";
		$script .= "{$code}";
		$script .= "}, 1500);";
					
		return $script;
		
	}
	
	function redirect($url, $msg = null, $type = null) {
		
		if(!$url) {
			$url = '/';
		}
		
		if ($url == "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) {
		
			header("Location: /");
			
		}
		else {
		
			$_SESSION['msg']		= $msg;
			$_SESSION['msg_type']	= $type;
			
			header("Location: {$url}");
			
		}
		
		exit;
		
	}
	
	function setText($text = '') {
	
		$text = stripslashes($text);
		
		if (mb_strpos($text, "{block ") !== false) {
			
			$parts = explode("{block ", $text);
			
			foreach ($parts as $n => $part) {
				
				$find_s		= mb_strpos($part, "}");
				$part_len	= mb_strlen($part, "utf-8");
				
				if ($find_s and $find_s < 20) {
					
					$text = mb_substr($part, $find_s +1, $part_len, "utf-8");
					$blockName = mb_substr($part, 0, $find_s, "utf-8");
					
					if (file_exists(path."/blocks/{$blockName}/index.php")) {
						
						insertBlock($blockName);
					}
					else {
						
						echo "{блок <b>{$blockName}</b> не установлен}";
					}
					
					echo $text;
				}
				else {
					
					echo $part;
				}
			}
		}
		else {
		
			echo $text;
		}
		
	}
	
	function getIntroText($text, $letters = 10) {
	
		$text = mb_substr($text, 0, $letters, "utf-8");
		
		if (strlen($text) > $letters) {
			
			$text .= "...";
		}
		
		return $text;
		
	}
	
	function getFirstSrc($html) {
	
		$regex = '!src="(.+)"!U';
		preg_match ($regex, $html, $matches);
		$srcs = (cnt($matches)) ? $matches : array();
		
		return str_replace('"', '', str_replace('src="', '', $srcs[0]));
	}
	
	function getTranslit($string) {
		$replace=array(
			"&quot;"=>"",
			":"=>"",
			" "=>"-",
			"?"=>"","'"=>"","`"=>"","«"=>"","»"=>"",","=>"","."=>"","!"=>"","--"=>"-",
			"а"=>"a","А"=>"a",
			"б"=>"b","Б"=>"b",
			"в"=>"v","В"=>"v",
			"г"=>"g","Г"=>"g",
			"д"=>"d","Д"=>"d",
			"е"=>"e","Е"=>"e",
			"ж"=>"zh","Ж"=>"zh",
			"з"=>"z","З"=>"z",
			"и"=>"i","И"=>"i",
			"й"=>"y","Й"=>"y",
			"к"=>"k","К"=>"k",
			"л"=>"l","Л"=>"l",
			"м"=>"m","М"=>"m",
			"н"=>"n","Н"=>"n",
			"о"=>"o","О"=>"o",
			"п"=>"p","П"=>"p",
			"р"=>"r","Р"=>"r",
			"с"=>"s","С"=>"s",
			"т"=>"t","Т"=>"t",
			"у"=>"u","У"=>"u",
			"ф"=>"f","Ф"=>"f",
			"х"=>"h","Х"=>"h",
			"ц"=>"c","Ц"=>"c",
			"ч"=>"ch","Ч"=>"ch",
			"ш"=>"sh","Ш"=>"sh",
			"щ"=>"sch","Щ"=>"sch",
			"ъ"=>"","Ъ"=>"",
			"ы"=>"y","Ы"=>"y",
			"ь"=>"","Ь"=>"",
			"э"=>"e","Э"=>"e",
			"ю"=>"yu","Ю"=>"yu",
			"я"=>"ya","Я"=>"ya",
			"і"=>"i","І"=>"i",
			"ї"=>"yi","Ї"=>"yi",
			"є"=>"e","Є"=>"e"
		);
		return $str=iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
	}
	
	function removeDirectory($dir) {
		
		if ($objs = glob($dir."/*")) {
			
			foreach($objs as $obj) {
				is_dir($obj) ? removeDirectory($obj) : unlink($obj);
			}
		}
		rmdir($dir);
	}
	
	function tmplText($name, $text, $head = true, $contacts = true, $footer = true) {
		
		$tmplPath = path."/tmpl/mail/{$name}.tpl";
		
		if (file_exists($tmplPath) == false) {

			return "Шаблон `{$name}` отсутствует";
		}
		else {
			
			$output = "";
			
			if ($head) {
				
				$output .= file_get_contents(path."/tmpl/mail/sys/head.tpl");
			}
			
			$tmpl = file_get_contents($tmplPath);
			
			$output .= str_replace(
					
				array(
				"%text%"
				),
				array (
				$text
				),
				
				$tmpl
			);
			
			// $output .= file_get_contents($tmplPath);
			
			if ($contacts) {
				 
				$output .= file_get_contents(path."/tmpl/mail/sys/contacts.tpl");
			}
			
			if ($footer) {
				
				$output .= file_get_contents(path."/tmpl/mail/sys/footer.tpl");
			}
			
			return $output;
		}
		
	}
	
	function sortByOrd($a1,$a2) {
		
		if($a1->ord < $a2->ord) return -1;
		elseif($a1->ord > $a2->ord) return 1;
		else return 0;
	}
	
	function getPhone($phone) {
			
		$number = substr($phone, 2);
		$code = substr($phone, 0, 2);
		
		return "{$code} {$number}";
			
	}
	
	function getPrice($sum = 0, $short = 1) {
		
		switch ($short) {
			
			case 1: return number_format($sum, 1, '.', ' '); break;
			case 2: return number_format($sum, 2, '.', ' '); break;
		}
	}
	
	// Постановка задачи на синхронизацию данных
	// Число проведенных репетиций клиента (для уровня скидки); все клиенты считаются одним запросом за запрос страницы
	function clientEventsCount($cid) {
		
		static $counts = null;
		
		if ($counts === null) {
			$counts = array();
			foreach (loadAssocList("SELECT `cid`, COUNT(*) AS `n` FROM `events` WHERE `state` = 2 AND `cid` > 0 GROUP BY `cid`") as $row) {
				$counts[$row['cid']] = (int) $row['n'];
			}
		}
		
		return isset($counts[(int) $cid]) ? $counts[(int) $cid] : 0;
	}
	
	// Имя/карта клиента; все клиенты читаются одним запросом за запрос страницы
	function clientField($cid, $field) {
		
		static $clients = null;
		
		if ($clients === null) {
			$clients = array();
			foreach (loadAssocList("SELECT `id`, `name`, `card` FROM `clients`") as $row) {
				$clients[$row['id']] = $row;
			}
		}
		
		return isset($clients[(int) $cid][$field]) ? $clients[(int) $cid][$field] : null;
	}
	
	// Сумма неоплаченного по прошедшим событиям клиента (с 01.07.2017)
	function clientDebt($cid) {
		
		static $debts = null;
		
		if ($debts === null) {
			$debts = array();
			foreach (loadAssocList("SELECT `cid`, SUM(`price` - `pay`) AS `debt` FROM `events` WHERE (`price` - `pay`) > 0 AND `state` > 1 AND `date` >= '2017-07-01' GROUP BY `cid`") as $row) {
				$debts[$row['cid']] = (float) $row['debt'];
			}
		}
		
		return isset($debts[(int) $cid]) ? $debts[(int) $cid] : 0;
	}
	
	// Сумма погашений долгов клиента (incomes по его событиям)
	function clientPaid($cid) {
		
		static $paid = null;
		
		if ($paid === null) {
			$paid = array();
			foreach (loadAssocList("SELECT e.`cid`, SUM(i.`sum`) AS `paid` FROM `incomes` AS i JOIN `events` AS e ON e.`id` = i.`eid` GROUP BY e.`cid`") as $row) {
				$paid[$row['cid']] = (float) $row['paid'];
			}
		}
		
		return isset($paid[(int) $cid]) ? $paid[(int) $cid] : 0;
	}
	
	// Имя студии
	function getStudioName($id = 0) {
		
		switch ($id) {
			
			case 1: $name = "BooM"; break;
			case 2: $name = "Hendrix"; break;
			case 3: $name = "Jam"; break;
			case 4: $name = "Точка"; break;
			default: $name = "";
		}
		
		return $name;
	}
	
	// Имя типа клиента
	function getClientTypeName($num = 0) {
		
		switch ($num) {
			
			case 0: $name = "Неопределен"; break;
			case 1: $name = "Группа"; break;
			case 2: $name = "Музыкант"; break;
			case 3: $name = "Админ"; break;
			default: $name = "";
		}
		
		return $name;
	}
	
	// Контакты клиента
	function getClientContacts($json = null, $type = 0) {
		
		if (!$json) return null;
		
		$contacts = json_decode($json);
		
		switch($type) {
			
			case 0:
			
				$strings = [];
				
				if ($contacts) {
					
					foreach ($contacts as $contact) {
						
						if (!$contact->phone) continue;
						
						$string = $contact->phone;
						
						if ($contact->name) {
							
							$string .= " ({$contact->name})";
						}
						
						$strings[] = $string;
					}
					
					$contacts = implode(', ', $strings);
				}
				else {
					
					$contacts = '';
				}
			
			break;
			
			case 1:
			
				//
			
			break;
		}
		
		
		return $contacts;
	}
	
	// Название типа расхода
	function getExpenseTypeName($num = 0) {
		
		switch ($num) {
			
			case 0: $name = "Прочие"; break;
			case 1: $name = "Заработная плата"; break;
			case 2: $name = "Мелко-бюджетный расход"; break;
			case 3: $name = "Барабанные расходы"; break;
			case 4: $name = "Транспортные расходы"; break;
			case 5: $name = "Строительные расходы"; break;
			case 6: $name = "Ремонты"; break;
			default: $name = "";
		}
		
		return $name;
	}
	
	// Уровень клиента
	function getClientLevel($count = 0, $type = 0) {
	
		if ($count < 5) {
			
			$level = 1;
			$dis = 5;
			$tonext = 5 - $count;
		}
		elseif ($count >= 5 and $count < 12) {
			
			$level = 2;
			$dis = 7;
			$tonext = 12 - $count;
		}
		elseif ($count >= 12 and $count < 22) {
			
			$level = 3;
			$dis = 10;
			$tonext = 22 - $count;
		}
		elseif ($count >= 22 and $count < 32) {
			
			$level = 4;
			$dis = 10;
			$tonext = 32 - $count;
		}
		else {
			
			$level = 5;
			$dis = 10;
		}
		
		switch ($type) {
			
			case 0: return $level; break;
			case 1: return $dis; break;
			case 2: return $tonext; break;
		}
	}
?>