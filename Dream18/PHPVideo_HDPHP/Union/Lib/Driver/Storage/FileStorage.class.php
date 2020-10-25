<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------
/**
 * 文件存储
 * @author hdxj <houdunwangxj@gmail.com>
 */
class FileStorage
{
    private $contents = array();

    /**
     * 储存内容
     * @param $fileName 文件名
     * @param $content 数据
     * @return bool
     */
    public function save($fileName, $content)
    {
        $dir = dirname($fileName);
        Dir::create($dir);
        if (file_put_contents($fileName, $content) === false) {
            halt("创建文件{$fileName}失败");
        }
        $this->contents[$fileName] = $content;
        return true;
    }

    /**
     * 获得
     * @param $fileName 文件名
     * @return bool|string
     */
    public function get($fileName)
    {
        if (isset($this->contents[$fileName])) {
            return $this->contents[$fileName];
        }
        if (!is_file($fileName)) {
            return false;
        }
        $content = file_get_contents($fileName);
        $this->contents[$fileName] = $content;
        return $content;
    }

}
