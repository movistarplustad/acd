<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<div>
	<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
	<input type="text" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][href]" value="<?=htmlspecialchars($fieldValue['href'])?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>" class="field link_href"/>
</div>
<div>
	<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>_description">Description</label>
	<input type="text" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][description]" value="<?=htmlspecialchars($fieldValue['description'])?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>_description" class="field link_description"/>
</div>
