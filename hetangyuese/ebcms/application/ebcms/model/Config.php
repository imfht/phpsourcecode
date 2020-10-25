<?php
namespace app\ebcms\model;

use think\Model;

class Config extends Model
{

    protected $pk = 'id';
    protected $type = [
        'config' => 'json',
    ];

    public function configcate()
    {
        return $this->belongsTo('Configcate');
    }
}