<?php

namespace Utility;

class File
{
    /**
     * @param $dirPath
     * @return bool
     */
    public static function createDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            try {
                return mkdir($dirPath, 0755, true);
            } catch (\Exception $e) {
            }
        }

        return true;
    }

    /**
     * @param $dirPath
     * @return bool
     */
    public static function delDir($dirPath)
    {
        if (self::clearDir($dirPath)) {
            try {
                return rmdir($dirPath);
            } catch (\Exception $e) {
            }
        }

        return false;
    }

    /**
     * @param $dirPath
     * @return bool
     */
    public static function clearDir($dirPath)
    {
        if (!is_dir($dirPath)) return false;
        try {
            $dirHandle = opendir($dirPath);
            if (!$dirHandle) return false;
            while (false !== ($file = readdir($dirHandle))) {
                if ($file == '.' || $file == '..') continue;
                if (!is_dir($dirPath . "/" . $file)) {
                    if (!self::delFile($dirPath . "/" . $file)) {
                        closedir($dirHandle);
                        return false;
                    }
                } else {
                    if (!self::delDir($dirPath . "/" . $file)) {
                        closedir($dirHandle);
                        return false;
                    }
                }
            }
            closedir($dirHandle);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param $dirPath
     * @param $targetPath
     * @param bool $overwrite
     * @return bool
     */
    public static function copyDir($dirPath, $targetPath, $overwrite = true)
    {
        if (!is_dir($dirPath)) return false;
        if (!file_exists($targetPath)) {
            if (!self:: createDir($targetPath)) return false;
        }
        try {
            $dirHandle = opendir($dirPath);
            if (!$dirHandle) return false;
            while (false !== ($file = readdir($dirHandle))) {
                if ($file == '.' || $file == '..') continue;
                if (!is_dir($dirPath . "/" . $file)) {
                    if (!self::copyFile($dirPath . "/" . $file, $targetPath . "/" . $file, $overwrite)) {
                        closedir($dirHandle);
                        return false;
                    }
                } else {
                    if (!self::copyDir($dirPath . "/" . $file, $targetPath . "/" . $file, $overwrite)) {
                        closedir($dirHandle);
                        return false;
                    };
                }
            }
            closedir($dirHandle);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param $dirPath
     * @param $targetPath
     * @param bool $overwrite
     * @return bool
     */
    public static function moveDir($dirPath, $targetPath, $overwrite = true)
    {
        try {
            if (self::copyDir($dirPath, $targetPath, $overwrite))
                return self::delDir($dirPath);
        } catch (\Exception $exception) {
        }

        return false;
    }

    /**
     * @param $filePath
     * @param bool $overwrite
     * @return bool
     */
    public static function createFile($filePath, $overwrite = true)
    {
        if (file_exists($filePath) && $overwrite == false)
            return false;
        elseif (file_exists($filePath) && $overwrite == true)
            if (!self::delFile($filePath)) return false;

        $aimDir = dirname($filePath);
        if (self::createDir($aimDir)) {
            try {
                return touch($filePath);
            } catch (\Exception $exception) {
            }
        }

        return false;
    }

    /**
     * @param $filePath
     * @param $content
     * @param bool $overwrite
     * @return bool|int
     */
    public static function saveFile($filePath, $content, $overwrite = true)
    {
        if (self::createFile($filePath, $overwrite))
            return file_put_contents($filePath, $content);

        return false;
    }

    /**
     * @param $filePath
     * @param $targetFilePath
     * @param bool $overwrite
     * @return bool
     */
    public static function copyFile($filePath, $targetFilePath, $overwrite = true)
    {
        if (!file_exists($filePath)) return false;

        if (file_exists($targetFilePath) && $overwrite == false) {
            return false;
        } elseif (file_exists($targetFilePath) && $overwrite == true) {
            if (!self::delFile($targetFilePath)) return false;
        }
        $aimDir = dirname($filePath);
        if (!self::createDir($aimDir))
            return false;

        return copy($filePath, $targetFilePath);
    }

    /**
     * @param $filePath
     * @param $targetFilePath
     * @param bool $overwrite
     * @return bool
     */
    public static function moveFile($filePath, $targetFilePath, $overwrite = true)
    {
        if (!file_exists($filePath)) return false;
        if (file_exists($targetFilePath) && $overwrite == false) {
            return false;
        } elseif (file_exists($targetFilePath) && $overwrite == true) {
            if (!self::delFile($targetFilePath)) return false;
        }
        $targetDir = dirname($targetFilePath);
        if (!self:: createDir($targetDir)) return false;

        return rename($filePath, $targetFilePath);
    }

    /**
     * @param $filePath
     * @return bool
     */
    public static function delFile($filePath)
    {
        try {
            unlink($filePath);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}