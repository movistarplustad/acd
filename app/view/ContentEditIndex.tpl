<main>
	<h2>Manage content type</h2>
	<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<ol id="structures_list">
	<?php
		foreach ($structures as $estructure) {
	?>
		<li class="structure">
			<a href="?a=list_contents&amp;id=<?=urlencode($estructure->getId())?>"><?=htmlspecialchars($estructure->getName())?></a>
		</li>
	<?php
		}
	?>
	</ol>
</main>