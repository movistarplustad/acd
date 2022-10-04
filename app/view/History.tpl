<ul>
<?php
$n = 0;
foreach ($items as $item) {
	if ($n === $maxSizeItems)
		break;
	@$title = $item['title'];
	$selected = ($n === $positionCurrentItem) ? ' class="selected"' : '';
?>
	<li<?=$selected?>><a href="back.php?p=<?=$n?>"><?=$title?></a></li>
<?php
	$n++;
}
?>
</ul>