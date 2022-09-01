<main id="manageInstallation">
	<h2>Database indexes</h2>
    <pre><?=$collectionsIndexes?></pre>
    <form action="do_install.php" method="post">
        <input type="submit" name="a" value="Create DB indexes"/>
        -
        <input type="submit" name="a" value="Drop DB indexes"/>
    </form>
</main>