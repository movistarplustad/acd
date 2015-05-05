<main>
	<h2>Manage structures</h2>
	<p class="result"><?=$resultDesc?></p>
	<ol id="structures_list">
	<?php
		foreach ($structures as $structure) {
	?>
		<li class="structure">
			<form action="do_process_structure.php" method="post">
				<input type="hidden" name="id" value="<?=htmlspecialchars($structure->getId())?>"/>
				<?=htmlspecialchars($structure->getName())?>
				<span class="tools"><input type="submit" name="a" value="edit" class="button edit"/>,  <input type="submit" name="a" value="clone" class="button clone"/>, <input type="submit" name="a" value="delete" class="button delete"/></span>
				<a href="content.php?a=list_contents&id=<?=htmlspecialchars($structure->getId())?>" class="button content">edit content</a>
			</form>
		</li>
	<?php
		}
	?>
	</ol>
	<div id="new_structure"><a href="?a=new" title="New structure" class="button new">new structure</a></div>
</main>