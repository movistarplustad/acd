<main>
	<h2>New structure</h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="do_process_structure.php" method="post">
		<div>
			<label for="id">Id</label>: <input type="text" name="id" id="id" value=""/>
		</div>
		<div>
			<label for="name">Name</label>: <input type="text" name="name" id="name" value=""/>
		</div>
		<div>
			<?php
			$options = '';
			foreach ($storageTypes as $key => $value) {
				$selected = $storage === $key ? ' selected="selected"' : '';
				$options .= '<option value="'.htmlspecialchars($key).'"'.$selected.'>'.htmlspecialchars($value).'</option>';
				
			}
			?>
			<label for="storage">Storage type</label>: <select name="storage" id="storage"><?=$options?></select>
		</div>

		<input type="hidden" name="a" value="new"/>
		<input type="submit" name="accion" value="save" class="button publish"/>
	</form>
</main>