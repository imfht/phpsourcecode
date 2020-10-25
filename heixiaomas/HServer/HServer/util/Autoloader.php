<?php

class Autoloader
{

    public static function RegisterPsr4($baseNS, $baseDir)
    {
        $baseDir = realpath($baseDir);
        if (!$baseDir) {
            throw new Exception('Invalid basedir');
        }
        spl_autoload_register(
            function ($class) use ($baseNS, $baseDir) {
                $parts = explode('\\', $class);
                if ($parts[0] !== $baseNS) return; // not mine;
                array_shift($parts);
                if (sizeof($parts) === 0) return; // need at least one part besides base
                $file = $baseDir . DIRECTORY_SEPARATOR;
                $file .= implode(DIRECTORY_SEPARATOR, $parts);
                $file .= '.php';
                if (file_exists($file)) {
                    require($file);
                }
            });
    }


    public static function RegisterDir($dirs)
    {

        if (!is_array($dirs)) $dirs = [$dirs];

        foreach ($dirs as &$dir) {
            $dir = realpath($dir);
            if (!$dir) {
                throw new Exception('Invalid autoload dir');
            }
        }

        spl_autoload_register(
            function ($class) use ($dirs) {

                foreach ($dirs as $dir) {
                    $file = $dir . DIRECTORY_SEPARATOR . $class . '.php';
                    if (file_exists($file)) {
                        require($file);
                        return;
                    }
                }

            }
        );
    }
}