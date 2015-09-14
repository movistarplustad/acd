<main id="manageStructure">
	<h2>Enumerated collections</h2>
	<ol id="enumerated_list" class="list_with_options">
		<?php
			foreach ($enumeratedList as $item) {
		?>
			<li class="list_item">
			<form action="do_enumerated.php" method="post">
				<input type="hidden" name="id" value="<?=htmlspecialchars($item['id'])?>"/>
				<label><?=htmlspecialchars($item['id'])?></label>
				<span class="tools">
					<input type="submit" name="a" value="edit" class="button edit"/>
					<input type="submit" name="a" value="delete" class="button delete"/>
				</span>
			</form>
		</li>
		<?php
			}
		?>
	</ol>
	<div id="new_enumerated" class="actions"><a href="?a=new" title="New enumerated" class="button new">new enumerated</a></div>
</main>