<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<link rel="stylesheet" type="text/css" href="style/main.css"/>
	<link href="style/icon_16.png" rel="icon" />
	<link href="style/icon_128.png" sizes="128x128" rel="icon" />
	<title>Administración estructuras</title>
</head>
<body>
<header>
	<h1>ACD</h1>
</header>
<main>
	<h2>Identificación</h2>
	
	<form action="do_login.php" method="post" class="form_login">
		<p class="result"><?=$resultDesc?></p>
		<div class="text">
			<label for="login">Login</label>
			<input type="text" name="login" value="<?=htmlspecialchars('')?>" spellcheck="false" placeholder="e.g. tiranosaurus" id="login"/>
		</div>
		<div class="text">
			<label for="password">Password</label>
			<input type="password" name="password" value="<?=htmlspecialchars('')?>" placeholder="e.g. *******" id="password"/>
		</div>
		<div class="remember">
			<input type="checkbox" name="remember" id="remember" value="<?=('1')?>"/>
			<label for="remember">Remember</label>
		</div>
		<div class="submit"><input type="submit" name="a" value="Enter"/></div>
	</form>
</main>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>