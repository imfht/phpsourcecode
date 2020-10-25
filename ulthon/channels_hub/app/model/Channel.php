<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class Channel extends Model
{
    //
    use SoftDelete;

    protected $defaultSoftDelete = 0;

    public $typeName = [
        1=>'TCP',
        2=>'UDP',
    ];

    public $statusName = [
        0=>'正常',
        1=>'禁用'
    ];

    public function getStatusAttr($value)
    {
        return $this->statusName[$value];
    }

    public function setStatusAttr($value)
    {
        if(!is_numeric($value)){
            $value = array_search($value,$this->statusName);
        }

        return $value;
    }

    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function getTypeAttr($value)
    {
        return $this->typeName[$value];
    }

    public function getListenAddressAttr()
    {
        return strtolower($this->getAttr('type')).'://0.0.0.0:'.$this->getData('server_port');
    }


}
