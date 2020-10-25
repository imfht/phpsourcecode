<?php

class RjOrderItem extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $product_id;

    /**
     *
     * @var integer
     */
    public $order_id;

    /**
     *
     * @var integer
     */
    public $number;

    /**
     *
     * @var string
     */
    public $message;
    public $price;
    /**
     * Independent Column Mapping.
     */
    public  function initialize(){
        $this->belongsTo('product_id','RjProduct','id',array(
            'alias' => 'product'
        ));
        $this->belongsTo('order_id','RjOrder','id',array(
            'alias' => 'order'
        ));
    }
    public function columnMap()
    {
        return array(
            'product_id' => 'product_id', 
            'order_id' => 'order_id', 
            'number' => 'number', 
            'message' => 'message',
            'price' => 'price'
        );
    }

}
