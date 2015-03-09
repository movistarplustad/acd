<!-- TODO -->
<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<ul>
<?php 
	foreach ($fieldRef as $fieldRefItem) {
		$idItem = $fieldRefItem['$id'];
?>
	<li>
		<input type="text" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldRefItem['$id'])?>" id="field_<?=$idItem?>" readonly="readonly"/>
		<a href="content.php?a=edit&amp;id=<?=htmlspecialchars($idItem)?>&amp;idt=<?=htmlspecialchars($fieldStructureRef)?>&amp;idp=<?=htmlspecialchars($idParent)?>&amp;idtp=<?=htmlspecialchars($idStructureParent)?>">Edit</a>
	</li>
<?php
	}
?>
</ul>