<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<?php
if ($idParent) {
?>
--SAVED FILENAME PREVIEW--
<input type="text" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue['value'])?>" id="field_<?=$id?>"/>
<input type="file" name="field[<?=$id?>][file]" class="field file"/>
<label for="field_<?=$id?>_delete">Delete: </label><input type="checkbox" name="field[<?=$id?>][delete]" value="1" id="field_<?=$id?>_delete"/>
<?php
}
?>