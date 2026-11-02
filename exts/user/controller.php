<?php
	
	defined('access') or die ('no access');
	
	$task = getVar('task', 'post');
	
	if (!$view and !$task) {
		
		$notmpl = true;
		
		if (getVar('logout', 'post')) {
			
			authLogout();
			redirect('/', "Всего доброго", "ok");
		}
		
		$login	= getVar('login', 'post');
		$key	= getVar('key', 'post', 0, 0);
		
		if ($login and $key !== '') {
			
			if (authCheckPassword($login, $key)) {
				
				authLogin($login);
				redirect(($login == 'admin') ? "/admin" : "/admin/table", "Добро пожаловать", "ok");
			}
			
			// замедляет перебор паролей
			sleep(1);
			redirect("/admin", "Неверный пароль", "error");
		}
		elseif (!$login and $key !== '') {
			$msg = "Логин не введен";
			$class = 'error';
		}
		elseif ($login and $key === '') {
			$msg = "Пароль не введен";
			$class = 'error';
		}
		else {
			$msg = "Логин и пароль не введены";
			$class = 'error';
		}
		
		$url = "/admin";
		
		if (!$url) {
		
			$url = '/'; $msg = 'Доступ запрещен'; $class= 'error';
		}
		
		// Редирект
		redirect($url, $msg, $class);
	}
	
?>