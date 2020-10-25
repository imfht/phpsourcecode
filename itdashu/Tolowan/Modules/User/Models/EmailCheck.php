<?php
namespace Modules\User\Models;

use Phalcon\Mvc\Model;

class EmailCheck extends Model
{
    public $id;
    public $salt;
    public $email;
    public $created;

    public function isOvertime(){
        if($this->created+86400 > time()){
            return false;
        }
        return true;
    }
}
