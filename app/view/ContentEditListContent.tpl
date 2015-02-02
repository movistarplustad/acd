<main>
	<h2>Manage content <spam class="structure_name"><?=htmlspecialchars($structure->getName())?></spam></h2>
	<p class="result"><?=$resultDesc?></p>
	<ol id="structures_list">
	<?php
		foreach ($contents as $content) {
	?>
		<li class="content">
			<a href="?a=edit&amp;id=<?=htmlspecialchars($content->getId())?>&amp;idt=<?=htmlspecialchars($content->getIdStructure())?>"><?=htmlspecialchars($content->getTitle())?></a>
		</li>
	<?php
		}
	?>
	</ol>
	<div id="new_structure"><a href="?a=new&amp;idt=<?=htmlspecialchars($content->getIdStructure())?>" title="New content" class="button new">new content</a></div>
</main>