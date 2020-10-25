<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Org\Util\String;

/**
 * 处理问卷相关请求
 */
class QuestionnaireController extends CommonController
{
	protected function _initialize()
	{
		parent::_initialize();
        $this->bcItemPush('问卷管理', U('Questionnaire/index'));
	}

    /* 问卷列表 */
    public function index()
    {
        $this->assign('questionnaires', M('Questionnaires')->field(true)->select());

        $this->display();
    }

    /* 问卷添加 */
    public function add()
    {
        $this->bcItemPush('添加');

        if( IS_GET ){ //访问页面
            $this->display();
        }else{ //表单提交
            $questionnaires = D('Questionnaires');
         
            if( $questionnaires->create() ){
                $state = $questionnaires->add();

                if( $state===false ){
                    $this->error('问卷添加失败，错误信息：'.$questionnaires->getDbError());
                }else{
                    $this->success('问卷添加成功', '/questionnaire/index', 0);
                }
            }else{ //表单提交不完整
                $this->assign(I('post.')); //反馈提交的数据
                $this->assign('errorNote', $questionnaires->getError());
                $this->display();
            }
        }
    }

    /* 问卷编辑 */
    public function edit()
    {
        $this->bcItemPush('编辑');

        if( IS_GET ){ //访问页面
            $questionnaires = M('Questionnaires')->field(true)->find( I('get.id/d') );
            $this->assign($questionnaires);
            $this->display();
        }else{ //表单提交
            $questionnaires = D('Questionnaires');

            if( $questionnaires->create() ){ //表单提交
                $state = $questionnaires->save();

                if( $state===false ){
                    $this->error('问卷更新失败，错误信息：'.$questionnaires->getDbError());
                }else{
                    $this->success('问卷编辑成功', '/questionnaire/index', 0);
                }
            }else{ //表单提交不完整
                $this->assign(I('post.')); //反馈提交的数据
                $this->assign('errorNote', $questionnaires->getError());
                $this->display();
            }
        }
    }

    /* 问卷删除 */
    public function delete()
    {
        if( is_array(I('id')) ){ //批量删除
            $id = implode( ',', I('id', array(), 'intval') );
        }else{ //单个删除
            $id = I('id/d');
        }

        $questionnaires = D('Questionnaires');
        $state = $questionnaires->relation('Questions')->delete($id);

        if( $state===false ){
            $this->error('问卷删除失败，错误信息: '.$questionnaires->getDbError()); //删除问卷，并关联删除旗下问题
        }else{
            $this->redirect('/questionnaire/index');
        }
    }

}
?>