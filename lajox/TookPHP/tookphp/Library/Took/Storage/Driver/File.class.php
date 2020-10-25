<?php
/**
 * 文件存储
 * @author lajox <lajox@19www.com>
 */
namespace Took\Storage\Driver;
class File extends \Took\Storage
{
    private $contents = array();

    /**
     * 储存内容
     * @param $filename 文件名
     * @param $content 数据
     * @return bool
     */
    public function save($filename, $content, $type='')
    {
        $dir = dirname($filename);
        \Tool\Dir::create($dir);
        if (file_put_contents($filename, $content) === false) {
            halt("创建文件{$filename}失败");
        }
        $this->contents[$filename] = $content;
        return true;
    }

    /**
     * 获得
     * @param string $filename 文件名
     * @param string $name  信息名 mtime或者content
     * @return bool|string
     */
    public function get($filename, $name='', $type='')
    {
        if (isset($this->contents[$filename])) {
            return $this->contents[$filename];
        }
        if (!is_file($filename)) {
            return false;
        }
        $name = !empty($name) ? $name : 'content';
        $info = array(
            'mtime' => filemtime($filename),
            'content' => file_get_contents($filename)
        );
        $this->contents[$filename] = $content = $info[$name];
        return $content;
    }

    /**
     * 文件追加写入
     * @access public
     * @param string $filename  文件名
     * @param string $content  追加的文件内容
     * @return boolean
     */
    public function append($filename, $content, $type=''){
        if(is_file($filename)){
            $content =  $this->get($filename).$content;
        }
        return $this->save($filename,$content);
    }

    /**
     * 文件删除
     * @access public
     * @param string $filename 文件名
     * @return boolean
     */
    public function unlink($filename, $type=''){
        unset($this->contents[$filename]);
        return is_file($filename) ? unlink($filename) : false;
    }

    /**
     * 文件是否存在
     * @access public
     * @param string $filename  文件名
     * @return boolean
     */
    public function has($filename, $type=''){
        return is_file($filename);
    }

    /**
     * 加载文件
     * @access public
     * @param string $filename  文件名
     * @param array $vars  传入变量
     * @return void
     */
    public function load($filename, $vars=null){
        if(!is_null($vars)){
            extract($vars, EXTR_OVERWRITE);
        }
        include $filename;
    }

}
