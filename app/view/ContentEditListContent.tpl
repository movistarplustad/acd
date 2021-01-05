<?php
$limits = $contents->getLimits();
$lowerLimit = $limits->getLower();
$bMorePage = $limits->getUpper() <= $limits->getTotal();
$setPrevPage = $lowerLimit > 0;
$nextPage = $limits->getUpper() / $limits->getStep();
$prevPage = $limits->getLower() / $limits->getStep() -1;
$totalPages = ceil($limits->getTotal() / $limits->getStep());
$setPagination = $setPrevPage || $bMorePage;
?>
<main>
	<h2>Manage elements <em class="structure_name"><?=htmlspecialchars($structure->getName())?></em></h2>
	<form action="" method="get" class="wrap_search">
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
	if($setPagination) {
	 ?>
	<p class="pagination">
		<?php if($setPrevPage) { ?>
			<a class="testII" href="?a=list_contents&amp;id=<?=urlencode($structure->getId())?>&amp;p=<?=$prevPage?>&amp;s=<?=urlencode($titleSearch)?>">Prev</a>
		<?php } ?>
		[<?=$nextPage?> / <?=$totalPages?>] 
		<?php if($bMorePage) {?>
		<a class="testII" href="?a=list_contents&amp;id=<?=urlencode($structure->getId())?>&amp;p=<?=$nextPage?>&amp;s=<?=urlencode($titleSearch)?>">Next</a>
		<?php } ?>
	</p>
	<?php 
	}
	?>
	<div id="new_content" class="actions"><a href="?a=new&amp;idt=<?=urlencode($structure->getId())?>" title="New content" class="button new">new content</a></div>
</main>