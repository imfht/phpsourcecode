<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 3:33 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 公共model
 * Class AdminUser
 * @package App\Models
 */
class BaseModels extends Model
{
    /**
     * 为数组/ JSON序列化准备一个日期。
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
