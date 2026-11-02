<?php

	define('access', 1);
	
	// Настройки
	include $_SERVER['DOCUMENT_ROOT'].'/config.php';
	// Подключение основных функций
	include path.'/functions/main.php';
	include path.'/functions/db.php';
	include path.'/functions/user.php';
	include path.'/functions/messages.php';
	
	// Изменять настройки блока может только администратор сайта, отправка форм открыта всем
	if (getVar('task', 'post') != 'send' and !$user->admin) {
		header('HTTP/1.1 403 Forbidden');
		exit;
	}
	
	switch (getVar('task', 'post')) {
		
		case 'save':
			
			$r		= getInt('r', 'post');
			$bid	= getInt('bid', 'post');
			
			$email		= getVar('email', 'post');
			$subject	= getVar('subject', 'post');
			
			$new_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<data>\n\t<params>\n";
			$new_xml .= "\t\t<email>{$email}</email>\n";
			$new_xml .= "\t\t<subject>{$subject}</subject>\n";
			$new_xml .= "\t</params>\n</data>";
			
			file_put_contents("admin.xml", $new_xml);
			
			$a_link = $r ? "" : "?edit={$bid}";
			
			echo getMsg("Действие выполнено", "ok", "window.location.assign('/admin/blocks{$a_link}');");
		
		break;
		
		case 'send':
			
			$name	= getVar('name', 'post');
			$phone	= getVar('phone', 'post');
			
			// Получаем данные
			// $xml = simplexml_load_file(path."/blocks/callback/admin.xml");
			
			// $mailTo		= (string) $xml->params->email;
			// $subject	= (string) $xml->params->subject;
			
			$mailTo		= "bw.studio@mail.ru";
			$subject	= "Запись на занятие";
			
			$html		= "";
			
			$html		.= "Имя: {$name}<br/>";
			$html		.= "Телефон: {$phone}<br/>";
			
			if (sendEmail($mailTo, $subject, $html)) {
			?>
			$('#enroll form').hide();
			$('#enroll .result').show();
			setTimeout(function(){ window.location.reload() }, 5000);
			<?php
			}
			else {
			?>
			alert('Ошибка отправки запроса. Попробуйте еще раз!');
			<?php
			}
			
		break;
	}
	
	if (!getVar('task', 'post')) { exit; }
	
?>