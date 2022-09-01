<main id="manageUser">
	<h2>Users</h2>
	<ol id="usert_list" class="list_with_options">
		<?php
			foreach ($userList as $item) {
		?>
			<li class="list_item">
			<form action="do_user.php" method="post">
				<input type="hidden" name="id" value="<?=htmlspecialchars($item->getId())?>"/>
				<label><?=htmlspecialchars($item->getId())?></label>
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
	<div id="new_user" class="actions"><a href="?a=new" title="New user" class="button new">new user</a></div>
</main>
