<?php

	defined('access') or die ('no access');
	
	include_once path."/blocks/slider/data.php";
	
if ($state) {
		
	if (count($params->slides)) {
		
		// if(strpos($_SERVER['REQUEST_URI'], "music_school/education")) { $class = " h300"; }
		?>
		<?php
		if (count($params->slides) == 1) {
		?>
		<div id="slider">
			<div id="slider-sh"></div>
			<div id="slide">
				<div class="slide" style="background: url('/images/<?php echo $params->slides[0]->image; ?>') center center no-repeat;"><?php if($params->slides[0]->video) {?><a class="fancybox-media play" href="http://www.youtube.com/watch?v=<?php echo $params->slides[0]->video; ?>"></a><?php } ?></div>
			</div>
		</div>
		<?php
		}
		else {
		?>
		<div id="slider">
			<div id="slider-sh"></div>
			<div id="slide">
		<?php
			foreach ($params->slides as $slide) {
			?>
			<div class="slide" style="background: url('/images/<?php echo $slide->image; ?>') center center no-repeat;"><?php if($params->slides[0]->video) {?><a class="fancybox-media play" href="http://www.youtube.com/watch?v=<?php echo $slide->video; ?>"></a><?php } ?></div>
			<?php
			}
		?>
			</div>
		</div>
		<?php
		}
		?>
		<link type="text/css" href="/tmpl/site/css/jquery.fancybox.css?v=2.1.5" rel="stylesheet" media="screen" />
		<script type="text/javascript" src="/tmpl/site/js/jquery.fancybox.js?v=2.1.5"></script>
		<script type="text/javascript" src="/tmpl/site/js/jquery.fancybox-media.js?v=1.0.6"></script>
		<script type="text/javascript" src="/tmpl/site/js/jquery.slides.min.js"></script>
		<script type="text/javascript">
		$(function() {
			
			<?php if (count($params->slides) > 1) { ?>
			$('#slide').slidesjs({
				
				width: 1200,
				height: 380,
				play: {
					active: false,
					auto: true,
					interval: 6000,
					swap: true,
					pauseOnHover: true
				},
				navigation: {
					active: false
				},
				pagination: {
					active: false
				}	
			});
			<?php } ?>
			
			$('.fancybox-media').attr('rel', 'media-gallery').fancybox({
				
				openEffect : 'none',
				closeEffect : 'none',
				prevEffect : 'none',
				nextEffect : 'none',
				arrows : false,
				helpers : {
					
					media : {},
					buttons : {}
				}
			});
		});
		</script>
		<?php
	}
	?>
	<?
}
?>
