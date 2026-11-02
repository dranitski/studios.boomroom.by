<?php
	
	defined('access') or die ('no access');
	
	$txtDn = getDayWeek(date('w', strtotime($date)), 1);
	
?>
<div id="select-date">
	На дату: 
	<input id="date" type="text" value="<?php echo $date; ?>" readonly="readonly" style="cursor: pointer;"/>, <?php echo $txtDn; ?>
</div>

<script type="text/javascript">

	$('#date').datepick($.extend({ 
		
		showAnim: 'slideDown',
		onSelect: function() {
			
			var thisDate = new Date($('#date').datepick('getDate'));
			var targetDate = thisDate.getDate()+'.'+(thisDate.getMonth() + 1)+'.'+thisDate.getFullYear();
			
			location.assign('/booking-rooms?date='+targetDate+'#booking');
		}
	
	}, $.datepick.regional['ru']));
	
</script>
