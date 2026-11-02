<?php
	
	defined('access') or die ('no access');
	
	$task = getVar('task', 'post');
	
	if (!$view and !$task) {
		
		$notmpl = true;
		
		if (!$user->id) {
		
			if(getVar('logout', 'post')) {
				
				setcookie("user_admin", '', time() - (authTime * 60), "/", ".".domain);
				setcookie("user_admin_panel", '', time() - (authTime * 60), "/", ".".domain);
				setcookie("user_director", '', time() - (authTime * 60), "/", ".".domain);
				redirect('/', "Всего доброго", "ok");
			}
		
			if (getVar('login', 'post') and getVar('key', 'post')) {
				
				$login	= getVar('login', 'post');
				$key	= getVar('key', 'post');
				
				if ($login == 'admin' and $key == apass) {
					
					setcookie("user_admin", 1, time() + (authTime * 60), "/", ".".domain);
					redirect("/admin", "Добро пожаловать", "ok");
				}
				elseif ($login == 'admin_panel' and $key == apass_panel) {
					
					setcookie("user_admin_panel", 1, time() + (authTime * 60), "/", ".".domain);
					redirect("/admin/table", "Добро пожаловать", "ok");
				}
				elseif ($login == 'director' and $key == pass_director) {
					
					setcookie("user_director", 1, time() + (authTime * 60), "/", ".".domain);
					redirect("/admin/table", "Добро пожаловать", "ok");
				}
				else {
					
					redirect("/admin", "Неверный пароль", "error");
				}
				
			}
			elseif (!getVar('login', 'post') and getVar('key', 'post')) {
				$msg = "Логин не введен";
				$class = 'error';
			}
			elseif (getVar('login', 'post') and !getVar('key', 'post')) {
				$msg = "Пароль не введен";
				$class = 'error';
			}
			else {
				$msg = "Логин и пароль не введены";
				$class = 'error';
			}
		
		}
		else {
			
			if(getVar('logout', 'post')) {
				
				// Очищаем user данные
				unsetUser($user->id);
				
				$msg = 'Всего доброго!';
				$class= 'ok';
			
			}
			else {
			
				$msg = 'Ошибка';
				$class= 'error';
			}
			
		}
		
		$url = "/admin";
		
		if (!$url) {
		
			$url = '/'; $msg = 'Доступ запрещен'; $class= 'error';
		}
		
		// Редирект
		redirect($url, $msg, $class);
	}
	
?>