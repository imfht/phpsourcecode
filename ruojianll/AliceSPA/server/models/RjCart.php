<?php

class RjCart extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $product_id;

    /**
     *
     * @var integer
     */
    public $number;
    public $price;
    public function initialize(){
        $this->belongsTo('user_id','RjUser','id',array(
            'alias' => 'user'
        ));
        $this->belongsTo('product_id','RjProduct','id',array(
            'alias' => 'product'
        ));
    }
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'user_id' => 'user_id', 
            'product_id' => 'product_id', 
            'number' => 'number',
            'price' => 'price'
        );
    }

}
