<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Administración estructuras</title>
</head>
	<body>
	<h1>ACD</h1>
	<h2>Identificación</h2>
	
	<form action="login.php" method="post" class="form_login">
		<p class="result"><?=$resultDesc?></p>
		<label for="login">Login</label>
		<input type="text" name="login" value="<?=htmlspecialchars('')?>" spellcheck="false" placeholder="e.g. tiranosaurus" id="login"/>
		<label for="password">Password</label>
		<input type="password" name="password" value="<?=htmlspecialchars('')?>" placeholder="e.g. *******" id="password"/>
		<div>
			<input type="checkbox" name="remember" id="remember" value="<?=('1')?>"/>
			<label for="remember">Remember</label>
		</div>

		<span class="tools"><input type="submit" name="a" value="Enter"/></span>
	</form>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>