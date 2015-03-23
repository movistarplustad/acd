<main>
	<h2>Search content</h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="" method="get">
		<input type="hidden" name="idp" value="<?=$id?>"/>
		<input type="hidden" name="idtp" value="<?=$type?>"/>
		<label for="title">Title:</label><input type="search" name="title" id="title"/>
		<label for="idt">Structure type:</label>
			<select name="idt">
				<option></option>
			<?php
				foreach ($structures as $estructure) {
			?>
				<option value="<?=htmlspecialchars($estructure->getId())?>"><?=htmlspecialchars($estructure->getName())?></option>
			<?php
				}
			?>
			</select>
		<input type="submit" name="a" value="search"/>
		<!-- 
		<input type="search" name="idt" id="idt" list="structure_type"/>
		<datalist id="structure_type">

		</datalist>
		-->
	</form>

</main>