<?php
/**
 * 应用安装
 */
namespace app\system\event;

class Install{

    /**
     * @param $dir 路径
     * @param $except 排除项
     * @return array
     * 搜索给定地址下目录列表
     */
    public static function getDir($dir,$except){
        $dirArray[]=NULL;
        if(false != ($handle = opendir($dir))){
            $i=0;
            while(false !== ($file = readdir($handle))) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if (array_search($file,$except) === false && $file != ".htaccess" && $file != "." && $file != ".."&&!strpos($file,".")){
                    $dirArray[$i]=$file;
                    $i++;
                }
            }
            //关闭句柄
            closedir($handle);
        }
        return $dirArray;
    }

    /**
     * 读取sql文件为数组
     * @param $sqlFile sql 文件路径
     * @param string $prefix 添加表前缀
     * @return array|bool
     */
    public static function get_sql_array($sqlFile,$prefix = ''){
        $sql = file_get_contents($sqlFile);
        $str = preg_replace('/(--.*)|(\/\*(.|\s)*?\*\/)|(\n)/', '',$sql);
        if(!empty($prefix)){
            $str = str_replace('ai_',$prefix,$str);
        }
        $list = explode(';',trim($str));
        foreach ($list as $key => $val) {
            if (empty($val)) {
                unset($list[$key]);
            } else {
                $list[$key] .= ';';
            }
        }
        return array_values($list);
    }
}