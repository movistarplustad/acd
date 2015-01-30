<main>
	<h2>Edit content <span class="structure_name"><?=htmlspecialchars($content->getTitulo())?></span></h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="do_process_content.php" method="post">
		<input type="hidden" name="id" value="<?=htmlspecialchars($content->getId())?>"/>
		<input type="hidden" name="ids" value="<?=htmlspecialchars($content->getIdStructure())?>"/>
		<div>
			<label for="name">Nombre</label>: <input type="text" name="name" id="name" value="<?=htmlspecialchars($content->getTitulo())?>"/>
		</div>
		<div>
			<?php
			$fields = $content->getData();
			$structure_fields = '';
			$n = 0;
			foreach ($fields as $key => $value) {
				$structure_fields .= '<li>
				<label for="field_'.$n.'">'.$key.'</label>
				<input type="text" name="field['.$n.'][name]" value="'.htmlspecialchars($value).'" id="field_'.$n.'"/>';
				$n++;
			}
			?>
			<fieldset>
				<legend>Campos</legend>
				<ul><?=$structure_fields?></ul>
			</fieldset>
		</div>
		<input type="hidden" name="a" value="<?=$actionValue?>"/><!-- TODO -->
		<input type="submit" name="accion" value="save" class="button publish"/>
		<input type="submit" name="accion" value="delete" class="button delete"/><!-- No sacar si es nuevo (id=='') -->
	</form>
</main>