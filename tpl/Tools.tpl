<aside id="tools" class="rol-<?=htmlspecialchars($rol)?>">
	<nav>
		<ul>
			<?php
				if ($rol == 'developer' || $rol == 'editor') {
			?>
			<li><a href="do_logout.php" class="no-button logout" rel="nofollow" title="<?=htmlspecialchars($login)?> - <?=htmlspecialchars($rol)?>">Logout (<?=htmlspecialchars($login)?>)</a></li>
			<?php
				}
				if ($rol == 'developer') {
			?>
			<li><a href="index.php" class="no-button edit-structures">Manage structures</a></li>
			<?php
				}
				if ($rol == 'developer' || $rol == 'editor') {
			?>
			<li><a href="content.php" class="no-button manage-content">Manage content</a></li>
			<?php
				}
			?>
		</ul>
	</nav>
</aside>