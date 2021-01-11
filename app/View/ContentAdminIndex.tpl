<main>
	<h2>Manage structures</h2>
	<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<ol id="structures_list" class="list_with_options">
	<?php
		foreach ($structures as $structure) {
	?>
		<li class="structure list_item">
			<form action="do_process_structure.php" method="post">
				<input type="hidden" name="id" value="<?=htmlspecialchars($structure->getId())?>"/>
				<label><?=htmlspecialchars($structure->getName())?></label>
				<span class="tools">
					<input type="submit" name="a" value="edit" class="button edit"/>  
					<input type="submit" name="a" value="clone" class="button clone"/> 
					<input type="submit" name="a" value="delete" class="button delete"/> 
					<a href="content.php?a=list_contents&amp;id=<?=urlencode($structure->getId())?>" class="button content">edit content</a>
				</span>
			</form>
		</li>
	<?php
		}
	?>
	</ol>
	<div id="new_structure" class="actions"><a href="?a=new" title="New structure" class="button new">new structure</a></div>
</main>