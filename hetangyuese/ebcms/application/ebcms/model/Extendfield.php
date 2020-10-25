<?php
namespace app\ebcms\model;

use think\Model;

class Extendfield extends Model
{

    protected $pk = 'id';
    protected $type = [
        'config' => 'json',
    ];

    public function extend()
    {
        return $this->belongsTo('Extend');
    }
}