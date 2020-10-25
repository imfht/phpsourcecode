<?php

namespace app\common\logic;

/**
 * 公告及消息逻辑
 */
class Message extends LogicBase
{
    
    // 公告及消息模型
    public static $messageModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$messageModel = model($this->name);
    }
    
    /**
     * 获取公告及消息信息
     */
    public function getMessageInfo($where = [], $field = true)
    {
        
        return self::$messageModel->getInfo($where, $field);
    }
    
    /**
     * 获取公告及消息列表
     */
    public function getMessageList($where = [], $field = true, $order = '')
    {
        
        return self::$messageModel->getList($where, $field, $order);
    }
    
    /**
     * 获取公告及消息列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['name'] = ['like', '%'.$data['search_data'].'%'];
        
        if (!is_administrator()) {
            
         
        }
        
        return $where;
    }
    
  
    /**
     * 公告及消息添加
     */
    public function messageAdd($data = [])
    {
       
       
       
        

       
        return self::$messageModel->setInfo($data) ? [RESULT_SUCCESS, '添加成功'] : [RESULT_ERROR, self::$messageModel->getError()];
    }
    /**
     * 公告及消息编辑
     */
    public function messageEdit($data = [],$info)
    {
    	 
    	

    
    	return self::$messageModel->setInfo($data) ? [RESULT_SUCCESS, '编辑成功'] : [RESULT_ERROR, self::$messageModel->getError()];
    }
    /**
     * 设置公告及消息信息
     */
    public function setMessageValue($where = [], $field = '', $value = '')
    {
       
        return self::$messageModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$navModel->getError()];
    }
    /**
     * 公告及消息批量删除
     */
    public function messageAlldel($ids)
    {
    	

    return self::$messageModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$messageModel->getError()];
    }  
    /**
     * 公告及消息删除
     */
    public function messageDel($where = [])
    {
        
      
        
        return self::$messageModel->deleteInfo($where) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$messageModel->getError()];
    }
}
