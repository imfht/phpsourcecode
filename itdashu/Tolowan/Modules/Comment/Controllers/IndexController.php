<?php
namespace Modules\Comment\Controllers;

use Core\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        extract($this->variables['router_params']);
    }

    public function loveAction()
    {
        extract($this->variables['router_params']);
        $data = array(
            'state' => false,
            'notice' => '执行失败',
        );
        $this->variables['#templates'] = 'json';
        $this->variables['data'] = &$data;
        $commentEntity = $this->entityManager->get('comment');
        $comment = $nodeEntity->findFirst($id, true);
        if ($comment) {
            $comment->love = $node->love + 1;
            if ($comment->save()) {
                $data = array(
                    'state' => true,
                    'notice' => '执行成功',
                );
            }
        }
    }
}
