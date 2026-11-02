<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/admin/menu/data.php";
	
?>
<div id="requests" data-count="0">
	<p onclick="showRequests();"><span>0</span>Заявки</p>
	<div class="w" onclick="hideRequests();"><div class="list" onclick="event.stopPropagation();"></div></div>
</div>