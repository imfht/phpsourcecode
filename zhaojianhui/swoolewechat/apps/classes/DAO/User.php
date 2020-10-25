<?php
namespace App\DAO;

/**
 * Class User
 * example: $user = new App\DAO\User(1);  $user->get();
 * @package App\DAO
 */
class User extends Base
{
    /**
     * 模型名称
     * @var string
     */
    protected $modelName = 'User';
}
