<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/feedback/data.php";
?>

<?php $result = isset($_GET['result']) ? $_GET['result'] : ''; if (in_array($result, array('ok', 'no'), true)) { ?>
<div id="sendResult" class="sr_<?php echo $result; ?>">
	<?php if ($result == "ok") { ?>Спасибо за Ваше сообщение!<?php } ?>
	<?php if ($result == "no") { ?>Ошибка отправки сообщения! Попробуйте позже.<?php } ?>
</div>
<?php } ?>

<div class="feedback">
	<form id="form" action="/blocks/feedback/action.php" method="post">
		Как к Вам обращаться:
		<input type="text" name="name" required placeholder="фамилия имя отчество">
		Email для связи:
		<input type="email" name="email" required placeholder="адрес электронной почты">
		Тема:
		<input type="text" name="tema" placeholder="тема сообщения">
		Текст:
		<textarea required name="text" rows="5"></textarea>
		<input type="submit" value="отправить">
		
		<input type="hidden" name="task" value="send"/>
	</form>
</div>