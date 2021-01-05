<!-- TODO -->
<?php
//	@$fieldRef['ref']= $fieldRef['ref'] ?: ''; // TODO, porner el valor por defecto mejor  a nivel de clase
//	@$fieldRef['id_structure']= $fieldRef['id_structure'] ?: '';
if ($fieldRef) {
	$idContent = $fieldRef->getId();
	$idStructure = $fieldRef->getIdStructure();
	$title = $fieldRef->getTitle();
	$validityDate = \Acd\Model\ValueFormater::encode($fieldRef->getPeriodOfValidity(), \Acd\Model\ValueFormater::TYPE_DATE_RANGE, \Acd\Model\ValueFormater::FORMAT_HUMAN);
	$pos = 0;
}
else {
	$idContent = '';
	$idStructure = '';
	$title = '';
	$validityDate = '';
	$pos = 0;
}
?>
	<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
	<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
	<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
	<span class="relatedContent">
		<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value]" value="<?=htmlspecialchars($idContent)?>" id="field_<?=htmlspecialchars($id.'_'.$idParent)?>"/>
		<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][type]" value="<?=htmlspecialchars($idStructure)?>"/>
		<input type="text" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][title][]" value="<?=htmlspecialchars($title)?>" disabled="disabled" class="field relationTitle"/>
<?php
		if ($idContent) {
?>
		<a href="content.php?a=edit&amp;id=<?=urlencode($idContent)?>&amp;idt=<?=urlencode($idStructure)?>&amp;idp=<?=urlencode($idParent)?>&amp;idtp=<?=urlencode($idStructureParent)?>" class="button edit">Edit</a>
		<a href="content.php?a=edit&amp;modrel=1&amp;id=<?=urlencode($idParent)?>&amp;idt=<?=urlencode($idStructureParent)?>&amp;posElement[]=<?=$pos?>&amp;element[<?=$pos?>][idm]=<?=urlencode($fieldId)?>&amp;element[<?=$pos?>][refm]=&amp;element[<?=$pos?>][reftm]=&amp;element[<?=$pos?>][posm]=<?=$pos?>" class="button clear">Clear</a>
		<span class="periodValidity" title="Period of validity"><?=$validityDate?></span>
<?php
		}
		if (!$bNew) {
?>
		<a href="content_rel.php?a=select_type&amp;idp=<?=urlencode($idParent)?>&amp;idtp=<?=urlencode($idStructureParent)?>&amp;f=<?=urlencode($fieldId)?>&amp;idt=<?=urlencode($idStructure)?>" class="button search">Find</a>
<?php
}
		if ($validityDate) {
?>
		<span class="periodValidity" title="Period of validity"><?=$validityDate?></span>
<?php
		}
?>
	</span>
