<?php

class RjOrder extends \Phalcon\Mvc\Model
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
    public $state;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $post_id;

    /**
     *
     * @var string
     */
    public $post_company;



    /**
     *
     * @var integer
     */
    public $operator_id;

    /**
     *
     * @var string
     */
    public $date;

    /**
     *
     * @var integer
     */
    public $history;

    /**
     *
     * @var integer
     */
    public $public;

    public $address_id;
    /**
     * Independent Column Mapping.
     */
    public $from_id;
    public $expect_pay;
    public $trad_id;
    public  function  initialize(){
        $this->belongsTo('user_id','RjUser','id',array(
            'alias' => 'user'
        ));
        $this->belongsTo('address_id','RjAddress','id',array(
            'alias' => 'address'
        ));
        $this->hasMany('id','RjOrderItem','order_id',array(
            'alias' => 'orderItems'
        ));
    }
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'state' => 'state', 
            'user_id' => 'user_id', 
            'post_id' => 'post_id', 
            'post_company' => 'post_company',
            'operator_id' => 'operator_id', 
            'date' => 'date', 
            'history' => 'history', 
            'public' => 'public',
            'address_id' => 'address_id',
            'from_id' => 'from_id',
            'expect_pay' => 'expect_pay',
            'trad_id' => 'trad_id'
        );
    }
}
