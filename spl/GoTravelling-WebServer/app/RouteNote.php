<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-6-2
 * Time: 下午3:58
 */
namespace App;

class RouteNote extends \Eloquent
{
    protected $table = 'routeNotes';

    protected $guarded = ['_id'];
}