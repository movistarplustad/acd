<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<input type="date" name="field[<?=$id?>][value][start]" value="<?=htmlspecialchars($fieldValue[0])?>" id="field_<?=$id?>" class="range start"/>
-
<input type="date" name="field[<?=$id?>][value][end]" value="<?=htmlspecialchars($fieldValue[1])?>" id="field_<?=$id?>_end" class="range end"/>