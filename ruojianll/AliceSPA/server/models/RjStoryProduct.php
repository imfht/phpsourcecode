<?php

class RjStoryProduct extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $story_id;

    /**
     *
     * @var integer
     */
    public $product_id;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'story_id' => 'story_id', 
            'product_id' => 'product_id'
        );
    }

}
