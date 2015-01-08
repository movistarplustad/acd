<main>
	<h2>Manage contents</h2>
	<p class="result"><?=$resultDesc?></p>
	<ol id="structures_list">
	<?php
		foreach ($structures as $estructure) {
	?>
		<li class="structure">
			<a href="?a=edit&amp;id=<?=htmlspecialchars($estructure->getId())?>"><?=htmlspecialchars($estructure->getName())?></a>
		</li>
	<?php
		}
	?>
	</ol>
</main>