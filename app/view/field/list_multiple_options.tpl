<?php
	// Parche temporal
	if (!is_array($fieldValue)) {
		$fieldValue = [];
	}
?>
<label for="<?=htmlspecialchars($fieldId)?>" class="for-tag"><?=htmlspecialchars($fieldName)?>:&nbsp;</label><select multiple="multiple" name="<?=htmlspecialchars($fieldId)?>" id="<?=htmlspecialchars($fieldId)?>" class="field select">
	<?php
		foreach ($fieldOptions->getItems() as $key => $value) {
			$selected = in_array($key, $fieldValue) ? ' selected="selected"' : '';
	?>
			<option value="<?=htmlspecialchars($key)?>"<?=$selected?>><?=htmlspecialchars($value)?></option>
	<?php
		}
	?>
</select>