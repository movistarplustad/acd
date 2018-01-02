<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?><?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<div class="textarea-wrap">
	<textarea name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value]" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>" class="field richtext"><?=htmlspecialchars($fieldValue)?></textarea>
</div>
