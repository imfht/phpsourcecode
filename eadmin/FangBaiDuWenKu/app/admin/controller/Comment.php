<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Comment as LogicComment;


/**
 * 评论控制器
 */
class Comment extends AdminBase
{
    
    /**
     * 评论逻辑
     */
	
    private static $commentLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
     
        self::$commentLogic = get_sington_object('commentLogic', LogicComment::class);
    }

    
    /**
     * 评论列表
     */
    public function commentList()
    {
        
        $where = self::$commentLogic->getWhere($this->param);
        
        $this->assign('list', self::$commentLogic->getCommentList($where, true, 'id desc'));
       
       
        return $this->fetch('comment_list');
    }
    
    /**
     * 评论编辑
     */
    public function commentEdit()
    {
    	$info = self::$commentLogic->getCommentInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$commentLogic->commentEdit($this->param,$info));
    	
    	
    	$this->assign('info', $info);
    	return $this->fetch('comment_edit');
    }
    /**
     * 评论批量删除
     */
    public function commentAlldel($ids = 0)
    {
    
    	$this->jump(self::$commentLogic->commentAlldel($ids));
    }
    /**
     * 评论删除
     */
    public function commentDel($id = 0)
    {
        
        $this->jump(self::$commentLogic->commentDel(['id' => $id]));
    }

}
