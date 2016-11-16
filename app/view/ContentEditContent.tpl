<?php
	$idHtml = htmlspecialchars($idContent);
	$title = (isset($bNew) && $bNew === true)
		? 'New content <em class="structure_name">'.htmlspecialchars($structure->getName()).'</em>'
		: 'Edit content <em class="structure_name">'.htmlspecialchars($structure->getName()).'</em>';
	$relationCount = $content->getCountParents();
	$aliasIdCount = $content->getCountAliasId();
	$deleteDisabled = $relationCount > 0 ? ' disabled="disabled"' : '';
?>
<main>
	<h2><?=$title?></h2>

	<?php
		if(isset($jsonSummary)) {
	?>
		<section class="contentSummary">
			<h2>Summary</h2>
			<a href="?a=edit&amp;id=<?=urlencode($content->getId())?>&amp;idt=<?=urlencode($content->getIdStructure())?>" class="close">Close</a>
			<pre><?=$jsonSummary?></pre>
		</section>
	<?php
		}
	?>
	<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<form action="do_process_content.php" method="post" enctype="multipart/form-data">
		<div class="inner-form">
			<fieldset class="internal">
				<legend>Internal data</legend>
				<input type="hidden" name="id[]" value="<?=$idHtml?>"/>
				<input type="hidden" name="ids[<?=$idHtml?>]" value="<?=htmlspecialchars($content->getIdStructure())?>"/>
				<ul>
					<li class="item">
						<label for="title_<?=$idHtml?>">Title:&nbsp;</label><input type="text" name="title[<?=$idHtml?>]" id="title_<?=$idHtml?>" value="<?=htmlspecialchars($contentTitle)?>" required="required" class="field text" maxlength="256"/>
					</li>
					<li class="item">
					<?php
						$periodOfValidity = \Acd\Model\ValueFormater::encode($content->getPeriodOfValidity(), \Acd\Model\ValueFormater::TYPE_DATE_TIME_RANGE, \Acd\Model\ValueFormater::FORMAT_EDITOR );
					?>
					<label for="validityPeriodStart_<?=$idHtml?>">Period of validity:&nbsp;</label><input type="datetime" name="validityPeriod[<?=$idHtml?>][start]" id="validityPeriodStart_<?=$idHtml?>" value="<?=htmlspecialchars($periodOfValidity[\Acd\Model\contentDO::PERIOD_OF_VALIDITY_START])?>" class="range start"/>
						-
						<input type="datetime" name="validityPeriod[<?=$idHtml?>][end]" id="validityPeriodEnd_<?=$idHtml?>" value="<?=htmlspecialchars($periodOfValidity[\Acd\Model\contentDO::PERIOD_OF_VALIDITY_END])?>" class="range end"/>
					</li>
					<li class="item">
						<?php
							if($aliasIdCount > 1) {
						?>
							<p class="result info"><strong>Info.</strong> Alias-id is repeated <a href="alias_id.php?id=<?=urlencode($aliasId)?>"><?=$aliasIdCount?></a> times.</p>
						<?php
							}
						?>
						<label for="aliasId_<?=$idHtml?>">Alias-id.:&nbsp;</label><input type="text" name="aliasId[<?=$idHtml?>]" id="aliasId_<?=$idHtml?>" value="<?=htmlspecialchars($aliasId)?>" class="field aliasId" maxlength="256"/>
					</li>
					<li class="item">
						<label for="tags_<?=$idHtml?>" class="for-tag">Tags:&nbsp;</label><input type="text" name="tags[<?=$idHtml?>]" id="tags_<?=$idHtml?>" value="<?=htmlspecialchars($contentTags)?>" class="field tags"<?=$tagsReadonly?>/>
					</li>
					<li class="item">
						<?=$profileOU->render()?>
					</li>
					<?php
						if($content->getCountParents() !== null && $relationCount > 0) {
					?>
						<li class="item"><label>#Relations:&nbsp;</label><a href="relation.php?id=<?=urlencode($content->getId())?>&amp;idt=<?=urlencode($content->getIdStructure())?>"><?=$relationCount?></a></li>
					<?php
						}
					?>
				</ul>
			</fieldset>
			<div class="fields">
				<?php
				// TODO: ¡¡bastante enrevesado para estar dentro de un tpl!!
				$fieldOU = new Acd\View\Field();
				$fields = $structure->getFields();
				$structure_fields = '';
				$n = 0;
				foreach ($fields as $field) {
					$idField = $field->getId();
					try {
						$fieldFromContent = $content->getFields()->get($idField);
						//+d($fieldFromContent->tokenizeData()[$idField]);
						$field->loadData($idField, $fieldFromContent->tokenizeData()[$idField], false); // TODO: ¡¡bastante enrevesado para estar dentro de un tpl!!
					}
					catch( \Exception $e ) {
						$field->loadData($idField, '', true);
					}
					$fieldOU->setField($field);
					$fieldOU->setId($n);
					$fieldOU->setParent($content);
					$structure_fields .= '<li>'.$fieldOU->render().'</li>';

					$n++;
				}
				?>
				<fieldset class="fields">
					<legend>Fields</legend>
					<ul class="fields"><?=$structure_fields?></ul>
				</fieldset>
			</div>
			<div class="wrap-actions">
				<div class="actions">
					<input type="submit" name="a" value="save" class="button publish"/>
					<?php
						if($content->getId()) {
					?>
						<input type="submit" name="a" value="clone" class="button clone"/>
						<input type="submit" name="a" value="delete" class="button delete"<?=$deleteDisabled?>/>
					<?php
						}
					?>
					<input type="submit" name="a" value="summary" class="button summary"/>
				</div>
			</div>
		</div>
	</form>
</main>
