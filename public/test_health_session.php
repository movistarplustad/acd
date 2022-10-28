<?php
//Cuando se pide por get devuelve el valor de la session
// y se le envia el valor por post
session_start();
if (isset($_POST['valorSession'])) {
    $_SESSION['MiSession'] = $_POST['valorSession'];
    echo $_POST['valorSession'];
} else {
    if (isset($_SESSION['MiSession'])){
        echo $_SESSION['MiSession'];
    }
}
