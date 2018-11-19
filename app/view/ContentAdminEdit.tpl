<main id="manageStructure">
	<h2>Edit structure <em class="structure_name"><?=htmlspecialchars($structureName)?></em></h2>
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
				// For enumerated field, select with sources
				$selectEnumeratedSource = '';
				if ($field->getType() === $field::TYPE_LIST_MULTIPLE || $field->getType() === $field::TYPE_LIST_OPTIONS) {
					$enumeratedSelected = $field->getOptions()->getId();
					$optionEnumerateSource = '';
					foreach ($enumeratedList as $item) {
						$key = $item['id'];
						$value = $item['id'];
						$selected = $key === $enumeratedSelected ? ' selected="selected"' : '';
						$optionEnumerateSource .= '<option value="'.htmlspecialchars($key).'"'.$selected.'>'.htmlspecialchars($value).'</option>';
					}
					$selectEnumeratedSource = '<div><label for="field_'.$n.'_source">Source:&nbsp;</label> <select  name="field['.$n.'][source]" id="field_'.$n.'_source" class="source select">'.$optionEnumerateSource.'</select></div>';
				}
				// Related contents can restrict structures asociated
				$restrictedStructures = '';
				if ($field->getType() === $field::TYPE_CONTENT || $field->getType() === $field::TYPE_COLLECTION) {

					$optionsRestristedStructures = '';
					foreach ($structures as $structure) {
						$key = $structure->getId();
						$value = $structure->getName();
						$selected = $field->getRestrictedStructures()->hasKey($key)
							? ' selected="selected"'
							: '';
						$optionsRestristedStructures .= '<option value="'.htmlspecialchars($key).'"'.$selected.'>'.htmlspecialchars($value).'</option>';
					}
					$restrictedStructures = '
						<div class="restricted-structures-wrap">
							<label for="field_'.$n.'_restrictedStructures">Restrict to:&nbsp;</label>
							<select name="field['.$n.'][restrictedStructures]" id="field_'.$n.'_restrictedStructures" multiple="multiple" class="field select">'.
										$optionsRestristedStructures.'
									</select>
						</div>';
				}

				$structure_fields .= '<li class="field">
					<label for="field_'.$n.'_id">Id:&nbsp;</label>
					<input type="text" name="field['.$n.'][id]" id="field_'.$n.'_id" value="'.htmlspecialchars($field->getId()).'"'.$lockId.' required="required" placeholder="Enter the field id"/>
					<label for="field_'.$n.'_type">'.htmlspecialchars($fieldTypes[$field->getType()]).'</label>
					<div>
						<label for="field_'.$n.'_name">Description:&nbsp;</label>
						<input type="text" name="field['.$n.'][name]" id="field_'.$n.'_name" value="'.htmlspecialchars($field->getName()).'" id="field_'.$n.'"/>
						<input type="hidden" name="field['.$n.'][type]" value="'.htmlspecialchars($field->getType()).'"/>
					</div>
					'.$restrictedStructures
					.$selectEnumeratedSource.'
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
		<div class="fields">
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
		<div class="wrap-actions">
			<div class="actions">
				<input type="hidden" name="a" value="<?=$actionValue?>"/>
				<input type="submit" name="accion" value="save" class="button publish"/>
			</div>
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
			$contentsByTagWithLevelOfDepth = $contentLoader->loadContent('tag-deep', ['tags' => ['portadacine', 'otros'], 'depth' => 2]);

			// Sample #4 only one content by any match tag, with n related levels
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$contentByTagWithLevelOfDepth = $contentLoader->loadContent('tag-one-deep', ['tags' => ['portadacine', 'otros'], 'depth' => 2]);

			// Sample #5 by any match tag, with n related levels and only content in date
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$contentByTagWithLevelOfDepth = $contentLoader->loadContent('tag-one-deep', ['tags' => ['portadacine', 'otros'], 'depth' => 2, 'validity-date' => time()]);

			// Sample #6 by alias-id, with n related leveles and only content in date
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$content = $contentLoader->loadContent('alias-id-deep', ['id' => $aliasIdContent, 'depth' => 5, 'validity-date' => time()]);

			// Sample #7 by id, with n related levels and only content in date and profile JAZZ
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>');
			$content = $contentLoader->loadContent('id-deep', ['id' => $idContent, 'depth' => 5, 'validity-date' => time(), 'profile' => 'JAZZ']);

			// Sample #8 difuse search by alias-id, e.g. one/two/three match by one/two/three &amp; one/two &amp; one
			$aliasId='one/two/three';
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>'); // Optional
			$matchContents = $contentLoader->loadContent('difuse-alias-id', ['id' => $aliasId]);

			// Sample #9 difuse search by alias-id, e.g. one/two/three match by one/two/three &amp; one/two &amp; one and only content in date
			$aliasId='one/two/three';
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId('<?=htmlspecialchars($structureId)?>'); // Optional
			$matchContents = $contentLoader->loadContent('difuse-alias-id', ['id' => $aliasId, 'validity-date' => time()]);

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
