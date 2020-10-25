<?php
/**
 *某篇汇报的评阅和已阅人员
 */

namespace application\modules\report\actions\api;

use application\core\utils\Env;
use application\core\utils\Ibos;
use application\core\utils\Org;
use application\core\utils\StringUtil;
use application\modules\message\model\Comment;
use application\modules\report\model\ModuleReader;
use application\modules\user\model\User;

class GetReviewComment extends Base
{

    public function run()
    {
        $repid = Env::getRequest('repid');
        $type = Env::getRequest('type');
        if ($type == 'comment'){
            $view = $this->getCommentView($repid);
        }else{
            $view = $this->getReviewView($repid);
        }
        echo $view;
    }

    private function getCommentView($repid)
    {
        $records = Comment::model()->fetchAll(
            array(
                'select' => array('uid', 'content', 'ctime'),
                'condition' => "module=:module AND `table`=:table AND rowid=:rowid AND isdel=:isdel ORDER BY ctime DESC LIMIT 0,5",
                'params' => array(':module' => 'report', ':table' => 'report', ':rowid' => $repid, ':isdel' => 0)
            )
        );
        $htmlStr = '<div class="pop-comment"><ul class="pop-comment-list">';
        if (!empty($records)) {
            foreach ($records as $record) {
                $record['realname'] = User::model()->fetchRealnameByUid($record['uid']);
                $content = StringUtil::cutStr($record['content'], 45);
                $htmlStr .= '<li class="media">
									<a href="' . Ibos::app()->createUrl('user/home/index', array('uid' => $record['uid'])) . '" class="pop-comment-avatar pull-left">
										<img src="' . Org::getDataStatic($record['uid'], 'avatar', 'small') . '" title="' . $record['realname'] . '" class="img-rounded"/>
									</a>
									<div class="media-body">
										<p class="pop-comment-body"><em>' . $record['realname'] . ': </em>' . $content . '</p>
									</div>
								</li>';
            }
        } else {
            $htmlStr .= '<li align="middle">' . Ibos::lang('Has not comment') . '</li>';
        }
        $htmlStr .= '</ul></div>';

        return $htmlStr;
    }

    private function getReviewView($repid)
    {
        $readerUidArr = ModuleReader::model()->getReader($repid);
        $htmlStr = '<table class="pop-table">';
        if (!empty($readerUidArr)) {
            $htmlStr .= '<div class="rp-reviews-avatar">';
            $users = User::model()->fetchAllByUids($readerUidArr);
            foreach ($users as $user) {
                $htmlStr .= '<a href="' . Ibos::app()->createUrl('user/home/index', array('uid' => $user['uid'])) . '">
								<img class="img-rounded" src="' . $user['avatar_small'] . '" title="' . $user['realname'] . '" />
							</a>';
            }
        } else {
            $htmlStr .= '<div><li align="middle">' . Ibos::lang('Has not reader') . '</li>';
        }
        $htmlStr .= '</div></table>';
        return $htmlStr;
    }
}