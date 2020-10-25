<?php
namespace app\ebcms\model;

use think\Model;

class Oplog extends Model
{

    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $type = [
        'request' => 'serialize',
    ];

    public function manager()
    {
        return $this->belongsTo('Manager', 'manager_id', 'id', '', 'LEFT');
    }

}