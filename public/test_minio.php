<?php
//Establecer para cada entorno la configuración.
if (file_exists('../config/conf.php')) {
    require '../config/conf.php';
}

//phpinfo()
use Acd\Lib\StorageSystemFactory;
use Acd\Lib\StorageSystemException;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToDeleteFile;

$storageBinaryAdapter = getenv('ACD_DATA_CONTENT_STORAGE_ADAPTER');
$filesystem = StorageSystemFactory::getFilesystem($storageBinaryAdapter);
$path = 'fichero';
echo "<p>Sistema de almacenamiento $storageBinaryAdapter</p>";
echo "<p>Ruta $path</p>";

if(($_POST['action'] ?? null) === 'save') {
    try {
        $isImage = isset($_POST['contentIsImage']);
        $config = [
            'mimetype' => 'text/plain'
        ];
        if($isImage) {
            $contents = file_get_contents('https://estatico.emisiondof6.com/recorte/m-DP/wpepg/CPCOLE');
            $filesystem->write($path, $contents);
        }
        else {
            $contents = $_POST['contents'] ?? 'Guardado';
            $config = [
                'mimetype' => 'text/plain'
            ];
            $filesystem->write($path, $contents, $config);
        }
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
    $lastModified = $filesystem->lastModified($path);
    try {
         $mimeType = $filesystem->mimeType($path);
    } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
        $mimeType = '?';
    }
    var_dump("ok read", $mimeType, date('Y/m/j H:i:s e', $lastModified));
    $isImage = substr($mimeType, 0, 6) === 'image/';
    if($isImage) {
        echo "<img src='data:$mimeType;base64, ".base64_encode($response)."' alt='Image'/>";
    }
    else {
        var_dump($response);
    }
} catch (FilesystemException | UnableToReadFile $exception) {
    // handle the error
    var_dump("fail", $exception->getMessage());
}

$contents = 'Generado a las '.date('H:i:s e');
echo "<form method='POST'><input type='submit' name='action' value='delete'/></form>";
echo "<form method='POST'><input type='text' name='contents' value='$contents'/><label>Guardar imagen <input type='checkbox' name='contentIsImage' value='1'/></label><input type='submit' name='action' value='save'/></form>";
