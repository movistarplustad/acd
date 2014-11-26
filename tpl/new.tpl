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
<h2>Administración <span id="structure_name"><?=htmlspecialchars($titleName)?></span></h2>
<a href="index.php" class="back">Volver</a>
<p class="result"><?=$resultDesc?></p>
<form action="do_process_structure.php" method="post">
	<div>
		<label for="id">Id</label>: <input type="text" name="id" id="id" value="<?=htmlspecialchars($id)?>"/>
	</div>
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
		$structure_fields = '';
		$idFields = $fields->keys();
		$n = 0;
		foreach ($idFields as $idField) {
			$field = $fields->get($idField);
			$structure_fields .= '<li>
				<input type="hidden" name="field['.$n.'][id]" value="'.htmlspecialchars($field->getId()).'"/>
				<input type="text" name="field['.$n.'][name]" value="'.htmlspecialchars($field->getName()).'" id="field_'.$n.'"/>
				<input type="hidden" name="field['.$n.'][type]" value="'.htmlspecialchars($field->getType()).'"/>
				<label for="field_'.$n.'">'.htmlspecialchars($field->getType()).'</label>
				<input type="checkbox" name="field['.$n.'][delete]" value="1" id="delete_field_'.$n.'"/>
				<label for="delete_field_'.$n.'">Delete</label>
				</li>';
			$n++;
		}
		?>
		<fieldset>
			<legend>Campos</legend>
			<ul><?=$structure_fields?></ul>
		</fieldset>
	</div>
	<input type="hidden" name="a" value="new"/>
	<input type="submit" name="accion" value="guardar" class="button publish" />
</form>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>