<!-- TODO -->
<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<ul>
<?php 
//	$id ='TODO';
	if ($fieldRef) { // TODO hacer que en el futuro fieldRef sea un array vacío
		foreach ($fieldRef as $fieldRefItem) {
			$idItem = $id.'_'.$fieldRefItem['id_structure'];
?>
		<li>
			<input type="text" name="field[<?=$id?>][value][]" value="<?=htmlspecialchars($fieldRefItem['ref'])?>" readonly="readonly"/>
			<input type="hidden" name="field[<?=$id?>][type][]" value="<?=htmlspecialchars($fieldRefItem['id_structure'])?>" />
			<a href="content.php?a=edit&amp;id=<?=htmlspecialchars($fieldRefItem['ref'])?>&amp;idt=<?=htmlspecialchars($fieldRefItem['id_structure'])?>&amp;idp=<?=htmlspecialchars($idParent)?>&amp;idtp=<?=htmlspecialchars($idStructureParent)?>">Edit</a>
		</li>
<?php
		}
	}
?>
</ul>