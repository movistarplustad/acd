<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>,
 <abbr title="latitude">lat.</abbr>: <input type="number" step="0.0000001" name="field[<?=$id?>][value][latitude]" value="<?=htmlspecialchars($fieldValue['latitude'])?>" id="field_<?=$id?>"/>
 <abbr title="longitude">long.</abbr>: <input type="number" step="0.0000001" name="field[<?=$id?>][value][longitude]" value="<?=htmlspecialchars($fieldValue['longitude'])?>" id="field_<?=$id?>_logitude"/>
