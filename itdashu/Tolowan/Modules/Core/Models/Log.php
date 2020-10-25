<?php
namespace Modules\Core\Models;

use Phalcon\Mvc\Model;

class Log extends Model
{
    public $id;
    public $data;
    public $created;
    public $del;
    // 设置数据库
    public function beforeValidationOnCreate()
    {
        $this->created = time();
        if (!$this->del) {
            $this->del = 24;
        }
    }
}
