<?php
namespace Modules\File\Forms\Element;

use Core\Config;
use Phalcon\Forms\Element\File as Pfile;
use Modules\File\Library\FileHandle;

class File extends Pfile
{
    protected $config = array();
    /*
     * @max 最大尺寸
     * @min 最小尺寸
     * @type array 文件类型
     * @access 文件权限
     * @dir 文件保存路径
     * @limit 文件数量
     */
    public function setConfig($config=array()){
        $config = array_merge(array(
            'value' => 'path',
            'valueType' => 'string'
        ),$config);
        $this->config = $config;
    }
    public function getConfig(){
        return $this->config;
    }
    public function getValue()
    {
        $value = array();
        $output = FileHandle::upload($this->config);
        foreach($output['success'] as $o){
            switch($this->config['value']){
                case 'id':
                    $value[] = $o['id'];
                    break;
                case 'url':
                    $value[] = $o['url'];
                    break;
                case 'path':
                    $value[] = $o['path'];
                    break;
            }
        }
        if($this->config['valueType'] == 'string'){
            $value = implode(';',$value);
        }
        return $value;
    }
}
