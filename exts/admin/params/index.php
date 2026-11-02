<?php
	
	defined('access') or die ('no access');
	
	$holidays = $params->holidays;
	
	if ($year = getInt('year', 'get')) {
		
		include path.'/functions/doom.php';
			
		$html = file_get_html("http://www.calendar.by/?year={$year}");
		$hds = $html->find('.tooltip');
		
		$dates_old = explode(",", $params->holidays);
		
		$dates_new = array();
		foreach ($hds as $hd) {
			
			$date = str_replace("day.php?date=", "", $hd->getAttribute('href'));
			$date = str_replace("-", ".", $date);
			$dates_new[] = $date;
		}
		
		$dates = array_unique(array_merge($dates_old, $dates_new));
		
		$holidays = implode(',', $dates);
	}
	
?>

<div class="block Bl mt0">
						
	<div class="bHead">
		<div class="bName">Выходные и праздники</div>
		<div class="bActions">
			<a class="save" href="javascript:void(0);" onclick="saveForm('holidays');">Сохранить</a>
		</div>
	</div>
	
	<div class="clear"></div>
	
	<form id="holidays">
	<textarea name="data" style="width: 100%; height: 150px; padding: 5px 10px; box-sizing: border-box;"><?php echo $holidays; ?></textarea>
	</form>

</div>

<script type="text/javascript">
function saveForm(formname){
	
	switch (formname) {
		
		case 'holidays':
			
			$.ajax({
		
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'params',
					'subtask':	'holidays',
					'data':		$('#holidays textarea[name="data"]').val()
					
				},
				dataType: 'script'
				
			});
			
		break;
	}
}
</script>