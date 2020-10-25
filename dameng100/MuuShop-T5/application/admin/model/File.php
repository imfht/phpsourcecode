<?php
namespace app\admin\model;

use think\Model;
use think\Upload;

/**
 * 文件模型
 * 负责文件的下载和上传
 */

class File extends Model{

    /**
     * 文件模型字段映射
     * @var array
     */
    protected $_map = array(
        'type' => 'mime',
    );



}
