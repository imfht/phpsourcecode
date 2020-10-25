<?php

class RjCommentImage extends \Phalcon\Mvc\Model
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
    public $comment_id;

    /**
     *
     * @var string
     */
    public $upload_file_name;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'comment_id' => 'comment_id', 
            'upload_file_name' => 'upload_file_name'
        );
    }

}
