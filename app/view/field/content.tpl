<!-- TODO -->
<?php
//	@$fieldRef['ref']= $fieldRef['ref'] ?: ''; // TODO, porner el valor por defecto mejor  a nivel de clase
//	@$fieldRef['id_structure']= $fieldRef['id_structure'] ?: ''; 
$fieldRef = $fieldRef->one();
$idContent = $fieldRef->getId();
$idStructure = $fieldRef->getIdStructure();
$title = $fieldRef->getTitle();
?>
<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<input type="text" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($idContent)?>" id="field_<?=$id?>" readonly="readonly"/>
<input type="hidden" name="field[<?=$id?>][type]" value="<?=htmlspecialchars($idStructure)?>"/>
<input type="text" name="field[<?=$id?>][title][]" value="<?=htmlspecialchars($title)?>" disabled="disabled" class="title"/>
<?php
if ($idContent) {
?>
	<a href="content.php?a=edit&amp;id=<?=urlencode($idContent)?>&amp;idt=<?=urlencode($idStructure)?>&amp;idp=<?=urlencode($idParent)?>&amp;idtp=<?=urlencode($idStructureParent)?>" class="button edit">Edit</a>
	<a href="content.php?a=edit&amp;id=<?=urlencode($idParent)?>&amp;idt=<?=urlencode($idStructureParent)?>&amp;idm=<?=urlencode($fieldId)?>&amp;refm=&amp;reftm=&amp;posm=" class="button clear">Clear</a>
<?php
}
if ($idParent) {
?>
<a href="content_rel.php?a=select_type&amp;idp=<?=urlencode($idParent)?>&amp;idtp=<?=urlencode($idStructureParent)?>&amp;f=<?=urlencode($fieldId)?>&amp;idt=<?=urlencode($idStructure)?>" class="button search">Find</a>
<?php
}
?>