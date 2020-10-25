<?php

class RjAddress extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $province_id;

    /**
     *
     * @var integer
     */
    public $city_id;

    /**
     *
     * @var integer
     */
    public $county_id;

    /**
     *
     * @var string
     */
    public $detail;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $postcode;

    public $public;
    public function initialize(){
        $this->belongsTo('user_id','RjUser','id',array(
            'alias' => 'user'
        ));
        $this->hasMany('id','RjOrder','address_id',array(
            'alias' => 'orders'
        ));
    }
    /**
     * Independent Column Mapping.
     */

    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'province_id' => 'province_id', 
            'city_id' => 'city_id', 
            'county_id' => 'county_id', 
            'detail' => 'detail', 
            'phone' => 'phone', 
            'name' => 'name', 
            'postcode' => 'postcode',
            'public' => 'public'
        );
    }

}
