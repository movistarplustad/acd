<!-- TODO -->
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][id]" value="<?=htmlspecialchars($fieldId)?>"/>
<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=htmlspecialchars($id.'_'.$idParent)?>"><?=htmlspecialchars($fieldName)?></label>
<ul class="collection">
<?php
//	$id ='TODO';
	if ($fieldRef) { // TODO hacer que en el futuro fieldRef sea un array vacío
		$pos = 0;
		foreach ($fieldRef as $fieldRefItem) {
			$idContent = $fieldRefItem->getId();
			$idStructure = $fieldRefItem->getIdStructure();
			$title = $fieldRefItem->getTitle();
			$validityDate = \Acd\Model\ValueFormater::encode($fieldRefItem->getPeriodOfValidity(), \Acd\Model\ValueFormater::TYPE_DATE_RANGE, \Acd\Model\ValueFormater::FORMAT_HUMAN);
?>
		<li class="relatedContent">
			<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][value][]" value="<?=htmlspecialchars($idContent)?>"/>
			<input type="hidden" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][type][]" value="<?=htmlspecialchars($idStructure)?>" />
			<input type="text" name="field[<?=htmlspecialchars($idParent)?>][<?=$id?>][title][]" value="<?=htmlspecialchars($title)?>" disabled="disabled" class="field relationTitle"/>
			<a href="content.php?a=edit&amp;id=<?=urlencode($idContent)?>&amp;idt=<?=urlencode($idStructure)?>&amp;idp=<?=urlencode($idParent)?>&amp;idtp=<?=urlencode($idStructureParent)?>" class="button edit">Edit</a>
			<a href="content.php?a=edit&amp;modrel=1&amp;id=<?=urlencode($idParent)?>&amp;idt=<?=urlencode($idStructureParent)?>&amp;posElement[]=<?=$pos?>&amp;element[<?=$pos?>][idm]=<?=urlencode($fieldId)?>&amp;element[<?=$pos?>][refm]=&amp;element[<?=$pos?>][reftm]=&amp;element[<?=$pos?>][posm]=<?=$pos?>" class="button clear">Clear</a>
			<span class="periodValidity" title="Period of validity"><?=$validityDate?></span>
		</li>
<?php
			$pos++;
		}
	}
	if (!$bNew) {
?>
	<li class="find">
		<a href="content_rel.php?a=select_type&amp;idp=<?=urlencode($idParent)?>&amp;idtp=<?=urlencode($idStructureParent)?>&amp;f=<?=urlencode($fieldId)?>" class="button search">Find new</a>
	</li>
<?php
	}
?>
</ul>
