<?php

class RjHistory extends \Phalcon\Mvc\Model
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
    public $type;

    /**
     *
     * @var string
     */
    public $object_id;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var string
     */
    public $operator_id;

    /**
     *
     * @var string
     */
    public $date;

    /**
     * Independent Column Mapping.
     */
    public function initialize(){
        $this->belongsTo('operator_id','RjUser','id',array(
            'alias' => 'operator'
        ));
    }
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'type' => 'type', 
            'object_id' => 'object_id', 
            'content' => 'content', 
            'operator_id' => 'operator_id', 
            'date' => 'date'
        );
    }
}
