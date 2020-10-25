<?php

namespace Controller;


use Model\User;
use Model\UserAsync;

class Welcome
{
        public function index()
        {
            $this->userInsert('sync');
            $this->userInsert('sync_1');
                return [
                        'code'  =>      0,
                        'view'  =>      realpath(__DIR__.'/../View/index.php')
                ];
        }

    public function async()
    {
        $this->userInsert1('async');
        $this->userInsert1('async_1');
        return [
            'code'  =>      0,
            'view'  =>      realpath(__DIR__.'/../View/index.php')
        ];
    }

    public function userInsert1(string $name)
    {
        $user = new UserAsync();
        $id = $user->insert(['name'=>$name])->execute();
        return ['id'=>$id];
    }

        public function userInsert(string $name)
        {
                $user = new User();
                $id = $user->insert(['name'=>$name])->execute();
                return ['id'=>$id];
        }
}