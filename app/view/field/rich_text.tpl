<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<textarea name="field[<?=$id?>][value]" id="field_<?=$id?>" class="field richtext"><?=htmlspecialchars($fieldValue)?></textarea>