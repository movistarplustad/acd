<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<input type="datetime" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][start]" value="<?=htmlspecialchars($fieldValue['start'])?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>" class="range start"/>
-
<input type="datetime" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][end]" value="<?=htmlspecialchars($fieldValue['end'])?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>_end" class="range end"/>
