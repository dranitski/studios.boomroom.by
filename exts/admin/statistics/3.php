<?php
	
	defined('access') or die ('no access');
	
	$managers = loadObjectList("SELECT * FROM `users` WHERE `type` = 0");
	
	$ch1_date_start	= '01.06.2015';
	$ch1_date_end	= date('d.m.Y');
	$ch1_mid		= 0;
?>

<div class="block" id="ch1" data-start="<?php echo $ch1_date_start; ?>" data-end="<?php echo $ch1_date_end; ?>" data-mid="<?php echo $ch1_mid; ?>">
	
	<div class="bHead">
		<div class="bName">СУММЫ СЧЕТОВ</div>
	</div>
	
	<div class="filter">
		<div><span>Дата</span> 
		с <input class="date" type="text" value="<?php echo $ch1_date_start; ?>" readonly data-id="1" data-type="start"/>
		по <input class="date" type="text" value="<?php echo $ch1_date_end; ?>" readonly data-id="1" data-type="end"/>
		</div>
		<div>
		<input type="button" class="apply" value="применить" onclick="loadChart(1);"/>
		</div>
		<div style="margin-left: 30px;">
			<select onchange="setMid(1, this);">
				<option value="0">все менеджеры</option>
				<?php foreach ($managers as $manager) { ?>
					<option value="<?php echo $manager->id; ?>"<?php echo ($manager->id == $ch1_mid) ? " selected" : ""; ?>><?php echo $manager->name; ?></option>
				<?php } ?>
			</select>
		</div>
		<p class="clear"></p>
	</div>
		
	<div class="load">
		
	</div>
	
</div>

<script type="text/javascript">

	$('.date').datepick($.extend({ 
		
		showAnim: 'slideDown',
		onSelect: function(){
			
			var thisDate = new Date($(this).datepick('getDate'));
			var date = thisDate.getDate()+'.'+(thisDate.getMonth() + 1)+'.'+thisDate.getFullYear();
			var id = $(this).attr('data-id');
			var type = $(this).attr('data-type');
			
			$('#ch'+id).attr('data-'+type, date);
		}
		
	
	}, $.datepick.regional['ru']));
	
	function setMid(id, e) {
		
		$('#ch'+id).attr('data-mid', $(e).val());
		
		loadChart(id);
	}
	
	function loadChart(id) {
		
		$.ajax({
			
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'user',
				'task':		'report',
				'subtask':	'chart',
				'type':		'invoices_sums',
				'id':			id,
				'start':		$('#ch'+id).attr('data-start'),
				'end':		$('#ch'+id).attr('data-end'),
				'mid':		$('#ch'+id).attr('data-mid')
				
			},
			dataType: 'html',
			success: function(data){
			    $('#ch'+id+' .load').html(data);
			}
		});
	}
	
	loadChart(1);
	
</script>