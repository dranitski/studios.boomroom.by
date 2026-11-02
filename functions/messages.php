<?php
	
	defined('access') or die ('no access');
	
	require_once __DIR__.'/inc/PHPMailer/Exception.php';
	require_once __DIR__.'/inc/PHPMailer/PHPMailer.php';
	require_once __DIR__.'/inc/PHPMailer/SMTP.php';
	
	function sendEmail($toEmail, $subject, $html, $toName = null, $fromEmail = null, $fromName = null, $text = null, $files = null) {
		
		if (!$fromEmail) {
			$fromEmail	= 'robot@'.domain;
		}
		if (!$fromName) {
			$fromName	= strtoupper(domain);
		}
		if (!$text) {
			$text			= null;
		}
		if (!$files) {
			$files			= null;
		}
		
		// Создаем экземпляр класса
		$mail = new \PHPMailer\PHPMailer\PHPMailer();
		
		$mail->CharSet = 'UTF-8';
		
		// От
		$mail->setFrom($fromEmail, $fromName);
		
		// Кому
		$mail->addAddress($toEmail, $toName);
		
		// Тема сообщения
		$mail->Subject = $subject;
		
		// Текст сообщения
		$mail->isHTML(true);
		$mail->Body = $html;
		
		if ($text) {
			$mail->AltBody = $text;
		}
		
		// Приложения
		if ($files) {
		
			if (is_array($files)) {
				foreach ($files as $file) {
					
					$mail->addAttachment($file);
				}
			}
			else {
			
				$mail->addAttachment($files);
			}
		}
		
		if (!$mail->send()) {
			
			// return "Сообщение не отправлено: {$mail->ErrorInfo}";
			return false;
		} else {
			
			// return "Сообщение отправлено";
			return true;
		}
		
	}
	
?>