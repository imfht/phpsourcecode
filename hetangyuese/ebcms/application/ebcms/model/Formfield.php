<?php
namespace app\ebcms\model;

use think\Model;

class Formfield extends Model
{

    protected $pk = 'id';
    protected $type = [
        'config' => 'json',
    ];

    public function form()
    {
        return $this->belongsTo('Form');
    }
}