<?php

class RjStoryImage extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $story_id;

    /**
     *
     * @var string
     */
    public $upload_file_name;

    /**
     * Independent Column Mapping.
     */
    public function initialize(){
        $this->belongsTo('story_id','RjStory','id',array(
            'alias' => 'story'
        ));
    }
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'story_id' => 'story_id', 
            'upload_file_name' => 'upload_file_name'
        );
    }

}
