<main>
	<h2>Manage content <spam class="structure_name"><?=htmlspecialchars($structure->getName())?></spam></h2>
	<p class="result"><?=$resultDesc?></p>
	<ol id="structures_list">
	<?php
		foreach ($contents as $content) {
	?>
		<li class="content">
			<a href="?a=edit&amp;id=<?=htmlspecialchars($content->getId())?>"><?=htmlspecialchars($content->getId())?></a>
		</li>
	<?php
		}
	?>
	</ol>
</main>