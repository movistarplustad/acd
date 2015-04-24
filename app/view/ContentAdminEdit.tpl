<main id="manageStructure">
	<h2>Edit structure <span class="structure_name"><?=htmlspecialchars($structureName)?></span></h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="do_process_structure.php" method="post">
		<div>
			<label for="idStructure">Id</label>: <input type="text" name="id" id="idStructure" value="<?=htmlspecialchars($structureId)?>" readonly="readonly"/>
		</div>
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
				$lockId = ($field->getId() === '' || $field->getId() === $field::EMPTY_ID) ? '' : ' readonly="readonly"';
				$structure_fields .= '<li class="field">
					<label for="field_'.$n.'_id">Id</label>:
					<input type="text" name="field['.$n.'][id]" id="field_'.$n.'_name" value="'.htmlspecialchars($field->getId()).'"'.$lockId.' required="required" placeholder="Enter the field id"/>
					<label for="field_'.$n.'_type">'.htmlspecialchars($fieldTypes[$field->getType()]).'</label>
					<div>
						<label for="field_'.$n.'_name">Description</label>:
						<input type="text" name="field['.$n.'][name]" id="field_'.$n.'_name" value="'.htmlspecialchars($field->getName()).'" id="field_'.$n.'"/>
						<input type="hidden" name="field['.$n.'][type]" value="'.htmlspecialchars($field->getType()).'"/>
						
					</div>
					<div class="delete">
						<label for="delete_field_'.$n.'">Delete</label>
						<input type="checkbox" name="field['.$n.'][delete]" value="1" id="delete_field_'.$n.'"/>
					</div>
					</li>';
				$n++;
			}
			?>
			<fieldset class="fields">
				<legend>Fields</legend>
				<ul class="items"><?=$structure_fields?></ul>
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
			// Sample #1 by id, only content without related content
			$plainContent = $contentLoader->loadContents('id', $idContent);
			// Sample #2 by id, with n related levels
			$contentWithLevelOfDepth = $contentLoader->loadContents('id-deep', ['id' => $idContent, 'depth' => 5]);
			// Sample #3 by any match tag, with n related levels
			$contentByTagWithLevelOfDepth = $contentLoader->loadContents('tag-one-deep', ['tags' => ['portadacine', 'otros'], 'depth' => 2]);
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