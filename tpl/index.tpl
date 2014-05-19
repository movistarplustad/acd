<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Administración estructuras</title>
</head>
<body>
<h1>ACD</h1>
<h2>Administración estructuras</h2>
<div><a href="?a=new">Nueva</a></div>
<ol id="structures_list">
<?php
	foreach ($estructuras as $id) {
		$estructura = $structures->get($id);
		//echo "";
 ?>
 	<li>
 		<form action="do_process_structure.php" method="post">
 			<input type="text" name="id" value="<?=htmlspecialchars($estructura->getId())?>"/>
	 		<?=$estructura->getName()?>
	 		<span class="tools"><input type="submit" name="a" value="edit"/>,  <input type="submit" name="a" value="clone"/>, <input type="submit" name="a" value="delete"/></span>
	 	</form>
 	</li>
<?php
	}
?>
</ol>
<footer>
	<a href="_test/">Tests</a>
</footer>
</body>
</html>