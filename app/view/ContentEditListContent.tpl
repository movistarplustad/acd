<?php
$limits = $contents->getLimits();
$bMorePage = $limits->getUpper() < $limits->getTotal();
?><main>
	<h2>Manage elements <spam class="structure_name"><?=htmlspecialchars($structure->getName())?></spam></h2>
	<form action="" method="get">
		<input type="hidden" name="id" value="<?=htmlspecialchars($structure->getId())?>"/>
		<input type="hidden" name="a" value="list_contents"/>
		<label for="title">Title:</label><input type="search" name="s" id="title" value="<?=htmlspecialchars($titleSearch)?>" />
		<input type="submit" name="action" value="search" class="button search" />
	</form>
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
	<?php
	if ($bMorePage) {
		$nextPage = $limits->getUpper() / $limits->getStep();
		$totalPages = ceil($limits->getTotal() / $limits->getUpper());
		?>
		<p><a href="?a=list_contents&amp;id=<?=htmlspecialchars($structure->getId())?>&amp;p=<?=$nextPage?>">More…</a> (<?=$nextPage?> / <?=$totalPages?>)</p>
		<?php
	}
	?>
	<div id="new_structure"><a href="?a=new&amp;idt=<?=htmlspecialchars($structure->getId())?>" title="New content" class="button new">new content</a></div>
</main>