<?php

class RjStory extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $content;
    public $creator_id;
    public $public;
    public function initialize(){
        $this->belongsTo('creator_id','RjUser','id',array(
            'alias' => 'creator'
        ));
        $this->hasMany('id','RjStoryImage','story_id',array(
            'alias' => 'images'
        ));
        $this->hasManyToMany('id',
            'RjStoryProduct','story_id','product_id',
            'RjProduct','id',
            array(
                'alias' => 'products'
            )
        );
    }
    /**
     * Independent Column Mapping.
     */

    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'title' => 'title', 
            'content' => 'content',
            'creator_id' => 'creator_id',
            'public' => 'public'
        );
    }

}
