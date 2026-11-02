<?php
	
	defined('access') or die ('no access');

	$orderLink = admin."/log?f=1";
	
	if ($date_d1) { $orderLink .= "&date_d1={$date_d1}"; }
	if ($date_d2) { $orderLink .= "&date_d2={$date_d2}"; }
	
	$orderLink_id	= "{$orderLink}&orderby=id&sort={$sort}";
	$orderLink_created	= "{$orderLink}&orderby=created&sort={$sort}";
?>

<div class="block Bl mt0">
	
	<div class="bHead">
		<div class="bName">Журнал действий</div>
	</div>
	
	<div class="filter">
		<div style="float: left;">
			<span>Период</span> 
			с <input id="date_d1" type="text" value="<?php echo $date_d1; ?>" readonly="readonly" style="width: 70px; cursor: pointer;"/>
			по <input id="date_d2" type="text" value="<?php echo $date_d2; ?>" readonly="readonly" style="width: 70px; cursor: pointer;"/>
			<input type="button" class="apply" value="применить" style="margin-left: 10px;" onclick="filter();"/>
			<input type="button" class="reset" value="сбросить" onclick="location.assign('<?php echo admin; ?>/log?f=0');" style="padding: 0 5px;" />
		</div>
		<p class="clear"></p>
		<div class="w50"><a href="<?php echo $orderLink_id; ?>">Id</a></div>
		<div class="w150"><a href="<?php echo $orderLink_created; ?>">Дата и время</a></div>
		<div class="">Событие</div>
		<p class="clear"></p>
	</div>
	
	<div id="log">
	<?php if (count($log)) { ?>
	<?php foreach ($log as $item) { ?>
	<div class="item">
		<div class="id w50"><?php echo $item->id; ?></div>
		<div class="name w150"><?php echo date('d.m.Y в H:i:s', strtotime($item->created)); ?></div>
		<div class="name "><?php echo $item->text; ?></div>
		<p class="clear"></p>
	</div>
	<?php } ?>
	<?php } ?>
	</div>
	
</div>


<script type="text/javascript">

	$('#date_d1').datepick($.extend({ 
		
		showAnim: 'slideDown',
		onSelect: function() {
			
			var thisDate = new Date($('#date_d1').datepick('getDate'));
			var minDate = thisDate.getDate()+'.'+(thisDate.getMonth() + 1)+'.'+thisDate.getFullYear();
			$('#date_d2').datepick('option', 'minDate', minDate);
		}
	
	}, $.datepick.regional['ru']));

	$('#date_d2').datepick($.extend({ 
		
		showAnim: 'slideDown'
	
	}, $.datepick.regional['ru']));
	
	function filter(){
		
		// var sid = $('#sid').val();
	
		var date_d1 = ($('#date_d1').val()) ? $('#date_d1').val() : 'null';
		var date_d2 = ($('#date_d2').val()) ? $('#date_d2').val() : 'null';
		
		var link = '/admin/log?f=1';
		
		// if (sid) { link += '&sid='+sid; }
		if (date_d1) { link += '&date_d1='+date_d1+'&date_d2='+date_d2; }
		
		<?php if (getVar('orderby', 'get')) { ?>link += '&orderby=<?php echo getVar('orderby', 'get'); ?>';<?php } ?>
		<?php if (getVar('sort', 'get')) { ?>link += '&sort=<?php echo getVar('sort', 'get'); ?>';<?php } ?>
		
		location.assign(link);
	}
	
</script>