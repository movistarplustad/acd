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
<h2>Administración <span id="structure_name"><?=htmlentities($name)?></span></h2>
<a href="index.php">Volver</a>
<form action="do_save_structure.php" method="post">
	<input type="hidden" name="id" value="<?=htmlentities($id)?>"/>
	<div>
		<label for="name">Nombre</label>: <input type="text" name="name" id="name" value="<?=htmlentities($name)?>"/>
	</div>
	<div>
		<?php
		$options = '';
		foreach (conf::$STORAGE_TYPES as $key => $value) {
			$selected = $storage === $key ? ' selected="selected"' : '';
			$options .= '<option value="'.htmlentities($key).'"'.$selected.'>'.htmlentities($value).'</option>';
			
		}
		?>
		<label for="storage">Tipo Almacenamiento</label>: <select name="storage" id="storage"><?=$options?></select>
	</div>

	<input type="submit" name="accion" value="guardar"/>
</form>
</body>
</html>