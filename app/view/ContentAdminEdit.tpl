<main>
	<h2>Edit structure <span class="structure_name"><?=htmlspecialchars($structureName)?></span></h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="do_process_structure.php" method="post">
		<input type="hidden" name="id" value="<?=htmlspecialchars($structureId)?>"/>
		<div>
			<label for="name">Name</label>: <input type="text" name="name" id="name" value="<?=htmlspecialchars($structureName)?>" required="required"/>
		</div>
		<div>
			<?php
			$options = '';
			foreach ($storageTypes as $key => $value) {
				$selected = $storage === $key ? ' selected="selected"' : '';
				$disabled = $value['disabled'] ? ' disabled="disabled"' : '';
				$options .= '<option value="'.htmlspecialchars($key).'"'.$selected.$disabled.'>'.htmlspecialchars($value['name']).'</option>';
				
			}
			?>
			<label for="storage">Storage type</label>: <select name="storage" id="storage"><?=$options?></select>
		</div>
		<div>
			<?php
			$structure_fields = '';
			$n = 0;
			foreach ($fields as $field) {
				$structure_fields .= '<li>
					<input type="hidden" name="field['.$n.'][id]" value="'.htmlspecialchars($field->getId()).'"/>
					<input type="text" name="field['.$n.'][name]" value="'.htmlspecialchars($field->getName()).'" id="field_'.$n.'"/>
					<input type="hidden" name="field['.$n.'][type]" value="'.htmlspecialchars($field->getType()).'"/>
					<label for="field_'.$n.'">'.htmlspecialchars($fieldTypes[$field->getType()]).'</label>
					<input type="checkbox" name="field['.$n.'][delete]" value="1" id="delete_field_'.$n.'"/>
					<label for="delete_field_'.$n.'">Delete</label>
					</li>';
				$n++;
			}
			?>
			<fieldset>
				<legend>Fields</legend>
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
				<legend>Add new field</legend>
				<ul><?=$field_types?></ul>
				<div><input type="submit" name="accion" value="add" class="button add"/></div>
			</fieldset>
		</div>
		<input type="hidden" name="a" value="<?=$actionValue?>"/>
		<input type="submit" name="accion" value="save" class="button publish"/>
	</form>


	<article class="dev_sample">
		<h1>Examples for developer</h1>		
		<pre>
			$contentLoader = new \ACD\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			// Sample #1
			$plainContent = $contentLoader->loadContents('id', $idContent);
			// Sample #2
			$contentWithLevelOfDepth = $contentLoader->loadContents('id-deep', ['id' => $idContent, 'depth' => 5]);
			// Value of field
<?php
				foreach ($fields as $field) {
?>
			echo $plainContent->getFields()->getValue('<?=htmlspecialchars($field->getName())?>');
<?php
				}
?>
			// Manage pure data
			var_dump($contentWithLevelOfDepth->tokenizeData());
		</pre>
	</article>
</main>