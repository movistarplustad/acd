<?php
//Establecer para cada entorno la configuración.
if (file_exists('../config/conf.php')) {
    require '../config/conf.php';
}


use Acd\Lib\StorageSystemFactory;
use Acd\Lib\StorageSystemException;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToDeleteFile;

$storageBinaryAdapter = getenv('ACD_DATA_CONTENT_STORAGE_ADAPTER');
$filesystem = StorageSystemFactory::getFilesystem($storageBinaryAdapter);
$path = 'f';
echo "<p>Sistema de almacenamiento $storageBinaryAdapter</p>";
echo "<p>Ruta $path</p>";
//$contents = file_get_contents('https://estatico.emisiondof6.com/recorte/m-DP/wpepg/CPCOLE');

if(($_POST['action'] ?? null) === 'save') {
    try {
        $contents = $_POST['contents'] ?? 'Guardado';
        $filesystem->write($path, $contents);
        var_dump("ok save");
    } catch (FilesystemException | UnableToWriteFile $exception) {
        // handle the error
        var_dump("fail", $exception->getMessage());
    }
}

if(($_POST['action'] ?? null) === 'delete') {
    try {
        $filesystem->delete($path);
        var_dump("ok delete", $path);
    } catch (FilesystemException | UnableToDeleteFile $exception) {
        // handle the error
        var_dump("fail", $exception->getMessage());
    }
}

try {
    $response = $filesystem->read($path);
    var_dump("ok read", $response);
} catch (FilesystemException | UnableToReadFile $exception) {
    // handle the error
    var_dump("fail", $exception->getMessage());
}

$contents = 'Generado a las '.date('H:i:s e');
echo "<form method='POST'><input type='submit' name='action' value='delete'/></form>";
echo "<form method='POST'><input type='text' name='contents' value='$contents'/><input type='submit' name='action' value='save'/></form>";
