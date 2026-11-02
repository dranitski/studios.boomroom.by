<?php
	
	defined('access') or die ('no access');
		
?>
<form id="login" action="/auth" method="post">
		
	<div id="logo"></div>
	
	<div class="lTitle">Вход в панель администрирования</div>

	<select name="login">
		<option value="admin_panel">Расписание</option>
		<option value="director">Директор</option>
		<option value="admin">Сайт</option>
	<select/>
	<input name="key" type="password" value="" placeholder="пароль"/>
	<input type="submit" value="вход"/>
	
</form>