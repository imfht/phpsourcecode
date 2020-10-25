<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Message as LogicMessage;


/**
 * 公告及消息控制器
 */
class Message extends AdminBase
{
    
    /**
     * 公告及消息逻辑
     */
	
    private static $messageLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
        self::$messageLogic = get_sington_object('messageLogic', LogicMessage::class);
    }

    
    /**
     * 公告及消息列表
     */
    public function messageList()
    {
        
        $where = self::$messageLogic->getWhere($this->param);
        
        $this->assign('list', self::$messageLogic->getMessageList($where, true, 'id desc'));
       
       
        return $this->fetch('message_list');
    }
    
    /**
     * 公告及消息添加
     */
    public function messageAdd()
    {
        
        IS_POST && $this->jump(self::$messageLogic->messageAdd($this->param));
        
        return $this->fetch('message_add');
    }
    /**
     * 公告及消息编辑
     */
    public function messageEdit()
    {
    	$info = self::$messageLogic->getMessageInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$messageLogic->messageEdit($this->param,$info));
    	
    	
    	$this->assign('info', $info);
    	return $this->fetch('message_edit');
    }
    /**
     * 公告及消息批量删除
     */
    public function messageAlldel($ids = 0)
    {
    
    	$this->jump(self::$messageLogic->messageAlldel($ids));
    }
    /**
     * 公告及消息删除
     */
    public function messageDel($id = 0)
    {
        
        $this->jump(self::$messageLogic->messageDel(['id' => $id]));
    }
}
