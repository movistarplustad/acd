<?php
$colorvalue = $fieldValue['rgb'] . sprintf('%02s', dechex($fieldValue['alfa']));
?><input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_1_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<input type="color" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][rgb]" value="<?=htmlspecialchars($fieldValue['rgb'])?>" id="field_<?=htmlspecialchars($id.'_1_'.$idParent)?>" class="field colorrgba componentrgb"/>
<input type="range" min="0" max="255" step="1" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][alfa]" value="<?=htmlspecialchars($fieldValue['alfa'])?>" id="field_<?=htmlspecialchars($id.'_2_'.$idParent)?>" class="field colorrgba componentalfa"/>
<label class="action empty"> <input type="checkbox" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][empty]" value="1" title="Clear value"/></label>
<span class="field colorvalue">rgba: <?=$colorvalue?></span>
