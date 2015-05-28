<main id="manageStructure">
	<h2>Edit structure <span class="structure_name"><?=htmlspecialchars($structureName)?></span></h2>
	<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<form action="do_process_structure.php" method="post">
		<fieldset class="common">
			<legend>Common</legend>
			<div>
				<label for="idStructure">Id:&nbsp;</label><input type="text" name="id" id="idStructure" value="<?=htmlspecialchars($structureId)?>" readonly="readonly"/>
			</div>
			<div>
				<label for="name">Name:&nbsp;</label><input type="text" name="name" id="name" value="<?=htmlspecialchars($structureName)?>" required="required"/>
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
				<label for="storage">Storage type:&nbsp;</label><select name="storage" id="storage" required="required"><?=$options?></select>
			</div>
		</fieldset>
		<div>
			<?php
			$structure_fields = '';
			$n = 0;
			foreach ($fields as $field) {
				$lockId = ($field->getId() === '' || $field->getId() === $field::EMPTY_ID) ? '' : ' readonly="readonly"';
				$structure_fields .= '<li class="field">
					<label for="field_'.$n.'_id">Id:&nbsp;</label>
					<input type="text" name="field['.$n.'][id]" id="field_'.$n.'_name" value="'.htmlspecialchars($field->getId()).'"'.$lockId.' required="required" placeholder="Enter the field id"/>
					<label for="field_'.$n.'_type">'.htmlspecialchars($fieldTypes[$field->getType()]).'</label>
					<div>
						<label for="field_'.$n.'_name">Description:&nbsp;</label>
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
		<div class="actions">
			<input type="hidden" name="a" value="<?=$actionValue?>"/>
			<input type="submit" name="accion" value="save" class="button publish"/>
		</div>
	</form>


	<article class="dev_sample">
		<h1>Examples for developer</h1>		
		<pre>
			// Can use contentLoader->loadContents(...) to return ContentsDo collection with the 0:n ContentDo matching elements
			// Can use contentLoader->loadContent(...) to return ContentDo object,  discret content ie. number in count query or null if matching is not done

			// Sample #1 by id, only content without related content
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$plainContent = $contentLoader->loadContent('id', $idContent);

			// Sample #2 by id, with n related levels
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$contentWithLevelOfDepth = $contentLoader->loadContent('id-deep', ['id' => $idContent, 'depth' => 5]);

			// Sample #3 by any match tag, with n related levels
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$contentByTagWithLevelOfDepth = $contentLoader->loadContent('tag-one-deep', ['tags' => ['portadacine', 'otros'], 'depth' => 2]);

			// Sample #4 by any match tag, with n related levels and only content in date
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$contentByTagWithLevelOfDepth = $contentLoader->loadContent('tag-one-deep', ['tags' => ['portadacine', 'otros'], 'depth' => 2, 'validity-date' => time()]);

			// Sample #5 by alias-id, with n related leveles and only content in date
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$contents = $contentLoader->loadContent('alias-id-deep', ['id' => $aliasIdContent, 'depth' => 5, 'validity-date' => time()]);

			// And get values of fields
<?php
				foreach ($fields as $field) {
?>
			echo $plainContent->getFields()->getValue('<?=htmlspecialchars($field->getId())?>');
<?php
				}
?>

			// Manage pure data
			var_dump($contentWithLevelOfDepth->tokenizeData());
		</pre>
	</article>
</main>