<?php
	$title = (isset($bNew) && $bNew === true)
		? 'New content'
		: 'Edit content <spam class="structure_name">'.htmlspecialchars($structure->getName()).'</spam>';
	$relationCount = $content->getCountParents();
	$aliasIdCount = $content->getCountAliasId();
	$deleteDisabled = $relationCount > 0 ? ' disabled="disabled"' : '';
?>
<main>
	<h2><?=$title?></h2>
	<?php
		if($content->getCountParents() !== null) {
	?>
		<p>#Relations: <?=$relationCount?></p>
	<?php
		}
	?>
	<?php
		if(isset($jsonSummary)) {
	?>
		<section class="contentSummary">
			<h2>Summary</h2>
			<pre><?=$jsonSummary?></pre>
		</section>
	<?php
		}
	?>
	<p class="result"><?=$resultDesc?></p>
	<form action="do_process_content.php" method="post" enctype="multipart/form-data">
		<fieldset class="internal">
			<legend>Internal data</legend>
			<input type="hidden" name="id" value="<?=htmlspecialchars($content->getId())?>"/>
			<input type="hidden" name="ids" value="<?=htmlspecialchars($content->getIdStructure())?>"/>
			<ul>
				<li>
					<label for="title">Title</label>: <input type="text" name="title" id="title" value="<?=htmlspecialchars($contentTitle)?>" required="required" class="field text"/>
				</li>
				<li>
				<?php
					$periodOfValidity = \Acd\Model\ValueFormater::encode($content->getPeriodOfValidity(), \Acd\Model\ValueFormater::TYPE_DATE_TIME_RANGE, \Acd\Model\ValueFormater::FORMAT_EDITOR );
				?>
				<label for="validityPeriodStart">Period of validity</label>:  <input type="datetime" name="validityPeriod[start]" id="validityPeriodStart" value="<?=htmlspecialchars($periodOfValidity[\Acd\Model\contentDO::PERIOD_OF_VALIDITY_START])?>" class="range start"/>
					-
					<input type="datetime" name="validityPeriod[end]" id="validityPeriodEnd" value="<?=htmlspecialchars($periodOfValidity[\Acd\Model\contentDO::PERIOD_OF_VALIDITY_END])?>" class="range end"/>
				</li>
				<li>
					<?php
						if($aliasIdCount > 1) {
					?>
						<p class="warning"><strong>Warning</strong> alias-id. repeat at <?=$aliasIdCount?> times.</p>
					<?php
						}
					?>
					<label for="aliasId">Alias-id.: </label> <input type="text" name="aliasId" id="aliasId" value="<?=htmlspecialchars($aliasId)?>" class="field text"/>
				</li>
				<li>
					<label for="tags" class="for-tag">Tags: </label> <input type="text" name="tags" id="tags" value="<?=htmlspecialchars($contentTags)?>" class="field tags"<?=$userRol?>/>
				</li>
			</ul>
		</fieldset>
		<div>
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
	</form>
</main>