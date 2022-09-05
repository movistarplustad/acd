<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<link rel="stylesheet" type="text/css" href="js/jquery-ui/jquery-ui.min.css"/>
	<link rel="stylesheet" type="text/css" href="js/datetimepicker/jquery.datetimepicker.css"/>
	<link rel="stylesheet" type="text/css" href="js/tag-it/jquery.tagit.css"/>
	<link rel="stylesheet" type="text/css" href="js/selectivity/selectivity-full.min.css"/>
	<link rel="stylesheet" type="text/css" href="style/main.css"/>
	<link href="style/icon_16.png" rel="icon" />
	<link href="style/icon_128.png" sizes="128x128" rel="icon" />
	<title><?=htmlspecialchars($headTitle)?></title>
</head>
<body class="<?=$bodyClass?>">
<header>
	<?=$headerMenu?>
	<h1>ACD</h1>
</header>
<div id="wrapper">
	<?php
	if (isset($resultCode)) {
	?>
		<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<?php
	}
	?>
	<?=$content?>
	<?=$tools?>
</div>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/datetimepicker/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/tag-it/tag-it.min.js"></script>
<script type="text/javascript" src="js/alias-id.js"></script>
<script type="text/javascript" src="js/color.js"></script>
<script type="text/javascript" src="js/selectivity/selectivity-full.min.js"></script>
<script type="text/javascript" src="js/jets.min.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>
