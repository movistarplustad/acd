<main id="manageStructure">
	<h2>Enumerated collections</h2>
	<ol id="enumerated_list">
		<?php
			foreach ($enumeratedList as $item) {
		?>
			<li><a href="?id=<?=htmlspecialchars($item['id'])?>"><?=htmlspecialchars($item['id'])?></a></li>
		<?php
			}
		?>
	</ol>
</main>