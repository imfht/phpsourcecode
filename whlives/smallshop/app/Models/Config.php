<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/5
 * Time: 上午10:04
 */

namespace App\Models;

/**
 * 系统设置
 * Class Config
 * @package App\Models
 */
class Config extends BaseModels
{
    protected $table = 'config';
    protected $guarded = ['id'];
}
