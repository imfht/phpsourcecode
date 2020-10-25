<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class HttpChannel extends Model
{
    //

    use SoftDelete;

    protected $defaultSoftDelete = 0;

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

    public function getListenAddressAttr()
    {
        return 'http://0.0.0.0:'.config('channel.channel_server_port');
    }
}
