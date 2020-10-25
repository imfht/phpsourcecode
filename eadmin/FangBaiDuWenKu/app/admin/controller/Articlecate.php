<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Articlecate as LogicArticlecate;


/**
 * 文章分类控制器
 */
class Articlecate extends AdminBase
{
    
    /**
     * 文章分类逻辑
     */
	
    private static $articlecateLogic = null;
   
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
        self::$articlecateLogic = get_sington_object('articlecateLogic', LogicArticlecate::class);
       
    }

    
    /**
     * 文章分类列表
     */
    public function articlecateList()
    {
        
        $where = self::$articlecateLogic->getWhere($this->param);
       
        
        
        
        $this->assign('list', self::$articlecateLogic->getArticlecateList($where, true, 'id desc'));
       
       
       
       
       
        return $this->fetch('articlecate_list');
    }
    
    /**
     * 文章分类添加
     */
    public function articlecateAdd()
    {
        
        IS_POST && $this->jump(self::$articlecateLogic->articlecateAdd($this->param));
        $this->assign('groupcate_list',self::$articlecateLogic->getArticlecateList(['status'=>1], true, 'id desc'));
        return $this->fetch('articlecate_add');
    }
    /**
     * 文章分类编辑
     */
    public function articlecateEdit()
    {
    	$info = self::$articlecateLogic->getArticlecateInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$articlecateLogic->articlecateEdit($this->param,$info));
    	
    	$this->assign('groupcate_list',self::$articlecateLogic->getArticlecateList(['status'=>1], true, 'id desc'));
    	$this->assign('info', $info);
    	return $this->fetch('articlecate_edit');
    }
    /**
     * 文章分类批量删除
     */
    public function articlecateAlldel($ids = 0)
    {
    
    	$this->jump(self::$articlecateLogic->articlecateAlldel($ids));
    }
    /**
     * 文章分类删除
     */
    public function articlecateDel($id = 0)
    {
        
        $this->jump(self::$articlecateLogic->articlecateDel(['id' => $id]));
    }

}
