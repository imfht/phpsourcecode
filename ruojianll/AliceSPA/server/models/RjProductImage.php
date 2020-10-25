<?php

class RjProductImage extends \Phalcon\Mvc\Model
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
    public $product_id;

    /**
     *
     * @var string
     */
    public $upload_file_name;
    public function initialize(){
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
            'id' => 'id', 
            'product_id' => 'product_id', 
            'upload_file_name' => 'upload_file_name'
        );
    }

}
