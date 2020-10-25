<?php

namespace model\index;

use onefox\Model;

class Test extends Model {

	protected $dbConfig = 'test';

    public function test(){
        return $this->db->query('select * from `posts`');
    }
}

