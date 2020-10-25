<?php
/**
 * 汇报Api控制器
 */

namespace application\modules\report\controllers;

use application\core\utils\Ibos;
use application\modules\user\components\User as ICUser;

class ApiController extends BaseController
{
    public function init()
    {
        parent::init();
        //如果为登录或者session过期，返回500和未登录状态
        $expires = Ibos::app()->user->getState(ICUser::AUTH_TIMEOUT_VAR);
        $isGuest = Ibos::app()->user->isGuest;
        if ( !(!$isGuest && ($expires == null || $expires > time()))) {
            header("HTTP/1.0 500");
            exit(json_encode(array(
                'isLogin' => false
            )));
        }
    }

    public function actions()
    {
        $actions = array(
            'shoplist' => 'application\modules\report\actions\api\ShopList',
            'addtemplate' => 'application\modules\report\actions\api\AddTemplate',
            'usertemplate' => 'application\modules\report\actions\api\UserTemplate',
            'sorttemplate' => 'application\modules\report\actions\api\SortTemplate',
            'settemplate' => 'application\modules\report\actions\api\SetTemplate',
            'managertemplate' => 'application\modules\report\actions\api\ManagerTemplate',
            'getcharge' => 'application\modules\report\actions\api\GetCharge',
            'savereport' => 'application\modules\report\actions\api\SaveReport',
            'formreport' => 'application\modules\report\actions\api\FormReport',
            'getlist' => 'application\modules\report\actions\api\GetList',
            'showreport' => 'application\modules\report\actions\api\ShowReport',
            'getreader' => 'application\modules\report\actions\api\GetReader',
            'delreport' => 'application\modules\report\actions\api\DelReport',
            'getcommentlist' => 'application\modules\report\actions\api\GetCommentList',
            'addcomment' => 'application\modules\report\actions\api\AddComment',
            'delcomment' => 'application\modules\report\actions\api\DelComment',
            'formtemplate' => 'application\modules\report\actions\api\FormTemplate',
            'savetemplate' => 'application\modules\report\actions\api\SaveTemplate',
            'deltemplte' => 'application\modules\report\actions\api\DelTemplate',
            'setstamp' => 'application\modules\report\actions\api\SetStamp',
            'allread'=> 'application\modules\report\actions\api\AllRead',
            'getpicture' => 'application\modules\report\actions\api\GetPicture',
            'getstamp' => 'application\modules\report\actions\api\GetStamp',
            'getcount' => 'application\modules\report\actions\api\GetCount',
            'getcommentview' => 'application\modules\report\actions\api\GetCommentView',
            'getreviewcomment' => 'application\modules\report\actions\api\GetReviewComment',
            'getauthority' => 'application\modules\report\actions\api\GetAuthority',
        );
        return $actions;
    }
}