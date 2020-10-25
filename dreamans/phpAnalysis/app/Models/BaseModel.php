<?php

namespace App\Models;

use Nimble\Mysql\Model;

class BaseModel extends Model
{
    protected $config;

    protected $tbPrefix = '';

    public function __construct()
    {
        $dbConfig = config('database.connection');
        $this->config = $dbConfig;
        $this->config['charset'] = 'utf8';
        $this->tbPrefix = $dbConfig['tb_prefix'];

        parent::__construct();
    }
}

