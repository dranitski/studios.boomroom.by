<?php
	
	defined('access') or die ('no access');
	
	$txtDn = getDayWeek(date('w', strtotime($date)), 1);
	
?>
<div id="tablerooms-head">
	<h1><input id="date" type="text" value="<?php echo $date; ?>" readonly="readonly" style="cursor: pointer;"/>, <?php echo $txtDn; ?></h1>
</div>

<script type="text/javascript">

	$('#date').datepick($.extend({ 
		
		showAnim: 'slideDown',
		onSelect: function() {
			
			var thisDate = new Date($('#date').datepick('getDate'));
			var targetDate = thisDate.getDate()+'.'+(thisDate.getMonth() + 1)+'.'+thisDate.getFullYear();
			
			location.assign('<?php echo admin; ?>/tablerooms?date='+targetDate);
		}
	
	}, $.datepick.regional['ru']));
	
</script>
