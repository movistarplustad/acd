<!-- TODO -->
<input type="hidden" name="field[<?=$id?>][id]" value="<?=htmlspecialchars($fieldName)?>"/>
<input type="hidden" name="field[<?=$id?>][name]" value="<?=htmlspecialchars($fieldName)?>"/>
<label for="field_<?=$id?>"><?=htmlspecialchars($fieldName)?></label>
<input type="text" name="field[<?=$id?>][value]" value="<?=htmlspecialchars($fieldRef)?>" id="field_<?=$id?>" readonly="readonly"/>
<a href="do_edit_content.php?id=<?=htmlspecialchars($fieldRef)?>&amp;parent=<?=htmlspecialchars('$fieldRef')?>&amp;field=<?=htmlspecialchars('$fieldRef')?>">Editar</a>