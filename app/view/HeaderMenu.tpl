	<div id="header-menu">
		<?php
			switch ($this->type) {
				case 'menu':
					?><a href="#tools" class="tools-menu"><img src="style/ic_menu_24px_inverse.svg" alt="Menu" height="30"/><span class="label"> menu</span></a><?php
					break;
				
				case 'back':
					?><a href="index.php" class="back"><img src="style/ic_chevron_left_24px_inverse.svg" alt="Back" height="30"/><span class="label"> back</span></a><?php
					break;
				case 'backContent':
					?><a href="content.php" class="back"><img src="style/ic_chevron_left_24px_inverse.svg" alt="Back" height="30"/><span class="label"> back</span></a><?php
					break;
				case 'backListContent':
					?><a href="<?=$this->url?>" class="back"><img src="style/ic_chevron_left_24px_inverse.svg" alt="Back" height="30"/><span class="label"> back</span></a><?php
					break;
				case 'menuBackUrl':
					?><a href="#tools" class="tools-menu"><img src="style/ic_menu_24px_inverse.svg" alt="Menu" height="30"/><span class="label"> menu</span></a>
					<a href="<?=$this->url?>" class="back"><img src="style/ic_chevron_left_24px_inverse.svg" alt="Back" height="30"/><span class="label"> back</span></a><?php
					break;
			}
		?>
	</div>