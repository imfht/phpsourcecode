<?php
namespace app\member\model;
use app\system\model\SystemModel;
/**
 * 文件上传操作
 */
class MemberFileModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'file_id',
    ];

}
