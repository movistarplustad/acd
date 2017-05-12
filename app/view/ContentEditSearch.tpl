<main>
	<h2>Search content</h2>
	<p class="result"><?=$resultDesc?></p>
	<form action="" method="get">
		<input type="hidden" name="idp" value="<?=htmlspecialchars($id)?>"/>
		<input type="hidden" name="idtp" value="<?=htmlspecialchars($type)?>"/>
		<input type="hidden" name="f" value="<?=htmlspecialchars($idField)?>"/>
		<label for="title">Title:</label><input type="search" name="s" id="title" value="<?=htmlspecialchars($titleSearch)?>" />
		<label for="idt">Structure type:</label>
			<select name="idt" id="idt" required="required">
				<option></option>
			<?php
				foreach ($structures as $estructure) {
					$selected = $estructure->getId() === $idStructureTypeSearch ? ' selected="selected"' : '';
			?>
				<option value="<?=htmlspecialchars($estructure->getId())?>"<?=$selected?>><?=htmlspecialchars($estructure->getName())?></option>
			<?php
				}
			?>
			</select>
		<input type="submit" name="a" value="search"/>
		<!--
		<input type="search" name="idt" id="idt" list="structure_type"/>
		<datalist id="structure_type">

		</datalist>
		-->
	</form>
	<?php
	if($resultSearchContents) {
		$limits = $resultSearchContents->getLimits();
		$lowerLimit = $limits->getLower();
		$bMorePage = $limits->getUpper() < $limits->getTotal();
	?>
		<form action="content.php?a=edit&id=<?=urlencode($id)?>&idt=<?=urlencode($type)?>" method="post">
			<input name="a" value="edit" type="hidden"/>
			<input name="modrel" value="1" type="hidden"/>
			<input name="id" value="<?=htmlspecialchars($id)?>" type="hidden"/>
			<input name="idt" value="<?=htmlspecialchars($type)?>" type="hidden"/>
			<ol id="contents_list" data-lower-limit="<?=$lowerLimit?>">
			<?php
				$posElement = 0;
				foreach ($resultSearchContents as $contentFound) {
					$posElement++;
			?>
				<li>
					<input name="posElement[]" value="<?=$posElement?>" type="checkbox" id="content_<?=$posElement?>"/>
					<input name="element[<?=$posElement?>][id]" value="<?=htmlspecialchars($id)?>" type="hidden"/>
					<input name="element[<?=$posElement?>][idt]" value="<?=htmlspecialchars($type)?>" type="hidden"/>
					<input name="element[<?=$posElement?>][idm]" value="<?=htmlspecialchars($idField)?>" type="hidden"/>
					<input name="element[<?=$posElement?>][refm]" value="<?=htmlspecialchars($contentFound->getId())?>" type="hidden"/>
					<input name="element[<?=$posElement?>][reftm]" value="<?=htmlspecialchars($contentFound->getIdStructure())?>" type="hidden"/>
					<input name="element[<?=$posElement?>][posm]" value="<?=htmlspecialchars($positionInField)?>" type="hidden"/>
					<label for="content_<?=$posElement?>"><?=htmlspecialchars($contentFound->getTitle())?></label>
				</li>
			<?php
				}
			?>
			</ol>
			<div class="wrap-actions">
				<div class="actions">

					<input name="action" value="add top" class="button add_top" type="submit"/>
					<input name="action" value="add bottom" class="button add_bottom" type="submit"/>
					<!--
					<span class=""><input name="relto" value="top" type="checkbox" id="relto"/><label for="relto">Top insert</label></span>
					<input name="action" value="add" class="button add" type="submit"/>
					-->
				</div>
			</div>
		</form>
	<?php
	if ($bMorePage) {
		$nextPage = $limits->getUpper() / $limits->getStep();
		$totalPages = ceil($limits->getTotal() / $limits->getStep());
		?>
		<p class="pagination">[<?=$nextPage?> / <?=$totalPages?>] <a href="?idp=<?=urlencode($id)?>&amp;idtp=<?=urlencode($type)?>&amp;f=<?=urlencode($idField)?>&amp;s=<?=urlencode($titleSearch)?>&amp;idt=<?=urlencode($idStructureTypeSearch)?>&amp;a=search&amp;p=<?=$nextPage?>">More…</a></p>
		<?php
	}
	?>
	<?php
	}
	?>

</main>
