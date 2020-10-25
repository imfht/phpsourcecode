<?php

/*
 *  @author myf
 *  @date 2014-11-13 20:06:20
 *  @Description 用户
 */

namespace Minyifei\Model;
use Myf\Mvc\Model;


class User extends Model {
    
    public $id;
    //省份
    public $province;
    //城市
    public $city;
    //ip
    public $ip;
    //电信商
    public $isp;
    /**
     *创建时间
     * @var datetime Y-m-d H:i:s 
     */
    public $created;
    
    
}
