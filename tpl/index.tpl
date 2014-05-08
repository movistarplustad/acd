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
 	<li><?=$estructura->getName()?>
 		<span class="tools"><a href="?a=edit&amp;id=<?=$id?>">editar</a>, <a href="?a=clone&amp;id=<?=$id?>">clonar</a>, <a href="?a=delete&amp;id=<?=$id?>">borrar</a></span>
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