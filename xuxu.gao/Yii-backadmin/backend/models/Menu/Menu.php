<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/20
 * Time: 9:59
 */

namespace backend\models\Menu;


use backend\models\Menu\Traits\MenuTraits;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Menu extends ActiveRecord{

    public static function tableName()
    {
        return '{{%menu}}';
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
                            ->andFilterWhere(['=','parent_id',0])
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

        $totalCount  = $query->andFilterWhere(['like','username',$where['search']])
                            ->andFilterWhere(['=','parent_id',0])
                            ->count();

        return $totalCount;
    }

}