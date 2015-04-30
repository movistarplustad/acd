<?php
	$title = (isset($bNew) && $bNew === true)
		? 'New content'
		: 'Edit content <spam class="structure_name">'.htmlspecialchars($structure->getName()).'</spam>';
	$relationCount = $content->getCountParents();
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
				<!--
				<li><label for="expiryStart">Expiry date</label>:  <input type="text" name="expiryStart" id="expiryStart" value="<?=htmlspecialchars('expiryStart')?>" class="range start"/>
					-
					<input type="text" name="expiryEnd" id="expiryEnd" value="<?=htmlspecialchars('expiryEnd')?>" class="range end"/>
				</li>
				-->
				<li>
					<label for="tags">Tags</label>: <input type="text" name="tags" id="tags" value="<?=htmlspecialchars($contentTags)?>"<?=$userRol?>/>
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