<main id="manageStructure">
	<h2>Enumerated element <span class="structure_name"><?=$enumeratedElement->getId()?></span></h2>
	<p class="result <?=$resultCode?>"><?=$resultDesc?></p>
	<table class="result-table enumerated">
		<thead>
			<tr>
				<th>Id</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($enumeratedElement->getItems() as $key => $item) {
		?>
			<tr>
				<td><?=htmlspecialchars($key)?></td>
				<td><?=htmlspecialchars($item)?></td>
		<?php
			}
		?>
		</tbody>
	</table>
</main>