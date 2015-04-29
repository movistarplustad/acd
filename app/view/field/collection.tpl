<!-- TODO -->
<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<ul class="collection">
<?php 
//	$id ='TODO';
	if ($fieldRef) { // TODO hacer que en el futuro fieldRef sea un array vacío
		$pos = 0;
		foreach ($fieldRef as $fieldRefItem) {
			$idContent = $fieldRefItem->getId();
			$idStructure = $fieldRefItem->getIdStructure();
			$title = $fieldRefItem->getTitle();
			//$idItem = $id.'_'.$fieldRefItem['id_structure'];
?>
		<li>
			<input type="hidden" name="field[<?=$id?>][value][]" value="<?=htmlspecialchars($idContent)?>"/>
			<input type="hidden" name="field[<?=$id?>][type][]" value="<?=htmlspecialchars($idStructure)?>" />
			<input type="text" name="field[<?=$id?>][title][]" value="<?=htmlspecialchars($title)?>" disabled="disabled" class="field relationTitle"/>
			<a href="content.php?a=edit&amp;id=<?=htmlspecialchars($idContent)?>&amp;idt=<?=htmlspecialchars($idStructure)?>&amp;idp=<?=htmlspecialchars($idParent)?>&amp;idtp=<?=htmlspecialchars($idStructureParent)?>" class="button edit">Edit</a>
			<a href="content.php?a=edit&amp;id=<?=urlencode($idParent)?>&amp;idt=<?=urlencode($idStructureParent)?>&amp;idm=<?=urlencode($fieldId)?>&amp;refm=&amp;reftm=&amp;posm=<?=$pos?>" class="button clear">Clear</a>
		</li>
<?php
			$pos++;
		}
	}
	if ($idParent) {
?>
	<li class="find">
		<a href="content_rel.php?a=select_type&amp;idp=<?=htmlspecialchars($idParent)?>&amp;idtp=<?=htmlspecialchars($idStructureParent)?>&amp;f=<?=htmlspecialchars($fieldId)?>" class="button search">Find new</a>
	</li>
<?php
	}
?>
</ul>