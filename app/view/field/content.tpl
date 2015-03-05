<!-- TODO -->
<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<input type="text" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldRef)?>" id="field_<?=$id?>" readonly="readonly"/>
<?php
if ($fieldRef) {
?>
	<a href="content.php?a=edit&amp;id=<?=htmlspecialchars($fieldRef)?>&amp;idt=<?=htmlspecialchars($fieldStructureRef)?>&amp;idp=<?=htmlspecialchars($idParent)?>idtp=<?=htmlspecialchars($idStructureParent)?>">Editar</a>
<?php
}
?>
