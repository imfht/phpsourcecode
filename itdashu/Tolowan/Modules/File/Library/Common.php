<?php
namespace Modules\File\Library;

use Core\Config;
use Core\File;
use Modules\File\Models\File as Mfile;

class Common
{
    public static function savePrivateUp()
    {
        global $di;
        $output = array();
        $config = Config::get('config');
        $basedir = $config['dir']['rootDir'] . 'file/images/';
        if ($di->getShared('request')->hasFiles()) {
            foreach ($di->getShared('request')->getUploadedFiles() as $file) {
                $dir = date('Y/m/d');
                File::mkdir('file/images/' . $dir);
                $dir = $dir . '/';
                //echo $file->getName() . '<br>';
                //echo end(explode('.', $file->getName()));
                $thisFile = explode('.', $file->getName());
                $fileName = time() . '.' . end($thisFile);
                if ($file->moveTo($basedir . $dir . $fileName)) {
                    $fileModel = new Mfile();
                    $fileModel->uid = $di->getShared('user')->uid;
                    $fileModel->path = $dir . $fileName;
                    $fileModel->created = time();
                    if ($fileModel->save()) {
                        $output[] = $fileModel->id;
                    } else {
                        unlink($basedir . $dir . $fileName);
                    }
                }
            }
        }
        return $output;
    }
}
