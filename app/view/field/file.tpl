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

		$urlPreview = 'file_preview.php?id='.urlencode($fieldValue['value']).'&amp;n='.htmlspecialchars($fieldValue['original_name']);
		if (substr($fieldValue['type'], 0, 6) === 'image/') {
			$preview = '<img src="'.$urlPreview.'" alt="Image preview, '.htmlspecialchars($fieldValue['original_name']).'" class="field preview"/> '.htmlspecialchars($fieldValue['original_name']).' <span class="size">'.$humanFileSize."</span>";
		}
		else {
			$preview = '<a href="'.$urlPreview.'">'.htmlspecialchars($fieldValue['original_name']).'</a> <span class="size">'.$humanFileSize."</span>";
		}
		$preview = "<div class='file-preview'>$preview</div>";
	}
?>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<?php
if (!$bNew) {
	if ($fieldValue['value']) {
?>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value]" value="<?=htmlspecialchars($fieldValue['value'])?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][original_name]" value="<?=htmlspecialchars($fieldValue['original_name'])?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][type]" value="<?=htmlspecialchars($fieldValue['type'])?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][size]" value="<?=htmlspecialchars($fieldValue['size'])?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][width]" value="<?=htmlspecialchars($fieldValue['width'])?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][height]" value="<?=htmlspecialchars($fieldValue['height'])?>"/>

<div>
	<?=$preview?>
	Description / image alt <input type="text" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][alt]" value="<?=htmlspecialchars($fieldValue['alt'])?>"/>
</div>
<input type="file" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][file]" class="field file" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>"/>
<input type="checkbox" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][delete]" value="1" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>_delete"/> <label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>_delete">Delete</label>
<?php
	}
	else {
?>
<div>
	Description / image alt <input type="text" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][alt]" value=""/>
</div>
<input type="file" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][file]" class="field file" id="field_<?=$id?>"/>
<?php
	}
}
?>
