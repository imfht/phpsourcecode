<?php
namespace Modules\Book\Controllers;

use Core\Mvc\Controller;
use Core\Config;
use Modules\Book\Models\NodeBook;
use Modules\Node\Entity\Node;
use Modules\Book\Library\Common;

class AdminController extends Controller
{

    public function indexAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $bookNode = Node::findFirst($id);
        if (!$bookNode) {
            return $this->notFount();
        }
        $data = NodeBook::find(array(
            'order' => 'weight DESC',
            'conditions' => 'bid = :bid: AND pid = :pid:',
            'bind' => array(
                'pid' => 0,
                'bid' => $id
            ),
        ));
        $bookItemAddForm = $this->form->create('book.bookItemAdd',array('bid'=>$id));
        if($bookItemAddForm->isValid()){
            $bookItemAddForm->save();
        }
        if ($this->request->isPost() && $this->request->hasPost('rh')) {
            $rh = json_decode($this->request->getPost('rh'));
            $rh = jsonToHierarchy($rh);
            Common::saveBookItemSort($rh);
            $this->flash->success('菜单排序成功');
        }

        $this->variables += array(
            'title' => '书本：《' . $bookNode->getTitle() . '》',
            'description' => '排序书本目录',
            '#templates' => 'page',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'nodeList' => array(
                    'href' => array(
                        'for' => 'adminEntityList',
                        'entity' => 'node',
                        'page' => 1,
                    ),
                    'name' => '内容列表',
                ),
            ),
            'content' => array(),
        );
        // 添加编辑菜单
        $content['bookList'] = array(
            '#templates' => 'box',
            'title' => '目录列表',
            'max' => false,
            'wrapper' => true,
            'color' => 'primary',
            'size' => '6',
            'content' => array(
                '#templates' => 'adminBookList',
                'id' => 'BookListHierarchy',
                'title_display' => false,
                'data' => $data,
            ),
        );
        $content['termHandle'] = array(
            '#templates' => 'box',
            'title' => '添加项目',
            'max' => false,
            'id' => 'right_handle',
            'wrapper' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'termForm' => $bookItemAddForm->renderForm(),
            ),
        );

        $this->variables['content'] += $content;
    }

    public function editAction()
    {
        extract($this->variables['router_params']);
        $nodeBook = NodeBook::findFirst($id);
        $bookItemAddInfo = Config::get('book.bookItemAdd');
        $bookItemAddInfo['settings']['id'] = $id;
        $bookItemAdd = $this->form->create($bookItemAddInfo,$nodeBook->toArray());
        if($bookItemAdd->isValid()){
            $bookItemAdd->save();
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(
                'termEditorForm' => array(
                    '#templates' => 'box',
                    'wrapper' => false,
                    'title' => '书本项目编辑-',
                    'max' => false,
                    'color' => 'success',
                    'size' => '12',
                    'content' => array(
                        'data' => $bookItemAdd->renderForm(),
                    ),
                ),
            ),
        );
    }

    public function deleteAction()
    {
        extract($this->variables['router_params']);
        $nodeBook = NodeBook::findFirst($id);
        if($nodeBook){
            if($nodeBook->delete()){
                $this->flash->success('删除成功');
            }else{
                $this->flash->error('删除失败');
            }
        }else{
            $this->flash->error('删除失败，内容不存在');
        }
        return $this->moved();
    }
}
