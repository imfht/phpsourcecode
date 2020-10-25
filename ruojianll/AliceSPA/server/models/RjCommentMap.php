<?php

class RjCommentMap extends \Phalcon\Mvc\Model
{
    public $id;
    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var
     */
    public $product_id;

    /**
     *
     * @var integer
     */
    public $upload_file_limit_id;
    public function initialize(){
        $this->belongsTo('user_id','RjUser','id',array(
            'alias' => 'user'
        ));
        $this->belongsTo('product_id','RjProduct','id',array(
            'alias' => 'product'
        ));
        $this->hasOne('upload_file_limit_id','RjUploadFileLimit','id',array(
            'alias' => 'uploadFileLimit'
        ));
    }
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'user_id' => 'user_id', 
            'product_id' => 'product_id', 
            'upload_file_limit_id' => 'upload_file_limit_id'
        );
    }

}
