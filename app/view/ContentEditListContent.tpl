<main>
	<h2>Manage content <spam class="structure_name"><?=htmlspecialchars($structure->getName())?></spam></h2>
	<p class="result"><?=$resultDesc?></p>
	<ol id="structures_list">
	<?php
		foreach ($structure->getFields() as $field) {
	?>
		<li class="structure">
			*
		</li>
	<?php
		}
	?>
	</ol>
</main>