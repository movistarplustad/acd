<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
--SAVED FILENAME PREVIEW--
<input type="file" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue)?>" id="field_<?=$id?>" class="field file"/>