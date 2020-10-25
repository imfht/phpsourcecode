<?php

class RjCategory extends \Phalcon\Mvc\Model
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
    public $name;

    /**
     *
     * @var string
     */
    public $upload_file_name;
    public $public;
    public  function  initialize(){
        $this->hasMany('id','RjProduct','category_id',array(
            'alias' => 'products'
        ));
    }
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'name' => 'name',
            'upload_file_name' => 'upload_file_name',
            'public' => 'public'
        );
    }

}
