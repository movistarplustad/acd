<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_1_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>,
 <abbr title="latitude">lat.</abbr>: <input type="number" step="0.0000001" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][latitude]" value="<?=htmlspecialchars($fieldValue['latitude'])?>" id="field_<?=htmlspecialchars($id.'_1_'.$idParent)?>"/>
 <abbr title="longitude">long.</abbr>: <input type="number" step="0.0000001" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][longitude]" value="<?=htmlspecialchars($fieldValue['longitude'])?>" id="field_<?=htmlspecialchars($id.'_2_'.$idParent)?>_logitude"/>
