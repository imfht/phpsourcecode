<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/13 22:24
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------

namespace backend\models;


use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord
{


    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public  function enumeration($field='',$key)
    {
        $Values = $this->attributeValues();
        return $Values[$field][$key];
    }
}