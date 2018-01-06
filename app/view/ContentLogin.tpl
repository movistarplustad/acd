<main>
	<h2>Authentication</h2>

	<form action="do_login.php" method="post" class="form_login">
		<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
		<?php
		if(isset($urlPostLogin)) {
		?>
			<input type="hidden" name="re" value="<?=htmlspecialchars($urlPostLogin)?>"/>
		<?php
		}
		?>
		<div class="text">
			<label for="login">Login</label>
			<input type="text" name="login" value="<?=htmlspecialchars($login)?>" spellcheck="false" placeholder="e.g. tiranosaurus" id="login" required="required"/>
		</div>
		<div class="text">
			<label for="password">Password</label>
			<input type="password" name="password" value="" placeholder="e.g. *******" id="password" required="required"/>
		</div>
		<div class="remember">
			<input type="checkbox" name="remember" id="remember" value="1" <?=\Acd\Model\ValueFormater::encode($bRemember, \Acd\Model\ValueFormater::TYPE_BOOLEAN, \Acd\Model\ValueFormater::FORMAT_EDITOR)?>/>
			<label for="remember">Remember</label>
		</div>
		<div class="submit"><input type="submit" name="a" value="Enter"/></div>
	</form>
</main>
