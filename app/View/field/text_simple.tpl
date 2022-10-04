<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<input type="text" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue)?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>" class="field text"/>
