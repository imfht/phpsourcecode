<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 15:13
 */

namespace backend\models\AuthItem;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class AuthItem extends ActiveRecord{

    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    /**
     * 自动更新  created_at  updated_at
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 分页方法
     * $where 查询条件数组
     * $page  分页条件数组
     */
    public static function dataPage($page = null){

        $query  = static::find();
        //每页显示几条
        $limit  = $page['pageSize'];
        //计算分页
        $offset = ($page['pageIndex'] - 1) * $limit;

        $ret    = $query    ->andFilterWhere(['like','name',$page['search']])
                            ->orFilterWhere(['like','description',$page['search']])
                            ->andFilterWhere(['=','type',$page['type']])
                            ->offset($offset)
                            ->limit($limit)
                            ->orderBy($page['sort'])
                            ->asArray()
                            ->all();
        return $ret;
    }
    /**
     * 获取总页数
     * $where 查询条件数组
     */
    public static function dataCount($where){

        $query       = static::find();

        $totalCount  = $query   ->andFilterWhere(['like','name',$where['search']])
                                ->orFilterWhere(['like','description',$where['search']])
                                ->andFilterWhere(['=','type',$where['type']])
                                ->count();

        return $totalCount;
    }

}