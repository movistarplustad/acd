<main>
	<h2>Edit structure <span class="structure_name"><?=htmlspecialchars($structureName)?></span></h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="do_process_structure.php" method="post">
		<input type="hidden" name="id" value="<?=htmlspecialchars($structureId)?>"/>
		<div>
			<label for="name">Nombre</label>: <input type="text" name="name" id="name" value="<?=htmlspecialchars($structureName)?>"/>
		</div>
		<div>
			<?php
			$options = '';
			foreach ($storageTypes as $key => $value) {
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
		<div>
			<?php
			$field_types = '';
			foreach ($fieldTypes as $key => $value) {
				$field_types .= '<li>
					<input type="radio" name="new_field" value="'.htmlspecialchars($key).'" id="field_'.$key.'"/>
					<label for="field_'.$key.'">'.htmlspecialchars($value).'</label>
					</li>';
			}
			?>
			<fieldset>
				<legend>Añadir campo</legend>
				<ul><?=$field_types?></ul>
				<div><input type="submit" name="accion" value="add" class="button add"/></div>
			</fieldset>
		</div>
		<input type="hidden" name="a" value="<?=$actionValue?>"/>
		<input type="submit" name="accion" value="save" class="button publish"/>
	</form>
</main>