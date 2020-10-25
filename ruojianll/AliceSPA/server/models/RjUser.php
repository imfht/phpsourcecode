<?php
use \Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
class RjUser extends \Phalcon\Mvc\Model
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
    public $password;

    /**
     *
     * @var string
     */
    public $mobilephone;

    /**
     *
     * @var string
     */
    public $e_mail;

    /**
     *
     * @var string
     */
    public $permission;
    public $create_date;
    public function  initialize(){
        $this->hasMany('id','RjAddress','user_id',array(
            'alias' => 'addresses'
        ));
        $this->hasMany('id','RjUser','user_id',array(
            'alias' => 'carts'
        ));
        $this->hasMany('id','RjComment','creator_id',array(
            'alias' => 'comments'
        ));
        $this->hasMany('id','RjCommentMap','user_id',array(
            'alias' => 'commentMaps'
        ));
        $this->hasMany('id','RjHistory','operator_id',array(
            'alias' => 'histories'
        ));
        $this->hasMany('id','RjOrder','user_id',array(
            'alias' => 'orders'
        ));
        $this->hasMany('id','RjStory','creator_id',array(
            'alias' => 'stories'
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
            'password' => 'password', 
            'mobilephone' => 'mobilephone', 
            'e_mail' => 'e_mail',
            'permission' => 'permission',
            'create_date' => 'create_date'
        );
    }
    public function validation()
    {
        //Robot name must be unique
        $this->validate(new Uniqueness(
            array(
                "field"   => "name",
                "message" => "The user name must be unique"
            )
        ));
        $this->validate(new Uniqueness(
            array(
                "field"   => "mobilephone",
                "message" => "The user mobilephone must be unique"
            )
        ));


        //Check if any messages have been produced
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
