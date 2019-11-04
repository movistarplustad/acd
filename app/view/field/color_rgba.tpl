<?php
$colorValue = [];
$colorvalue[] = hexdec(substr($fieldValue['rgb'], 1,2));
$colorvalue[] = hexdec(substr($fieldValue['rgb'], 3,2));
$colorvalue[] = hexdec(substr($fieldValue['rgb'], 5,2));
//$colorvalue[] = hexdec(substr($fieldValue['rgb'], 7,2));
$colorvalue[] = $fieldValue['alfa'];
?><input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_1_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<input type="color" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][rgb]" value="<?=htmlspecialchars($fieldValue['rgb'])?>" id="field_<?=htmlspecialchars($id.'_1_'.$idParent)?>" class="field colorrgba componentrgb"/>
<input type="range" min="0" max="1" step="0.005" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][alfa]" value="<?=htmlspecialchars($fieldValue['alfa'])?>" id="field_<?=htmlspecialchars($id.'_2_'.$idParent)?>" class="field colorrgba componentalfa"/>
<span class="field colorvalue">rgba(<?=implode(',', $colorvalue)?>)<span>
