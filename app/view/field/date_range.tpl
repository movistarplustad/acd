<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<input type="date" name="field[<?=$id?>][value][start]" value="<?=htmlspecialchars($fieldValue)?>" id="field_<?=$id?>"/>
-
<input type="date" name="field[<?=$id?>][value][end]" value="<?=htmlspecialchars($fieldValue)?>" id="field_<?=$id?>_end"/>