<?php

class RjUploadFile extends \Phalcon\Mvc\Model
{



    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $app_type;

    /**
     *
     * @var string
     */
    public $mime_type;

    /**
     *
     * @var string
     */
    public $name;

    public $refrence_count;
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'user_id' => 'user_id', 
            'app_type' => 'app_type', 
            'mime_type' => 'mime_type', 
            'name' => 'name',
            'refrence_count' => 'refrence_count'
        );
    }

}
