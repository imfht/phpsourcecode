<?php

namespace app\common\logic;

/**
 * 评论逻辑
 */
class Comment extends LogicBase
{
    
    // 会员模型
    public static $commentModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$commentModel = model($this->name);
    }
    
    /**
     * 获取评论信息
     */
    public function getCommentInfo($where = [], $field = true)
    {
        
        return self::$commentModel->getInfo($where, $field);
    }
    
    /**
     * 获取评论列表
     */
    public function getCommentList($where = [], $field = true, $order = '')
    {
    	


      
        return self::$commentModel->getList($where, 'm.*,user.username,doccon.title as doctitle', $order,0,[['user','m.uid=user.id'],['doccon','m.fid=doccon.id']]);
    }
    
    /**
     * 获取评论列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        
        $where['m.status']=array('neq',-1);
        
        !empty($data['search_data']) && $where['m.name'] = ['like', '%'.$data['search_data'].'%'];
        
        if (!is_administrator()) {
            
         
        }
        
        return $where;
    }
    
  
    /**
     * 评论添加
     */
    public function commentAdd($data = [])
    {
       
       
       
        

       
        return self::$commentModel->setInfo($data) ? [RESULT_SUCCESS, '评论添加成功'] : [RESULT_ERROR, self::$commentModel->getError()];
    }
    /**
     * 评论编辑
     */
    public function commentEdit($data = [],$info)
    {
    	 
    	 
    

    
    	return self::$commentModel->setInfo($data) ? [RESULT_SUCCESS, '评论编辑成功'] : [RESULT_ERROR, '1'];
    }
    /**
     * 设置评论信息
     */
    public function setCommentValue($where = [], $field = '', $value = '')
    {
       
        return self::$commentModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$commentModel->getError()];
    }
    /**
     * 评论批量删除
     */
    public function commentAlldel($ids)
    {
    	

    return self::$commentModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '评论删除成功'] : [RESULT_ERROR, self::$commentModel->getError()];
    }  
    /**
     * 评论删除
     */
    public function commentDel($where = [])
    {
        
      
        
        return self::$commentModel->deleteInfo($where) ? [RESULT_SUCCESS, '评论删除成功'] : [RESULT_ERROR, self::$commentModel->getError()];
    }
}
