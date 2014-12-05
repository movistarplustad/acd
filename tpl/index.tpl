<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<link rel="stylesheet" type="text/css" href="style/main.css"/>
	<link href="style/icon_16.png" rel="icon" />
	<link href="style/icon_128.png" sizes="128x128" rel="icon" />
	<title>Administración estructuras</title>
</head>
<body>
<h1>ACD</h1>
<h2>Administración estructuras</h2>
<a href="do_logout.php" class="logout" rel="nofollow">Logout</a>
<p class="result"><?=$resultDesc?></p>
<ol id="structures_list">
<?php
	foreach ($estructuras as $id) {
		$estructura = $structures->get($id);
		//echo "";
?>
	<li class="structure">
		<form action="do_process_structure.php" method="post">
			<input type="hidden" name="id" value="<?=htmlspecialchars($estructura->getId())?>"/>
			<?=$estructura->getName()?>
			<span class="tools"><input type="submit" name="a" value="edit" class="button edit"/>,  <input type="submit" name="a" value="clone" class="button clone"/>, <input type="submit" name="a" value="delete" class="button delete"/></span>
		</form>
	</li>
<?php
	}
?>
</ol>
<div id="new_structure"><a href="?a=new" title="new structure" class="button new">New structure</a></div>
<!--
<footer>
	<a href="_test/">Tests</a>
</footer>
-->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>