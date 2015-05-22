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
		<ol id="contents_list" data-lower-limit="<?=$lowerLimit?>">
		<?php
			foreach ($resultSearchContents as $contentFound) {
		?>
			<li><a href="content.php?a=edit&amp;id=<?=urlencode($id)?>&amp;idt=<?=urlencode($type)?>&amp;idm=<?=urlencode($idField)?>&amp;refm=<?=urlencode($contentFound->getId())?>&amp;reftm=<?=urlencode($contentFound->getIdStructure())?>&amp;posm=<?=urlencode($positionInField)?>"><?=htmlspecialchars($contentFound->getTitle())?></a></li>
		<?php
			}
		?>
		</ol>
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