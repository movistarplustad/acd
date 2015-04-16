<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<?php
if ($idParent) {
	if ($fieldValue['value']) {
?>
<input type="hidden" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue['value'])?>"/>
<input type="hidden" name="field[<?=$id?>][original_name]" value="<?=htmlspecialchars($fieldValue['original_name'])?>"/>
<input type="hidden" name="field[<?=$id?>][type]" value="<?=htmlspecialchars($fieldValue['type'])?>"/>
<input type="hidden" name="field[<?=$id?>][size]" value="<?=htmlspecialchars($fieldValue['size'])?>"/>

<div>
	<img src="file_preview.php?id=<?=urlencode($fieldValue['value'])?>" alt="Image preview" class="field preview"/>
	Alt <input type="text" name="field[<?=$id?>][alt]" value="<?=htmlspecialchars($fieldValue['alt'])?>"/>
</div>
<input type="file" name="field[<?=$id?>][file]" class="field file" id="field_<?=$id?>"/>
<input type="checkbox" name="field[<?=$id?>][delete]" value="1" id="field_<?=$id?>_delete"/> <label for="field_<?=$id?>_delete">Delete:</label>
<?php
	}
	else {
?>
<div>
	Alt <input type="text" name="field[<?=$id?>][alt]" value=""/>
</div>
<input type="file" name="field[<?=$id?>][file]" class="field file" id="field_<?=$id?>"/>
<?php
	}
}
?>