<?php
namespace app\common\service;


use app\common\model\AbilitiesModel;
use app\common\model\UserModel;
use utils\SessionUtils;
use utils\ValidateUtils;

class LoginService
{
    protected $sessionUtils;
    protected $userModel;

    public function __construct()
    {
        $this->sessionUtils = new SessionUtils();
        $this->userModel = new UserModel();
    }

    public function auth($username, $password)
    {
        $validateUtils = new ValidateUtils();
        $type = $validateUtils->usernameType($username);
        $input = [
            'username' => $username,
            'password' => $password,
            'type'    => $type
        ];
        $user = $this->userModel->getFind($input);
        if(!empty($user)){
            $data = [
                'id'             => $user['id'],
                'name'           => $user['name'],
                'email'          => $user['email'],
                'phone'          => $user['phone'],
                'introduction'   => $user['introduction'],
                'sex'            => $user['sex'],
                'birth_at'       => $user['birth_at'],
                'real_name'      => $user['real_name'],
                'id_name'        => $user['id_name'],
                'location'       => $user['location'],
                'remember_token' => $user['remember_token'],
                'created_at'     => $user['created_at'],
                'updated_at'     => $user['updated_at'],
                'login_time'     => time()
            ];

            $abilitiesModel = new AbilitiesModel();
            $abilitiesAuth = $abilitiesModel->trueAbilities($data['id']);
            $this->sessionUtils->setSessionAuth($data, $abilitiesAuth);

            return true;
        }
        return false;
    }

    public function sessionOut()
    {
        $this->sessionUtils->sessionOut();
    }

}