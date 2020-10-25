<?php

namespace application\modules\assignment\controllers;

use application\core\controllers\Controller;
use application\core\utils\Env;
use application\core\utils\Ibos;
use application\core\utils\StringUtil;

class CommentController extends Controller
{

    /**
     * 获取评论列表
     */
    public function actionGetCommentList()
    {
        if (Env::submitCheck('formhash')) {
            $module = StringUtil::SQLfilter($_POST['module']);
            $table = StringUtil::SQLfilter($_POST['table']);
            $limit = filter_var(Env::getRequest('limit'),FILTER_VALIDATE_INT); //每页条数
            $offset = filter_var(Env::getRequest('offset'),FILTER_VALIDATE_INT); //偏移
            $rowid = filter_var($_POST['rowid'],FILTER_VALIDATE_INT);
            $type = StringUtil::SQLfilter(Env::getRequest('type'));
            $url = filter_var(Env::getRequest('url'),FILTER_VALIDATE_URL);
            $properties = array(
                'module' => $module,
                'table' => $table,
                'attributes' => array(
                    'rowid' => $rowid,
                    'limit' => $limit ? intval($limit) : 10,
                    'offset' => $offset ? intval($offset) : 0,
                    'type' => $type,
                    'url' => $url
                )
            );
            $widget = Ibos::app()->getWidgetFactory()->createWidget($this, 'application\modules\assignment\widgets\AssignmentComment', $properties);
            $list = $widget->fetchCommentList();
            $this->ajaxReturn(array('isSuccess' => true, 'data' => $list));
        }
    }

    /**
     * 增加一条评论或回复
     * @return string
     */
    public function actionAddComment()
    {
        if (Env::submitCheck('formhash')) {
            $widget = Ibos::app()->getWidgetFactory()->createWidget($this, 'application\modules\assignment\widgets\AssignmentComment');
            return $widget->addComment();
        }
    }

    /**
     * 增加一条评论或回复
     * @return void
     */
    public function actionDelComment()
    {
        $widget = Ibos::app()->getWidgetFactory()->createWidget($this, 'application\modules\assignment\widgets\AssignmentComment');
        return $widget->delComment();
    }

}