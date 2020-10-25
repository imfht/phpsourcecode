<?php
namespace Modules\Comment\Controllers;

use Core\Mvc\Controller;
use Modules\User\Models\UserLog;

class UserController extends Controller
{
    public function indexAction()
    {
        extract($this->variables['router_params']);
    }

    public function submitAction()
    {
        extract($this->variables['router_params']);
        $nodeEntity = $this->entityManager->get('node');
        $commentEntity = $this->entityManager->get('comment');
        $node = $nodeEntity->findFirst($nid, true);
        if (!$node) {
            return $this->notFount();
        }
        $nodeFields = $nodeEntity->getFields($node->contentModel);
        $commentForm = $commentEntity->addForm($nodeFields['commentContentModel'], array(
            'nid' => $nid,
            'pid' => $pid,
            'contentModel' => $nodeFields['commentContentModel'],
        ));
        $this->variables += array(
            '#templates' => 'commentSubmit',
            'node' => $node,
            'commentForm' => $commentForm->renderForm(),
        );
        if ($commentEntity->isSaveSuccess() === true) {
            //评论成功
            $this->variables['#templates'] = 'commentSubmitSuccess';
            $this->variables['commentEntity'] = $commentEntity->entityModel;
        } elseif ($commentEntity->isSaveSuccess() === false) {
            //评论失败
            $this->variables['#templates'] = 'commentSubmitFalse';
        }
    }
    public function loveAction()
    {
        extract($this->variables['router_params']);
        $data = array();
        $this->variables += array(
            '#templates' => 'json',
            'data' => &$data,
        );
        $commentEntity = $this->entityManager->get('comment');
        $comment = $commentEntity->findFirst($id, true);
        if (!$comment) {
            $data = array(
                'state' => 'fail',
                'number' => '0',
                'notice' => '内容不存在了',
            );
        }
        $log = UserLog::findFirst(array(
            'conditions' => 'uid = :uid: AND type = :type:',
            'bind' => array(
                'uid' => $this->user->id,
                'type' => 'comment_love_' . $id,
            ),
        ));

        if ($log && $comment->love >= 1) {
            $comment->love += 1;
        } else {
            $comment->love -= 1;
        }
        if ($comment->save()) {
            $data = array(
                'state' => 'success',
                'number' => $comment->love,
            );
        } else {
            $data = array(
                'state' => 'fail',
                'number' => $comment->love,
                'notice' => '保存失败',
            );
        }
    }
}
