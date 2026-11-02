<?php defined('access') or die ('no access'); ?>
<!DOCTYPE HTML>
<html>
<head>
<?php include path.'/includes/head.php'; ?>
</head>
<body>

	<?php // insertBlock('login'); ?>
	<?php echo insertMsg(); ?>
	
	<?php if ($user->admin) { ?>
	<div id="head">
		
		<div id="title">Панель администрирования | <a href="/" target="_blank"><?php echo siteName; ?></a></div>
		
		<div id="loguot">
			<form action="/auth" method="post">
				<input name="logout" type="hidden" value="1"/>
				<input type="submit" class="" value="выход"/>
			</form>
		</div>
		
	</div>
	
	<?php insertBlock('admin/menu'); ?>
	
	<?php } ?>
	
	<?php if ($user->admin_panel or $user->director) { ?>
	<div id="head">
	
		<?php insertBlock('admin/menu'); ?>
		
		<div id="loguot">
			<form action="/auth" method="post">
				<input name="logout" type="hidden" value="1"/>
				<input type="submit" class="" value="выход"/>
			</form>
		</div>
		
		<?php insertBlock('admin/requests'); ?>
		
	</div>
	
	<?php } ?>
	
	<div id="main"<?php if ($user->admin or $user->admin_panel or $user->director) { ?> class="inner"<?php } ?>>
	
	<?php include path.'/includes/body.php'; ?>
	
	</div>
	
	<div id="about"><div id="version">v1.0</div><a id="m-logo" href="http://mazanik.com/" target="_blank"></a></div>
	
</body>
</html>