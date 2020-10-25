<?php
namespace Core;

/**
 * 本类为文件操作类，实现了文件的建立，写入，删除，修改，复制，移动，创建目录，删除目录
 * 列出目录里的文件等功能，路径后面别忘了加"/"
 *
 * @author 路人郝
 * @copyright myself
 * @link www.phpr.cn
 *
 */
use Core\Config;
use Phalcon\Exception;

class File
{
    public static $root = __DIR__ . '/../';

    /**
     * 本方法用来在path目录下创建name文件
     *
     * @param     string path
     * @param     string name
     */
    public static function create($path, $name)
    {
        $abPath = ROOT_DIR . $path;
        $abFile = $path . $name;
        if (!file_exists($abPath)) {
            self::mkdir($path);
        }
        if (file_exists($abFile)) {
            return '文件已经存在，创建失败';
        } else {
            if (file_exists($abPath)) {
                touch($name);
                return rename($name, $abFile);
            } else {
                return '创建失败，创建父级目录失败';
            }
        }
    }

    /*
     * 将远程文件下载至本地
     * @$url 远程文件url地址
     * @$path 将远程文件保存至何目录，默认放置下载临时目录
     */
    public static function saveUri($url, $path = '')
    {
        $config = Config::get('config');
        $fileName = basename($url);
        if (empty($path)) {
            $path = $config['dir']['downloadDir'];
        }
        return self::writeFile($path, $fileName, file_get_contents($url));
    }

    /*
     * 循环创建目录
     * @$path合法的目录
     */
    public static function mkdir($dir, $mod = 0777)
    {
        if (!is_string($dir)) {
            throw new Exception('参数错误');
        }
        $path = ROOT_DIR . $dir;
        if (file_exists($path)) {
            return true;
        }
        $dirArr = explode('/', trim($dir, '/'));
        $thePath = ROOT_DIR;
        foreach ($dirArr as $da) {
            $thePath = $thePath . $da . '/';
            if (!file_exists($thePath)) {
                $state = mkdir($thePath, $mod);
                if ($state == false) {
                    return $state;
                }
            }
        }
        if (!file_exists($path)) {
            return mkdir($path, $mod);
        }
        return true;
    }

    /**
     * 本方法用来写文件，向path路径下name文件写入content内容，bool为写入选项，值为1时
     * 接着文件原内容下继续写入，值为2时写入后的文件只有本次content内容
     *
     * @param     string_type path
     * @param     string_type name
     * @param     string_type content
     * @param     bool_type bool
     */
    public static function writeFile($path, $name, $content)
    {
        $abPath = ROOT_DIR . $path;
        $abFile = $abPath . $name;
        if (!file_exists($abPath)) {
            self::mkdir($path);
        }
        if (file_put_contents($abFile, $content)) {
            return true;
        } else {
            return '文件写入失败，可能没有写入权限';
        }
    }

    /*
     * 本方法列出dirName中的所有子目录，只包含下一级目录
     */
    public static function listDir($dirName)
    {
        $list = scandir(ROOT_DIR . $dirName);
        $dirList = array();
        foreach ($list as $l) {
            if ($l[0] != '.' && is_dir(ROOT_DIR . $dirName . $l)) {
                $dirList[$l] = $dirName . $l . '/';
            }
        }
        return $dirList;
    }

    public static function ll($dir)
    {
        $path = ROOT_DIR . $dir;
        $list = scandir($path);
        $output = array();
        foreach ($list as $file) {
            if ($file[0] != '.') {
                if (is_dir($path . '/' . $file)) {
                    $type = 'dir';
                    $fileArr = array('dir');
                } else {
                    $type = 'file';
                    $fileArr = explode('.', $file);
                }
                $output[] = array(
                    'path' => $dir . '/' . $file,
                    'type' => $type,
                    'fileType' => end($fileArr),
                    'url' => ltrim($dir . '/' . $file, 'public'),
                    'dir' => $dir,
                );
            }
        }
        return $output;
    }

    /**
     * 本方法删除path路径下name文件
     *
     * @param     string_type path
     * @param     string_type name
     */
    public static function rm($path, $r = false)
    {
        $absolutePath = ROOT_DIR . $path;
        if (!file_exists($absolutePath)) {
            return true;
        }
        if (is_dir($absolutePath) && $r) {
            if (self::clearDir($path) && rmdir($path)) {
                return true;
            } else {
                return '删除文件夹失败，可能是权限问题';
            }
        }
        if (unlink(ROOT_DIR . $path)) {
            return true;
        } else {
            return '删除失败';
        }
    }

    public static function clearCache($type = 'all')
    {
        $cacheDir = 'Web/' . WEB_CODE . '/cache/';
        $cacheList = self::listDir($cacheDir);
        $output = array();
        if ($type != 'all') {
            if (isset($cacheList[$type])) {
                if (self::clearDir($cacheDir . $type . '/') === true) {
                    return true;
                } else {
                    $outpu[] = '删除缓存：' . $type . '失败';
                }
            } else {
                $outpu[] = '缓存目录：' . $type . '不存在';
            }
        } else {
            foreach ($cacheList as $key => $value) {
                if (self::clearDir($value) === true) {
                    return true;
                } else {
                    $output[] = '缓存清除失败，请手动清除：' . $key;
                }
            }
        }
        if (empty($output)) {
            return true;
        } else {
            return $output;
        }
    }

    public static function clearDir($dir, $r = false)
    {
        //先删除目录下的文件：
        $output = array();
        $dh = opendir(ROOT_DIR . $dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = ROOT_DIR . $dir . "/" . $file;
                if (is_dir($fullpath) && $r) {
                    if (self::clearDir($dir . '/' . $file) === true) {
                        if (!@rmdir($fullpath)) {
                            $output[] = '删除 ' . $dir . "/" . $file . ' 失败';
                        }
                        unset($fullpath);
                    }
                } else {
                    if (!@unlink($fullpath)) {
                        $output[] = '删除 ' . $dir . "/" . $file . ' 失败';
                        break;
                    }
                }
            }
        }

        closedir($dh);
        if (empty($output)) {
            return true;
        } else {
            return $output;
        }
    }

    public static function cp($src, $des)
    {
        $abSrc = ROOT_DIR . $src;
        $abDes = ROOT_DIR . $des;
        $dir = opendir($abSrc);
        if (!file_exists($abDes)) {
            self::mkdir($des);
        }
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($abSrc . '/' . $file)) {
                    recurseCopy($abSrc . '/' . $file, $abDes . '/' . $file);
                } else {
                    copy($abSrc . '/' . $file, $abDes . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * 本方法把name文件从spath移动到path路径
     *
     * @param     string_type path
     * @param     string_type dirname
     */
    public function moveFile($filename, $dpath)
    {
        $output = array();
        $abFile = ROOT_DIR . $filename;
        $abDpath = ROOT_DIR . $dpath;
        if (file_exists($abFile)) {
            $result = rename($abFile, $abDpath);
            if ($result == false or !file_exists($dpath)) {
                return '文件移动失败';
            } else {
                return true;
            }
        }
        return '要移动的文件不存在';
    }
}
