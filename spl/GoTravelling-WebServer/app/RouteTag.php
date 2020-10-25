<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-20
 * Time: 下午7:48
 */
namespace App;

class RouteTag extends \Eloquent
{

    protected $table = 'tags';

    protected $guarded = ['_id'];
}