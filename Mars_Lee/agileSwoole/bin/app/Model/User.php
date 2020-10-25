<?php

namespace Model;


use Component\Orm\Model\Model;

class User extends Model
{
        protected $driver = 'sMysql';
        public function __construct()
        {
                $this->configName = 'users';
                parent::__construct();
        }
}