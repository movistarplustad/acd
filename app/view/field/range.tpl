<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<input type="range" min="0" max="100" step="1" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue)?>" id="field_<?=$id?>"/>