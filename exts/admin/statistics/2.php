<?php
	
	defined('access') or die ('no access');
	
	$studios = loadObjectList("SELECT * FROM `studios`");
	
	$ch1_date_start	= date('d.m.Y', strtotime("now - 1 month"));
	$ch1_date_end	= date('d.m.Y');
	$ch1_sid		= 0;
	$ch1_group		= 2;
	$ch1_ord		= 1;
	$ch1_sort		= 1;
?>

<div class="block" id="ch1" data-start="<?php echo $ch1_date_start; ?>" data-end="<?php echo $ch1_date_end; ?>" data-group="<?php echo $ch1_group; ?>" data-ord="<?php echo $ch1_ord; ?>" data-sort="<?php echo $ch1_sort; ?>" style="margin-top: 45px;">
	
	<div class="bHead">
		<div class="bName">СОБЫТИЯ</div>
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
			<select onchange="setSid(1, this);">
				<option value="0">все студии</option>
				<?php foreach ($studios as $studio) { ?>
					<option value="<?php echo $studio->id; ?>"<?php echo ($studio->id == $ch1_sid) ? " selected" : ""; ?>><?php echo $studio->name; ?></option>
				<?php } ?>
			</select>
		</div>
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
			var id = $(this).data('id');
			var type = $(this).data('type');
			
			$('#ch'+id).data(type, date);
		}
		
	
	}, $.datepick.regional['ru']));
	
	function setGroup(id, e) {
		
		$('#ch'+id).data('group', $(e).val());
		
		loadChart(id);
	}
	
	function setSid(id, e) {
		
		$('#ch'+id).data('sid', $(e).val());
		
		loadChart(id);
	}
	
	function setOrd(id, ord) {
		
		var sort = ($('#ch'+id).data('sort') == 1) ? 2 : 1;
		
		$('#ch'+id).data('ord', ord);
		$('#ch'+id).data('sort', sort);
		
		loadChart(id);
	}
	
	function loadChart(id) {
		
		$.ajax({
			
			url:		'/action.php',
			type:	'post',
			data: {
				
				'ext':		'admin',
				'task':		'statistics',
				'subtask':	'chart',
				'option':		'studios',
				'id':			id,
				'start':		$('#ch'+id).data('start'),
				'end':		$('#ch'+id).data('end'),
				'sid':		$('#ch'+id).data('sid'),
				'group':		$('#ch'+id).data('group'),
				'ord':		$('#ch'+id).data('ord'),
				'sort':		$('#ch'+id).data('sort')
				
			},
			dataType: 'html',
			success: function(data){
			    $('#ch'+id+' .load').html(data);
			}
		});
	}
	
	loadChart(1);
	
</script>