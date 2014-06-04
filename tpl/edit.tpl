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
<h2>Administración <span id="structure_name"><?=htmlspecialchars($titleName)?></span></h2>
<a href="index.php">Volver</a>
<p class="result"><?=$resultDesc?></p>
<form action="do_process_structure.php" method="post">
	<input type="hidden" name="id" value="<?=htmlspecialchars($id)?>"/>
	<div>
		<label for="name">Nombre</label>: <input type="text" name="name" id="name" value="<?=htmlspecialchars($name)?>"/>
	</div>
	<div>
		<?php
		$options = '';
		foreach (conf::$STORAGE_TYPES as $key => $value) {
			$selected = $storage === $key ? ' selected="selected"' : '';
			$options .= '<option value="'.htmlspecialchars($key).'"'.$selected.'>'.htmlspecialchars($value).'</option>';
			
		}
		?>
		<label for="storage">Tipo Almacenamiento</label>: <select name="storage" id="storage"><?=$options?></select>
	</div>
	<div>
		<?php
		$field_types = '';
		foreach (conf::$FIELD_TYPES as $key => $value) {
			$field_types .= '<li>
				<input type="radio" name="new_field" value="'.htmlspecialchars($key).'" id="field_'.$key.'"/>
				<label for="field_'.$key.'">'.htmlspecialchars($value).'</label>';
		}
		?>
		<fieldset>
			<legend>Tipos de campo</legend>
			<ul><?=$field_types?></ul>
			<div><input type="submit" name="accion" value="Añadir"/></div>
		</fieldset>
	</div>
	<input type="hidden" name="a" value="save"/>
	<input type="submit" name="accion" value="guardar"/>
</form>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>