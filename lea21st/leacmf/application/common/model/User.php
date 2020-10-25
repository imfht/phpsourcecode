<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/19
 * Time: 13:02
 */

namespace app\common\model;

use think\Model;

class User extends Model
{

    protected $readonly = ['uuid', 'register_time', 'mobile'];

    const STATUS_FORBIDDEN = '0';  //状态：禁用
    const STATUS_ACTIVE    = '1';   //状态:正常
    const STATUS_DELETE    = '2';   //状态:删除

}