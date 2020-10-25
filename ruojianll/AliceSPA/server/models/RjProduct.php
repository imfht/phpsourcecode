<?php

class RjProduct extends \Phalcon\Mvc\Model
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
    public $category_id;

    /**
     *
     * @var integer
     */
    public $number;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $price;

    /**
     *
     * @var string
     */
    public $old_price;

    /**
     *
     * @var string
     */
    public $comment;
    public $public;
    public $summary;
    public $create_date;
    public $sold_number;
    public function initialize(){
        $this->hasMany('id',
            'RjProductImage', 'product_id',
            array(
                'alias' => 'images'
            ));
        $this->hasMany('id','RjComment','product_id',
            array(
                'alias' => 'comments'
            ));
        $this->belongsTo('category_id','RjCategory','id',array(
            'alias' => 'category'
        ));
        $this->hasManyToMany('id',
            'RjStoryProduct','product_id','story_id',
            'RjStory','id',
            array(
                'alias' => 'stories'
            ));
    }
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'category_id' => 'category_id', 
            'number' => 'number', 
            'name' => 'name', 
            'price' => 'price', 
            'old_price' => 'old_price', 
            'comment' => 'comment',
            'public' => 'public',
            'summary' => 'summary',
            'create_date' => 'create_date',
            'sold_number' => 'sold_number'
        );
    }
}
