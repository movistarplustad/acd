<?php
	$title = (isset($bNew) && $bNew === true)
		? 'New content'
		: 'Edit content <spam class="structure_name">'.htmlspecialchars($structure->getName()).'</spam>';
?>
<main>
	<h2><?=$title?></h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="do_process_content.php" method="post">
		<input type="hidden" name="id" value="<?=htmlspecialchars($content->getId())?>"/>
		<input type="hidden" name="ids" value="<?=htmlspecialchars($content->getIdStructure())?>"/>
		<div>
			<label for="title">Title</label>: <input type="text" name="title" id="title" value="<?=htmlspecialchars($content->getTitle())?>"/>
		</div>
		<div>
			<?php
			$fields = $structure->getFields();
			$structure_fields = '';
			$n = 0;
			foreach ($fields as $field) {
				$fieldName = $field->getName();
				$fieldValue = $content->getFieldValue($fieldName);
				
				$structure_fields .= '<li>
				<input type="hidden" name="field['.$n.'][id]" value="'.htmlspecialchars($fieldName).'"/>
				<input type="hidden" name="field['.$n.'][name]" value="'.htmlspecialchars($fieldName).'"/>
				<label for="field_'.$n.'">'.$fieldName.'</label>
				<input type="text" name="field['.$n.'][value]" value="'.htmlspecialchars($fieldValue).'" id="field_'.$n.'"/>';
				$n++;
			}
			?>
			<fieldset>
				<legend>Fields</legend>
				<ul><?=$structure_fields?></ul>
			</fieldset>
		</div>
		<input type="submit" name="a" value="save" class="button publish"/>
		<?php
			if($content->getId()) {
		?>
			<input type="submit" name="a" value="clone" class="button clone"/>
			<input type="submit" name="a" value="delete" class="button delete"/>
		<?php
			}
		?>
	</form>
</main>