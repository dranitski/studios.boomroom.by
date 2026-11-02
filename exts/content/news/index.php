<?php
	
	defined('access') or die ('no access');
	
?>
<link rel="stylesheet" href="/tmpl/site/css/imageflow.css" type="text/css" />
<script type="text/javascript" src="/tmpl/site/js/imageflow.js"></script>


<div id="events">
	<div class="desc" style="margin-bottom: 30px;"><h1>Последние события</h1></div>
	<div id="myImageFlow" class="imageflow">
	<?php
		if (cnt($news)) {
		foreach ($news as $new) {
			
			$new->date = date('d.m.Y в H:i', strtotime($new->addDate));
	?>
		<img src="<?php echo getFirstSrc(stripslashes($new->text)); ?>" albumsrc="<?php echo $new->link; ?>" longdesc="<?php echo $new->name; ?>" width="400" height="300" alt="<?php echo $new->name; ?>" /><a href="<?php echo $new->link; ?>"></a>
	<?php
		}
		}
		else {
	?>Нет новостей<?php
		}
	?>

	</div>
	
	<div class="list">
	<?php
		if (cnt($news)) {
		foreach ($news as $new) {
			
			$new->date = date('d.m.Y в H:i', strtotime($new->addDate));
	?>
		<div class="event">
			<a href="/<?php echo $new->link; ?>"><img src="<?php echo getFirstSrc(stripslashes($new->text)); ?>"/></a>
			<a href="/<?php echo $new->link; ?>"><?php echo $new->name; ?></a><br/>
			<span><?php echo $new->date; ?></span><br>
			<?php echo getIntroText(strip_tags(stripslashes($new->text)), 200); ?>
			<div class="clear"></div>
		</div>
	<?php
		}
		}
		else {
	?>Нет новостей<?php
		}
	?>
	</div>
</div>

<!--
<div id="events">

	<div class="desc" style="margin-bottom: 30px;"><h1>Последние события</h1></div>

	<div class="main-new">
		<a href="<?php echo $news[0]->link; ?>">
			<div><img src="<?php echo getFirstSrc(stripslashes($news[0]->text)); ?>" /></div>
			<h3><?php echo $news[0]->name; ?></h3>
		</a>
	</div>
	<div class="main-new">
		<a href="<?php echo $news[1]->link; ?>">
			<div><img src="<?php echo getFirstSrc(stripslashes($news[1]->text)); ?>" /></div>
			<h3><?php echo $news[1]->name; ?></h3>
		</a>
	</div>
	
	<div class="clear" style="height: 30px;"></div>

<?php
	if (cnt($news)) {
	foreach ($news as $new) {
		
		$new->date = date('d.m.Y в H:i', strtotime($new->addDate));
?>
	<div class="event">
	
		<a href="<?php echo $new->link; ?>"><?php echo $new->name; ?></a><br/>
		<span><?php echo $new->date; ?></span><br>
		<?php echo getIntroText(strip_tags(stripslashes($new->text)), 200); ?>
	</div>
	<hr/>
<?php
	}
	}
	else {
?>Нет новостей<?php
	}
?>
</div>
-->