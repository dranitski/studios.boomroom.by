<?php
	
	defined('access') or die ('no access');
	
	$managers = loadObjectList("SELECT * FROM `users` WHERE `type` = 0");
	
	$ch1_date_start	= '01.06.2015';
	$ch1_date_end	= date('d.m.Y');
	$ch1_plan		= 120000000;
	$ch1_mid		= ($user->type == 0) ? $user->id : 0;
	$ch1_group		= 2;
	$ch1_ord		= 1;
	$ch1_sort		= 1;
?>

<div class="block" id="ch1" data-start="<?php echo $ch1_date_start; ?>" data-end="<?php echo $ch1_date_end; ?>"  data-plan="<?php echo $ch1_plan; ?>" data-mid="<?php echo $ch1_mid; ?>" data-group="<?php echo $ch1_group; ?>" data-ord="<?php echo $ch1_ord; ?>" data-sort="<?php echo $ch1_sort; ?>">
	
	<div class="bHead">
		<div class="bName">ОПЛАЧЕННЫЕ СЧЕТА</div>
	</div>
	
	<div class="filter">
		<div><span>Дата</span> 
		с <input class="date" type="text" value="<?php echo $ch1_date_start; ?>" readonly data-id="1" data-type="start"/>
		по <input class="date" type="text" value="<?php echo $ch1_date_end; ?>" readonly data-id="1" data-type="end"/>
		</div>
		<div>
		<input type="button" class="apply" value="применить" onclick="loadChart(1);"/>
		</div>
		<?php if ($user->type != 0) { ?>
		<div style="margin-left: 30px;">
			<select onchange="setMid(1, this);">
				<option value="0">все менеджеры</option>
				<?php foreach ($managers as $manager) { ?>
					<option value="<?php echo $manager->id; ?>"<?php echo ($manager->id == $ch1_mid) ? " selected" : ""; ?>><?php echo $manager->name; ?></option>
				<?php } ?>
			</select>
		</div>
		<?php } ?>
		<?php if ($user->type != 0) { ?>
		<div style="margin-left: 30px;">
			План: <input type="text" value="<?php echo $ch1_plan; ?>" onchange="setPlan(1, this);" onkeyup="setPlan(1, this);"/>
			<input type="button" class="apply" value="пересчитать" onclick="loadChart(1);"/>
		</div>
		<?php } ?>
		<div style="float: right; margin-right: 10px;">
			вывод по&nbsp;&nbsp;
			<select onchange="setGroup(1, this);">
				<option value="1"<?php echo ($ch1_group == 1) ? " selected" : ""; ?>>месяцам</option>
				<option value="2"<?php echo ($ch1_group == 2) ? " selected" : ""; ?>>неделям</option>
				<option value="3"<?php echo ($ch1_group == 3) ? " selected" : ""; ?>>дням</option>
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
	
	function setGroup(id, e) {
		
		$('#ch'+id).attr('data-group', $(e).val());
		
		loadChart(id);
	}
	
	function setMid(id, e) {
		
		$('#ch'+id).attr('data-mid', $(e).val());
		
		loadChart(id);
	}
	
	function setPlan(id, e) {
		
		$('#ch'+id).attr('data-plan', $(e).val());
	}
	
	function setOrd(id, ord) {
		
		var sort = ($('#ch'+id).attr('data-sort') == 1) ? 2 : 1;
		
		$('#ch'+id).attr('data-ord', ord);
		$('#ch'+id).attr('data-sort', sort);
		
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
				'type':		'invoices_paid',
				'id':			id,
				'start':		$('#ch'+id).attr('data-start'),
				'end':		$('#ch'+id).attr('data-end'),
				'plan':		$('#ch'+id).attr('data-plan'),
				'mid':		$('#ch'+id).attr('data-mid'),
				'group':		$('#ch'+id).attr('data-group'),
				'ord':		$('#ch'+id).attr('data-ord'),
				'sort':		$('#ch'+id).attr('data-sort')
				
			},
			dataType: 'html',
			success: function(data){
			    $('#ch'+id+' .load').html(data);
			}
		});
	}
	
	loadChart(1);
	
</script>