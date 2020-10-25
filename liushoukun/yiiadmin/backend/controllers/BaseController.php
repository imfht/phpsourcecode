<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/10 23:56
// +----------------------------------------------------------------------
// | TITLE:基础类
// +----------------------------------------------------------------------

namespace backend\controllers;

use backend\behaviors\AdminLog;
use backend\behaviors\Rbac;
use Yii;
use yii\helpers\Url;
use backend\helps\Tree;
use yii\web\Controller;
use backend\models\AdminRole;


/**
 * Class BaseController
 * @package backend\controllers
 */
class BaseController extends Controller
{
    const ADMIN_DOING = 'ADMIN_RUN';
    public $layout = 'public';
    public $menu;
    public $menuHtml;

    public function behaviors()
    {

        $behaviors = [
            AdminLog::className(),//记录
            Rbac::className(),//权限控制
        ];
        return array_merge( parent::behaviors(),$behaviors);
    }


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        if ($this->isLogin($action)) {

            if (!$this->verifyRule($this->route)) {
                //todo 没有权限处理
                die('你没有权限');
            } else {
                $this->menu = AdminRole::getRule(Yii::$app->user->identity->role_id);
                $this->menuHtml = self::buildMenuHtml(Tree::makeTree($this->menu));
            }
        }
        return true;
    }

    /**
     * 验证登入
     * @return bool
     */
    protected function isLogin()
    {
        if (Yii::$app->user->isGuest) {
            $allowUrl = ['site/logout', 'site/login'];
            if (in_array($this->route, $allowUrl) == false) {
                $loginUrl = Url::toRoute('site/login');
                header("Location: $loginUrl");
                exit();
            } else {
                return false;
            }
        } else {
            return true;
        }

    }

    /**
     * 生成
     * @param $data
     * @param string $html
     * @return string
     */
    private static function buildMenuHtml($data, $html = '')
    {
        foreach ($data as $k => $v) {
            if (isset($v['type']) && $v['type'] != 2 && $v['status'] == 1) {

                $html .= '<li >';
                //需要验证是否有子菜单
                if (isset($v['children']) && is_array($v['children'])) {
                    $html .= '<a href="javascript::(0)" class="dropdown-toggle">';
                } else {
                    $html .= '<a href="javascript:openapp(\' ' . Url::toRoute($v['route']) . '\',\'' . $v['id'] . '\',\'' . $v['title'] . '\',true);" class="">';
                }
                //图标
                $html .= '<i class="menu-icon ' . $v['icon'] . '"></i>';
                //名称
                $html .= '   <span class="menu-text">' . $v['title'] . '</span>';

                if (isset($v['children']) && is_array($v['children'])) {
                    $html .= '<b class="arrow fa fa-angle-down"></b></a>';
                } else {
                    $html .= '<b class="arrow fa s"></b></a>';
                }

                //需要验证是否有子菜单
                if (isset($v['children']) && is_array($v['children'])) {
                    $html .= ' <b class="arrow"></b>';
                    $html .= '<ul class="submenu nav-hide" style="display: none;">';
                    $html .= self::buildMenuHtml($v['children']);
                    //验证是否有子订单
                    $html .= '</ul>';
                }
                $html .= '</li>';
            }
        }
        return $html;

    }

}

