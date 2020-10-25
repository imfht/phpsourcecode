<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/1/4 16:32
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------


namespace backend\behaviors;

use Yii;
use yii\base\Behavior;
use backend\models\AdminRole;
use yii\helpers\ArrayHelper;


class Rbac extends Behavior
{
    /**
     * 放行路由
     * @var array
     */
    public $allowUrl = [
        'site/logout',
        'site/login',
        'index/index',
        'index/main'
    ];

    /**
     * 验证
     * @param $route 当前路由
     * @return bool
     */
    public function verifyRule($route)
    {

        $this->allowUrl = array_merge(Yii::$app->params['allowUrl'], $this->allowUrl);
        $rules = AdminRole::getRule(Yii::$app->user->identity->role_id);
        if (Yii::$app->user->identity->role_id == AdminRole::ADMIN_ID) return true;
        $rules = ArrayHelper::map($rules, 'id', 'route');
        $rules = array_merge($rules, $this->allowUrl);
        return in_array($route, $rules);
    }


}