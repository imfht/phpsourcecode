<?php
/**
 * HerosPHP 文件操作工具类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */
namespace herosphp\files;

class FileUtils {

	/**
	 * 创建多层文件目录
	 * @param 	string 		$path			需要创建路径
	 * @return 	boolean     成功时返回true，失败则返回false;
	 */
	public static function makeFileDirs($path) {
        //必须考虑 "/" 和 "\" 两种目录分隔符
        $files = preg_split('/[\/|\\\]/s', $path);
        $_dir = '';
        foreach ($files as $value) {
            $_dir .= $value.DIRECTORY_SEPARATOR;
            if ( !file_exists($_dir) ) {
                mkdir($_dir);
            }
        }
        return true;
	}

	/**
     * 获取文件后缀名称
     * @param string  	文件名
     * @return string
	 */
	public static function getFileExt($filename) {
		$_pos = strrpos( $filename, '.' );
		return strtolower( substr( $filename , $_pos+1 ) );
	}

    /**
     * 递归删除文件夹
     * @param $dir
     * @return boolean
     */
    public static function removeDirs($dir) {

        $handle = opendir($dir);
        //删除文件夹下面的文件
        while ( $file=readdir($handle) ) {
            if( $file != "." && $file != ".." ) {
                $filename = $dir."/".$file;
                if( !is_dir($filename) ) {
                    @unlink($filename);
                } else {
                    self::removeDirs($filename);
                }
            }
        }
        closedir($handle);

        //删除当前文件夹
        if( rmdir($dir) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 拷贝目录
     * @param $src 源文件
     * @param $dst 目标文件
     * @return boolean
     */
    public static function copyDir($src, $dst) {
        if ( is_file($src) ) {  //如果是文件，则直接拷贝
            return copy($src, $dst);
        }
        @mkdir($dst);   //创建目标目录
        $handle = opendir($src);
        if ( $handle !== false ) {
            while( ($filename = readdir($handle)) ) {

                if ( $filename == '.'  || $filename == '..' ) continue;
                $fileSrc = $src.'/'.$filename;
                $fileDst = $dst.'/'.$filename;
                if ( is_dir($fileSrc) ) {
                    self::copyDir($fileSrc, $fileDst);
                } else {
                    copy($fileSrc, $fileDst);
                }

            }
        }
        closedir($src);
    }

    /**
     * 判断一个目录是否为空
     * @param $dirName
     * @return boolean
     */
    public static function isEmptyDir($dirName) {
        $handle = opendir($dirName);
        if ( $handle != FALSE ) {
            while ( ($filename = readdir($handle)) != false  ) {
                if ( $filename != '.' && $filename != '..' )
                    return false;
            }
        }
        closedir($handle);
        return true;
    }

    /**
     * 遍历目录，返回目录文件相对路径
     * @param $dir
     * @return array
     */
    public static function dirTraversal($dir) {
        $files = array();
        self::getDirFiles($dir, '', $files);
        return $files;
    }

    /**
     * 获取目录文件
     * @param $absolute_dir 目录绝对路径
     * @param $relative_dir 目录相对路径
     * @param $files
     */
    private static function getDirFiles($absolute_dir, $relative_dir, &$files) {
        $handler = opendir($absolute_dir);
        if ( $handler != false ) {
            while ( $filename = readdir($handler) ) {
                if ( $filename != "." && $filename != ".." ) {
                    if ( is_dir($absolute_dir."/".$filename) ) {
                        self::getDirFiles($absolute_dir."/".$filename, $relative_dir.$filename."/", $files);
                    } else {
                        $files[] = $relative_dir.$filename;
                    }
                }
            }
            closedir($handler);
        }
    }

    /**
     * get format filesize string(获取格式化文件大小字符串)
     * @param 	int			$size
     * @return  string 		$size_str
     */
    public static function formatFileSize($size) {
        if ( $size/1024 < 1 ) {
            return $size ." B";
        } else if ( $size/1024 > 1 && $size/(1024*1024) < 1 ) {
            return number_format($size/1024, 2, '.', '') .'KB';
        } else if ( $size/(1024*1024) > 1 && $size/(1024*1024*1024)< 1 ) {
            return number_format($size/(1024*1024), 2, '.', '') ." MB";
        } else {
            return number_format($size/(1024*1024*1024), 2, '.', '')." GB";
        }
    }
}
