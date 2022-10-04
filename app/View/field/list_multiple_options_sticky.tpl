<label for="<?=htmlspecialchars($fieldId.'_'.$idParent)?>" class="for-tag"><?=htmlspecialchars($fieldName)?>:&nbsp;</label>
<?php
	 $itemsInOut = $fieldOptions->detachItems($fieldValue);
?>
<select multiple="multiple" name="<?=htmlspecialchars($fieldId)?>[<?=htmlspecialchars($idParent)?>][]" id="<?=htmlspecialchars($fieldId.'_'.$idParent)?>" class="field select">
	<?php
		foreach ($itemsInOut['in'] as $key => $value) {
	?>
		<option value="<?=htmlspecialchars($key)?>" selected="selected"><?=htmlspecialchars($value)?></option>
	<?php
		}
		foreach ($itemsInOut['out'] as $key => $value) {
	?>
		<option value="<?=htmlspecialchars($key)?>"><?=htmlspecialchars($value)?></option>
	<?php

		}
	?>
</select>
