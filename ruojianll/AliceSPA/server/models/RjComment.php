<?php

class RjComment extends \Phalcon\Mvc\Model
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
    public $content;

    /**
     *
     * @var integer
     */
    public $creator_id;

    /**
     *
     * @var string
     */
    public $date;

    /**
     *
     * @var integer
     */
    public $rating;
    public  function initialize(){
        $this->belongsTo('product_id','RjProduct','id',array(
            'alias' => 'product'
        ));
        $this->belongsTo('creator_id','RjUser','id',array(
            'alias' => 'creator'
        ));
        $this->hasMany('id','RjCommentImage','comment_id',array(
            'alias' => 'images'
        ));
        $this->belongsTo('creator_id','RjUser','id',array(
            'alias' => 'creator'
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
            'content' => 'content', 
            'creator_id' => 'creator_id', 
            'date' => 'date', 
            'rating' => 'rating'
        );
    }
}
