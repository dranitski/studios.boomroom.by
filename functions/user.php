<?php
	
	defined('access') or die ('no access');
	
	// Авторизация в админке: роль хранится в cookie user_<role> как "<истекает>.<hmac>".
	// Ключ подписи зависит от пароля роли, поэтому смена пароля разлогинивает всех с этой ролью.
	
	function authRoles() {
		
		return array('admin' => apass, 'admin_panel' => apass_panel, 'director' => pass_director);
	}
	
	function authSign($role, $expires) {
		
		$roles = authRoles();
		$key = hash('sha256', "studios-auth|{$role}|{$roles[$role]}|"._DB_PASS);
		
		return hash_hmac('sha256', "{$role}|{$expires}", $key);
	}
	
	function authCookie($role, $value, $expires) {
		
		$secure = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off');
		
		// host-only cookie (без domain), недоступна из JS; SameSite=Lax не отправляет ее в POST с чужих сайтов (CSRF)
		$cookie = "user_{$role}=".rawurlencode($value).'; Expires='.gmdate('D, d M Y H:i:s', $expires).' GMT; Max-Age='.max(0, $expires - time()).'; Path=/; HttpOnly; SameSite=Lax';
		header('Set-Cookie: '.$cookie.($secure ? '; Secure' : ''), false);
	}
	
	function authLogin($role) {
		
		$expires = time() + authTime * 60;
		authCookie($role, $expires.'.'.authSign($role, $expires), $expires);
	}
	
	function authLogout() {
		
		foreach (array_keys(authRoles()) as $role) {
			
			authCookie($role, '', time() - 3600);
			// cookie старого формата ставились на .boomroom.by
			setcookie("user_{$role}", '', time() - 3600, '/', '.'.domain);
		}
	}
	
	function authCheckPassword($role, $key) {
		
		$roles = authRoles();
		
		return isset($roles[$role]) and $roles[$role] !== '' and hash_equals((string) $roles[$role], (string) $key);
	}
	
	function authHasRole($role) {
		
		$value = isset($_COOKIE["user_{$role}"]) ? (string) $_COOKIE["user_{$role}"] : '';
		$parts = explode('.', $value, 2);
		
		if (cnt($parts) != 2 or !ctype_digit($parts[0]) or (int) $parts[0] < time()) {
			return false;
		}
		
		return hash_equals(authSign($role, $parts[0]), $parts[1]);
	}
	
	class User {
		
		public $admin = false;
		public $admin_panel = false;
		public $director = false;
		public $id = 0;
		public $type = null;
	}
	
	$user = new User;
	
	$user->admin		= authHasRole('admin');
	$user->admin_panel	= authHasRole('admin_panel');
	$user->director		= authHasRole('director');
	
?>
