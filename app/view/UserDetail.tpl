<?php
$bNew = $userElement->getId() == '';
$userRol = $userElement->getRol();
?>
<main id="manageStructure">
	<h2>User <em class="structure_name"><?=$userElement->getId()?></em></h2>
	<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<form action="do_user.php" method="post">
		<fieldset class="internal">
		<?php
			if($bNew) {
		?>
			<div class="pre_table user">
				<label for="id_user">Id user:</label> <input name="id" id="id_user" value="" required="required" placeholder="New id" type="text"/>
			</div>
			<div>
				<label for="password">Password:&nbsp;</label><input type="password" name="password" id="password" value="" required="required"/>
			</div>
		<?php
			}
			else {
		?>
			<input name="id" value="<?=$userElement->getId()?>" type="hidden"/>
			<div>
				<label for="password">Password:&nbsp;</label><input type="password" name="password" id="password" value=""/>
			</div>
		<?php
			}
		?>
		<div>
			<?php
			$options = '';
			foreach ($roles as $key => $value) {
				$selected = $userRol === $key ? ' selected="selected"' : '';
				$options .= '<option value="'.htmlspecialchars($key).'"'.$selected.'>'.htmlspecialchars($value).'</option>';

			}
			?>
			<label for="storage">Rol:&nbsp;</label><select name="rol" id="rol" required="required"><option></option><?=$options?></select>
		</div>
		<div class="actions">
			<input name="a" value="save" class="button publish" type="submit">
		</div>
		</fieldset>
	</form>
</main>
