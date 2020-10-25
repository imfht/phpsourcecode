<?php
namespace Model;
use Illuminate\Database\Eloquent\Model;
class User extends Model
{

	protected $table = 'tb_user';

	public $timestamps = false;

    public static function test(){
        return "test";
    }

}