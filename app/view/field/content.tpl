<!-- TODO -->
<?php
	@$fieldRef['ref']= $fieldRef['ref'] ?: ''; // TODO, porner el valor por defecto mejor  a nivel de clase
	@$fieldRef['id_structure']= $fieldRef['id_structure'] ?: ''; 
?>
<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<input type="text" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldRef['ref'])?>" id="field_<?=$id?>" readonly="readonly"/>
<input type="hidden" name="field[<?=$id?>][type]" value="<?=htmlspecialchars($fieldRef['id_structure'])?>"/>
<?php
if ($fieldRef ['ref']) {
?>
	<a href="content.php?a=edit&amp;id=<?=htmlspecialchars($fieldRef['ref'])?>&amp;idt=<?=htmlspecialchars($fieldRef['id_structure'])?>&amp;idp=<?=htmlspecialchars($idParent)?>&amp;idtp=<?=htmlspecialchars($idStructureParent)?>">Edit</a>
	<a href="content_rel.php?a=select_type&amp;id=<?=htmlspecialchars($fieldRef['ref'])?>&amp;idt=<?=htmlspecialchars($fieldRef['id_structure'])?>&amp;idp=<?=htmlspecialchars($idParent)?>&amp;idtp=<?=htmlspecialchars($idStructureParent)?>&amp;f=<?=htmlspecialchars($fieldName)?>">Find</a>
<?php
}
?>