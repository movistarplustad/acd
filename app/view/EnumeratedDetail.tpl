<?php
$bNew = $enumeratedElement->getId() == '';
?>
<main id="manageStructure">
	<h2>Enumerated element <em class="structure_name"><?=$enumeratedElement->getId()?></em></h2>
	<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<form action="do_enumerated.php" method="post">
		<?php
			if($bNew) {
		?>
			<div class="pre_table enumerated">
				<label for="id_enumerated">Id enumerated collection:</label> <input name="id" id="id_enumerated" value="" required="required" placeholder="New id" type="text"/>
			</div>
		<?php
			}
			else {
		?>
			<input name="id" value="<?=$enumeratedElement->getId()?>" type="hidden"/>
		<?php
			}
		?>
		<table class="result_table enumerated">
			<thead>
				<tr>
					<th class="id">Id</th>
					<th class="description">Description</th>
					<th class="action"></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$n = 0;
				foreach ($enumeratedElement->getItems() as $key => $item) {
			?>
				<tr>
					<td><input type="text" name="element[<?=htmlspecialchars($key)?>][id]" value="<?=htmlspecialchars($key)?>" class="field text"/></td>
					<td><input type="text" name="element[<?=htmlspecialchars($key)?>][description]" value="<?=htmlspecialchars($item)?>" class="field text"/></td>
					<td>
						<label for="delete_element_<?=$n?>">Delete</label>
						<input name="element[<?=htmlspecialchars($key)?>][delete]" value="1" id="delete_element_<?=$n?>" type="checkbox"/>
					</td>
				</tr>
			<?php
					$n++;
				}
			?>
				<tr>
					<td><input type="text" name="element[<?=$enumeratedElement::EMPTY_ID?>][id]" value="" placeholder="New id" class="field text"/></td>
					<td><input type="text" name="element[<?=$enumeratedElement::EMPTY_ID?>][description]" value="" placeholder="New description" class="field text"/></td>
				</tr>
			</tbody>
		</table>
		<div class="actions">
			<input name="a" value="save" class="button publish" type="submit">
		</div>
	</form>
</main>