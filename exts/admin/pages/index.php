<?php
	
	defined('access') or die ('no access');
	
?>

<?php

if(getVar('act', 'get') == 'add' or getInt('edit', 'get')) {
	
	$id	 = getInt('edit', 'get');
	
	if ($id) {
		
		$sql = "SELECT p.*, l.`url` AS `link`" .
			" FROM `pages` AS p".
			" LEFT JOIN `links` AS l ON l.`id` = p.`lid`".
			" WHERE p.`id` = {$id}";
			
		$ePage	= loadObject($sql);
		
		$eTitle = "Редактирование страницы";
		
	} else {
		
		$eTitle = "Новая страница";
	}
	
	switch (getInt('type', 'get')) {
		
		case 0: include "type_0.php"; break;
		case 1: include "type_1.php"; break;
		// case 2: include "type_2.php"; break;
		// case 3: include "type_3.php"; break;
		
		default:
		
			include "type_0.php";
		
	}
}

?>

<?php include "list.php"; ?>

<script type="text/javascript">
	
	function closeForm(){
		
		window.location.assign('/admin/pages');
		
	}
	
	function saveForm() {
		
		tinyMCE.triggerSave();
		
		var form = "#page";
		
		var state = $(form+' input[name=state]'); state.is(':checked') ? state.val('1') : state.val('0');
		
		$(form).submit();
		
	}
	
	function applyPage() {
		
		$('#page input[name=act]').val(1);
		saveForm();
		
	}
	
	function savePage() {
		
		$('#page input[name=act]').val(2);
		saveForm();
		
	}
	
	function setType() {
		
		window.location.assign('/admin/pages?act=add&type='+$('#type').val());
	}
	
	function deleteData(id) {
	
		if (confirm("Вы подтверждаете удаление?")) {
			
			$.ajax({
			
				url:		'/action.php',
				type:	'post',
				data: {
					
					'ext':		'admin',
					'task':		'page',
					'subtask':	'delete',
					'id':			id
					
				},
				dataType: 'script'
			});
			
		}
		
	}
	
</script>
<script type="text/javascript">
	tinymce.init({
	    
		language: "ru",
		selector: ".editor",
		theme: "modern",
		plugins: [
			"autosave advlist autolink lists link image charmap print preview anchor",
			"searchreplace visualblocks code fullscreen",
			"insertdatetime media table contextmenu paste",
			"textcolor"
		],
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | forecolor backcolor | bullist numlist outdent indent | link unlink image media",
		height: 300,
		image_advtab: true,
		relative_urls: false,
		force_p_newlines: false,
		forced_root_block: ''
		
	});
</script>