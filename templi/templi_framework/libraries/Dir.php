<?php 
/**
 * TempLi 共共函数库 目录
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-1-20
 */

namespace framework\libraries;


class Dir
{
    /**
     * 转化 \ 为 /
     *
     * @param	string	$path	路径
     * @return	string	路径
     */
    public static function dirPath($path) {
        $path = str_replace('\\', '/', $path);
        if(substr($path, -1) != '/') $path = $path.'/';
        return $path;
    }

    /**
     * 创建目录
     *
     * @param string $path 路径
     * @param int|string $mode 属性
     * @return string    如果已经存在则返回true，否则为flase
     */
    public static function dirCreate($path, $mode = 0777) {
        if(is_dir($path)) return true;
        $path = self::dirPath($path);
        $tmp = explode('/', $path);
        $cur_dir = '';
        $max = count($tmp) - 1;
        for($i=0; $i<$max; $i++) {
            $cur_dir .= $tmp[$i].'/';
            if (@is_dir($cur_dir)) {
                continue;
            }
            @mkdir($cur_dir, $mode,true);
            @chmod($cur_dir, $mode);
        }
        return is_dir($path);
    }
    /**
     * 拷贝目录及下面所有文件
     *
     * @param	string	$fromDir	原路径
     * @param	string	$toDir		目标路径
     * @return	string	如果目标路径不存在则返回false，否则为true
     */
    function dirCopy($fromDir, $toDir) {
        $fromDir = self::dirPath($fromDir);
        $toDir = self::dirPath($toDir);
        if (!is_dir($fromDir)) return false;
        if (!is_dir($toDir)) self::dirCreate($toDir);
        $list = glob($fromDir.'*');
        if (!empty($list)) {
            foreach($list as $v) {
                $path = $toDir.basename($v);
                if(is_dir($v)) {
                    self::dirCopy($v, $path);
                } else {
                    copy($v, $path);
                    @chmod($path, 0777);
                }
            }
        }
        return TRUE;
    }
    /**
     * 转换目录下面的所有文件编码格式
     *
     * @param	string	$in_charset		原字符集
     * @param	string	$out_charset	目标字符集
     * @param	string	$dir			目录地址
     * @param	string	$fileExts		转换的文件格式
     * @return	string	如果原字符集和目标字符集相同则返回false，否则为true
     */
    public static function dirIconv($in_charset, $out_charset, $dir, $fileExts = 'php|html|htm|shtml|shtm|js|txt|xml') {
        if($in_charset == $out_charset) return false;
        $list = self::dirList($dir);
        foreach($list as $v) {
            if (pathinfo($v, PATHINFO_EXTENSION) == $fileExts && is_file($v)){
                file_put_contents($v, iconv($in_charset, $out_charset, file_get_contents($v)));
            }
        }
        return true;
    }
    /**
     * 列出目录下所有文件
     *
     * @param	string	$path		路径
     * @param	string	$exts		扩展名
     * @param	array	$list		增加的文件列表
     * @return	array	所有满足条件的文件
     */
    public static function fileList($path, $exts = '', $list= array()) {
        $path = self::dirPath($path);
        $files = glob($path.'*');
        foreach($files as $v) {
            if (is_dir($v)) {
                $list[] = $v;
                $list = self::fileList($v, $exts, $list);
            } elseif (!$exts || pathinfo($v, PATHINFO_EXTENSION) == $exts) {
                $list[] = $v;
            }
        }
        return $list;
    }
    /**
     * 列出目录下所有文件
     *
     * @param	string	$path		路径
     * @param	string	$exts		扩展名要列出的文件扩展名
     * @param	array	$list		增加的文件列表
     * @return	array	所有满足条件的文件
     */
    public static function fileList2($path, $exts = '', $list= array()) {
        $path = self::dirPath($path);
        $files = scandir($path);
        foreach($files as $v) {
            if($v == '.' || $v == '..') {
                continue;
            }
            if (is_dir($path.$v)) {
                $list[] = $path.$v;
                $list = self::fileList2($path.$v, $exts, $list);
            }elseif(!$exts || pathinfo($v, PATHINFO_EXTENSION) == $exts) {
                $list[] = $path.$v;
            }
        }
        return $list;
    }
    /**
     * 目录列表
     *
     * @param	string	$dir		路径
     * @param	int		$parentid	父id
     * @param	array	$dirs		传入的目录
     * @return	array	返回目录列表
     */
    public static function dirList($dir, $parentid = 0, $dirs = array()) {
        static $id;
        if ($parentid == 0) $id = 0;
        $list = glob($dir.'*');
        foreach($list as $v) {
            if (is_dir($v)) {
                $id++;
                $dirs[$id] = array('id'=>$id,'parent_id'=>$parentid, 'name'=>basename($v), 'dir'=>$v.'/');
                $dirs = self::dirList($v.'/', $id, $dirs);
            }
        }
        return $dirs;
    }
    /**
     * 设置目录下面的所有文件的访问和修改时间
     *
     * @param	string	$path		路径
     * @param	int		$mtime		修改时间
     * @param	int		$atime		访问时间
     * @return	array	不是目录时返回false，否则返回 true
     */
    public static function dirTouch($path, $mtime = TIME, $atime = TIME) {
        if (!is_dir($path)) return false;
        $path = self::dirPath($path);
        if (!is_dir($path)) touch($path, $mtime, $atime);
        $files = glob($path.'*');
        foreach($files as $v) {
            is_dir($v) ? self::dirTouch($v, $mtime, $atime) : touch($v, $mtime, $atime);
        }
        return true;
    }
    /**
     * 获取和目录结构类似的文件树形数组 （树形多维数组）
     * @param string $path
     * @param string $exts 要显示的文件的后缀
     * @return array
     */
    public static function dirTree($path, $exts = '')
    {
        $path = self::dirPath($path);
        $files = scandir($path);
        $dir_tree =array();
        foreach ($files as $key=>$val){
            if($val == '..' || $val == '.'){
                continue;
            }
            if(is_dir($path.$val)){
                $dir_tree[$val] = self::dirTree($path.$val, $exts);
            } elseif(!$exts || pathinfo($path.$val, PATHINFO_EXTENSION) == $exts) {
                $dir_tree[] = $val;
            }
        }
        return $dir_tree;
    }
    /**
     * 删除目录及目录下面的所有文件
     *
     * @param	string	$dir		路径
     * @return	bool	如果成功则返回 TRUE，失败则返回 FALSE
     */
    public static function dirDelete($dir) {
        $dir = self::dirPath($dir);
        if (!is_dir($dir)) return FALSE;
        $list = glob($dir.'*');
        foreach($list as $v) {
            is_dir($v) ? self::dirDelete($v) : @unlink($v);
        }
        return @rmdir($dir);
    }
}