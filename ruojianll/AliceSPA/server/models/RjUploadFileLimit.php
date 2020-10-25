<?php

class RjUploadFileLimit extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $count;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'count' => 'count'
        );
    }

}
