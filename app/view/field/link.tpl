<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<div>
	<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
	<input type="text" name="field[<?=$id?>][value][href]" value="<?=htmlspecialchars($fieldValue['href'])?>" id="field_<?=$id?>" class="field link_href"/>
</div>
<div>
	<label for="field_<?=$id?>_description">Description</label>
	<input type="text" name="field[<?=$id?>][value][description]" value="<?=htmlspecialchars($fieldValue['description'])?>" id="field_<?=$id?>_description" class="field link_description"/>
</div>