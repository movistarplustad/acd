<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<input type="color" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue)?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>" class="field colorrgb componentrgb"/>
<label class="action empty"> <input type="checkbox" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][empty]" value="1" title="Clear value"/></label>
<span class="field colorvalue">rgb: <?=$fieldValue?></span>
