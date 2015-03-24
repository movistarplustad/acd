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
			<a href="content.php?a=edit&amp;id=<?=htmlspecialchars($fieldRefItem['ref'])?>&amp;idt=<?=htmlspecialchars($fieldRefItem['id_structure'])?>&amp;idp=<?=htmlspecialchars($idParent)?>&amp;idtp=<?=htmlspecialchars($idStructureParent)?>" class="button edit">Edit</a>
			<a href="do_clear_field.php?a=clear&amp;idp=<?=urlencode($idParent)?>&amp;idtp=<?=urlencode($idStructureParent)?>&amp;f=<?=urlencode($fieldName)?>&amp;id=<?=htmlspecialchars($fieldRefItem['ref'])?>" class="button clear">Clear</a>
		</li>
<?php
		}
	}
	if ($idParent) {
?>
	<li>
		<a href="content_rel.php?a=select_type&amp;idp=<?=htmlspecialchars($idParent)?>&amp;idtp=<?=htmlspecialchars($idStructureParent)?>&amp;f=<?=htmlspecialchars($fieldName)?>" class="button search">Find new</a>
	</li>
<?php
	}
?>
</ul>