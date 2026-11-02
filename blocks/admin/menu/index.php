<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/admin/menu/data.php";
	
?>
<?php if ($user->admin) { ?>
	<div id="menu">
		<a href="/admin"<?php if($view == 'main') { ?> class="active"<?php } ?>>Главная</a>
		<a href="/admin/pages"<?php if($view == 'pages') { ?> class="active"<?php } ?>>Страницы</a>
		<a href="/admin/blocks"<?php if($view == 'blocks') { ?> class="active"<?php } ?>>Блоки</a>
	</div>
<?php } else { ?>
	<div id="menu">
		<a href="/admin/table"<?php if($view == 'table') { ?> class="active"<?php } ?>>Расписание</a>
		<a href="/admin/tablerooms"<?php if($view == 'tablerooms') { ?> class="active"<?php } ?>>Комнаты</a>
		<a href="/admin/weekly"<?php if($view == 'weekly') { ?> class="active"<?php } ?>>Постоянки</a>
		<?php // if ($user->director) { ?>
		<a href="/admin/edittable"<?php if($view == 'edittable') { ?> class="active"<?php } ?>><?php echo ($user->director) ? "Настройка цен" : "Тарифы"; ?></a>
		<?php // } ?>
		<a href="/admin/clients"<?php if($view == 'clients') { ?> class="active"<?php } ?>>Клиенты</a>
		<a href="/admin/equipments"<?php if($view == 'equipments') { ?> class="active"<?php } ?>>Оборудование</a>
		<a href="/admin/statistics"<?php if($view == 'statistics') { ?> class="active"<?php } ?>>Статистика</a>
		<a href="/admin/economics"<?php if($view == 'economics') { ?> class="active"<?php } ?>>Экономика</a>
		<a href="/admin/debtors"<?php if($view == 'debtors') { ?> class="active"<?php } ?>>Должники</a>
		<a href="/admin/params"<?php if($view == 'params') { ?> class="active"<?php } ?>>Параметры</a>
		<a href="/admin/log"<?php if($view == 'log') { ?> class="active"<?php } ?>>Журнал действий</a>
	</div>
	<?php if($view == 'statistics') { ?>
	<div id="submenu">
		<a href="/admin/statistics?type=1"<?php if(getInt('type', 'get') == 1 or getInt('type', 'get') == 0) { ?> class="active"<?php } ?>>Общая информация</a>
		<a href="/admin/statistics?type=2"<?php if(getInt('type', 'get') == 2) { ?> class="active"<?php } ?>>События</a>
	</div>
	<?php } ?>
	<?php if($view == 'economics') { ?>
	<div id="submenu">
		<a href="/admin/economics?type=1"<?php if(getInt('type', 'get') == 1 or getInt('type', 'get') == 0) { ?> class="active"<?php } ?>>Расходы</a>
		<a href="/admin/economics?type=2"<?php if(getInt('type', 'get') == 2) { ?> class="active"<?php } ?>>Доп. доходы</a>
		<a href="/admin/economics?type=3"<?php if(getInt('type', 'get') == 3) { ?> class="active"<?php } ?>>Отчет</a>
		<!--<a href="/admin/economics?type=4"<?php if(getInt('type', 'get') == 4) { ?> class="active"<?php } ?>>Должники</a>-->
		<a class="print" href="javascript:void(0);" onclick="window.print();">Печать</a>
	</div>
	<?php } ?>
<?php } ?>