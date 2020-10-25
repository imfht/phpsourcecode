<?php

namespace application\modules\user\model;


use application\core\model\Model;

class CacheUserDetail extends Model
{
    /**
     * @param string $className
     * @return CacheUserDetail
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{cache_user_detail}}';
    }

}