<?php
$colorValue = [];
$colorvalue[] = hexdec(substr($fieldValue, 1,2));
$colorvalue[] = hexdec(substr($fieldValue, 3,2));
$colorvalue[] = hexdec(substr($fieldValue, 5,2));
?><input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<input type="color" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue)?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>" class="field colorrgb componentrgb"/>
<span class="field colorvalue">rgb(<?=implode(',', $colorvalue)?>)</span>
