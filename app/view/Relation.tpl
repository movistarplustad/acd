<main id="showRelation">
	<h2><em><?=htmlspecialchars($contentTitle)?></em> element is part of:</h2>
	<ol id="enumerated_list">
		<?php
			foreach ($parentList as $item) {
		?>
			<li><a href="content.php?a=edit&amp;id=<?=htmlspecialchars($item->getId()).'&amp;idt='.htmlspecialchars($item->getIdStructure())?>"><?=htmlspecialchars($item->getTitle())?></a></li>
		<?php
			}
		?>
	</ol>
</main>