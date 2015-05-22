<main>
	<h2>Manage elements <spam class="structure_name"><?=htmlspecialchars($structure->getName())?></spam></h2>
	<form action="" method="get">
		<input type="hidden" name="id" value="<?=htmlspecialchars($structure->getId())?>"/>
		<input type="hidden" name="a" value="list_contents"/>
		<label for="title">Title:&nbsp;</label><input type="search" name="s" id="title" value="<?=htmlspecialchars($titleSearch)?>" />
		<input type="submit" name="action" value="search" class="button search" />
	</form>
	<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<ol id="contents_list" data-lower-limit="<?=$lowerLimit?>">
	<?php
		foreach ($contents as $content) {
	?>
		<li class="content">
			<a href="?a=edit&amp;id=<?=urlencode($content->getId())?>&amp;idt=<?=urlencode($content->getIdStructure())?>"><?=htmlspecialchars($content->getTitle())?></a>
		</li>
	<?php
		}
	?>
	</ol>
	<?php
	$limits = $contents->getLimits();
	$lowerLimit = $limits->getLower();
	$bMorePage = $limits->getUpper() < $limits->getTotal();
	if ($bMorePage) {
		$nextPage = $limits->getUpper() / $limits->getStep();
		$totalPages = ceil($limits->getTotal() / $limits->getStep());
		?>
		<p class="pagination">[<?=$nextPage?> / <?=$totalPages?>] <a href="?a=list_contents&amp;id=<?=urlencode($structure->getId())?>&amp;p=<?=$nextPage?>">More…</a></p>
		<?php
	}
	?>
	<div id="new_structure"><a href="?a=new&amp;idt=<?=urlencode($structure->getId())?>" title="New content" class="button new">new content</a></div>
</main>