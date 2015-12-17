<main id="showMatches">
	<?php
		// Distribute results in precise and difuse lists
		$list['precise'] = '';
		$list['difuse'] = '';
		foreach ($matchList as $item) {
			$target = ($item->getAliasId() === $aliasId) ? 'precise' : 'difuse';
			$list[$target] .= '<li><a href="content.php?a=edit&amp;id='.htmlspecialchars($item->getId()).'&amp;idt='.htmlspecialchars($item->getIdStructure()).'">'.htmlspecialchars($item->getTitle()).'</a> ('.htmlspecialchars($item->getIdStructure()).')</li>';
		}
	?>
	<h2><em><?=htmlspecialchars($contentTitle)?></em> are in:</h2>
	<ol id="precise_aliasId_list">
			<?=$list['precise']?>
	</ol>
	<?php
	if ($list['difuse']) {
	?>
		<h2>by difuse search are in:</h2>
		<ol id="difuse_aliasId_list">
				<?=$list['difuse']?>
		</ol>
	<?php
	}
	?>
</main>
