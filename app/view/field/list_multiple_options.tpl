<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="<?=htmlspecialchars($fieldId)?>"><?=htmlspecialchars($fieldName)?></label>
<div class="select-wrap">
	<select multiple="multiple" name="field[<?=$id?>][value]" id="field_<?=$id?>" class="field select">
		<?php
			foreach ($fieldOptions->getItems() as $key => $value) {
				$selected = in_array($key, $fieldValue) ? ' selected="selected"' : '';
		?>
				<option value="<?=htmlspecialchars($key)?>"<?=$selected?>><?=htmlspecialchars($value)?></option>
		<?php
			}
		?>
	</select>

</div>