<?php

class RjBanner extends \Phalcon\Mvc\Model
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
    public $title;

    /**
     *
     * @var string
     */
    public $subtitle;

    /**
     *
     * @var integer
     */
    public $type;

    /**
     *
     * @var string
     */
    public $upload_file_name;

    /**
     *
     * @var string
     */
    public $value;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'title' => 'title', 
            'subtitle' => 'subtitle', 
            'type' => 'type', 
            'upload_file_name' => 'upload_file_name', 
            'value' => 'value'
        );
    }

}
