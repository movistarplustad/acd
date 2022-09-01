<?php

namespace Acd\Controller;

use \Acd\Model\Auth;
/* Clase encargada de hacer las redirecciones o cortar el acceso a nivel http a los diferentes puntos públicos */

class RolPermissionHttp
{
  public static function checkUserEditor($rolesAccepted)
  {
    if (!Auth::isLoged()) {
      header('Location: index.php?re=' . urlencode($_SERVER["REQUEST_URI"]));
      return false;
    }

    if (!in_array(Auth::getRol(), $rolesAccepted)) {
      header('HTTP/1.0 403 Forbidden');
      echo 'Unauthorized, only admin can show this section.';
      return false;
    }

    return true;
  }
}
