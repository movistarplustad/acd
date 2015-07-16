<?php

	$preview = '';
	if (isset($fieldValue) && isset($fieldValue['value']) && $fieldValue['value']) {
		$size = $fieldValue['size'];
		$unit = null;
		if( (!$unit && $size >= 1<<30) || $unit == "GB") {
			$humanFileSize =  number_format($size/(1<<30),2)."GB";
		}
		elseif( (!$unit && $size >= 1<<20) || $unit == "MB"){
			$humanFileSize =  number_format($size/(1<<20),2)."MB";
		}
		elseif( (!$unit && $size >= 1<<10) || $unit == "KB") {
			$humanFileSize =  number_format($size/(1<<10),2)."KB";
		}
		else {
			$humanFileSize = number_format($size)." bytes";
		}

		if (substr($fieldValue['type'], 0, 6) === 'image/') {
			$preview = '<img src="file_preview.php?id='.urlencode($fieldValue['value']).'" alt="Image preview, '.htmlspecialchars($fieldValue['original_name']).'" class="field preview"/> '.htmlspecialchars($fieldValue['original_name']).' <span class="size">'.$humanFileSize."</span>";
		}
		else {
			$preview = '<a href="file_preview.php?id='.urlencode($fieldValue['value']).'">'.htmlspecialchars($fieldValue['original_name']).'</a> <span class="size">'.$humanFileSize."</span>";
		}
		$preview = "<div class='file-preview'>$preview</div>";
	}
?>
<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<?php
if ($idParent) {
	if ($fieldValue['value']) {
?>
<input type="hidden" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue['value'])?>"/>
<input type="hidden" name="field[<?=$id?>][original_name]" value="<?=htmlspecialchars($fieldValue['original_name'])?>"/>
<input type="hidden" name="field[<?=$id?>][type]" value="<?=htmlspecialchars($fieldValue['type'])?>"/>
<input type="hidden" name="field[<?=$id?>][size]" value="<?=htmlspecialchars($fieldValue['size'])?>"/>

<div>
	<?=$preview?>
	Description / image alt <input type="text" name="field[<?=$id?>][alt]" value="<?=htmlspecialchars($fieldValue['alt'])?>"/>
</div>
<input type="file" name="field[<?=$id?>][file]" class="field file" id="field_<?=$id?>"/>
<input type="checkbox" name="field[<?=$id?>][delete]" value="1" id="field_<?=$id?>_delete"/> <label for="field_<?=$id?>_delete">Delete</label>
<?php
	}
	else {
?>
<div>
	Description / image alt <input type="text" name="field[<?=$id?>][alt]" value=""/>
</div>
<input type="file" name="field[<?=$id?>][file]" class="field file" id="field_<?=$id?>"/>
<?php
	}
}
?>