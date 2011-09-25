<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$title;?> - Application name</title>
	<?= Asset::css('reset.css'); ?>
	<?= Asset::css('layout.css'); ?>
</head>
<body>
	<div id="wrapper">
		<h1><?=$title;?></h1>
		
		<p class="intro"><?=$heading;?></p>
		
		<?php if (isset($errors) && ! empty($errors)): ?>
		<ul class="form_errors">
		<?php foreach ($errors as $error): ?>
			<li><?=$error;?></li>
		<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		
		<?=$content;?>
		
		<p class="footer">
			<a href="http://fuelphp.com">Fuel PHP</a> is released under the MIT license.<br />Page rendered in {exec_time}s using {mem_usage}mb of memory.
		</p>
	</div>
</body>
</html>