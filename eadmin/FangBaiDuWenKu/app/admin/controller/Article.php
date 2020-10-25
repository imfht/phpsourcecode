<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Article as LogicArticle;
use app\common\logic\Articlecate as LogicArticlecate;

/**
 * 文章控制器
 */
class Article extends AdminBase
{
    
    /**
     * 文章逻辑
     */
	
    private static $articleLogic = null;
    private static $articlecateLogic = null;
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        self::$articlecateLogic = get_sington_object('articlecateLogic', LogicArticlecate::class);
        self::$articleLogic = get_sington_object('articleLogic', LogicArticle::class);
    }

    
    /**
     * 文章列表
     */
    public function articleList()
    {
        
        $where = self::$articleLogic->getWhere($this->param);
        
        $this->assign('list', self::$articleLogic->getArticleList($where, true, 'id desc'));
        $this->assign('articlecate',self::$articlecateLogic->getArticlecateList(['status'=>1], true, 'id desc',false));
       
        return $this->fetch('article_list');
    }
    
    /**
     * 文章添加
     */
    public function articleAdd()
    {
        
        IS_POST && $this->jump(self::$articleLogic->articleAdd($this->param));
        $this->assign('groupcate_list',self::$articlecateLogic->getArticlecateList(['status'=>1], true, 'id desc',false));
        return $this->fetch('article_add');
    }
    /**
     * 文章编辑
     */
    public function articleEdit()
    {
    	$info = self::$articleLogic->getArticleInfo(['id' => $this->param['id']]);
    	$info['content']=htmlspecialchars_decode($info['content']);
    	
    	IS_POST && $this->jump(self::$articleLogic->articleEdit($this->param,$info));
    	$this->assign('groupcate_list',self::$articlecateLogic->getArticlecateList(['status'=>1], true, 'id desc',false));
    	
    	$this->assign('info', $info);
    	return $this->fetch('article_edit');
    }
    /**
     * 文章批量删除
     */
    public function articleAlldel($ids = 0)
    {
    
    	$this->jump(self::$articleLogic->articleAlldel($ids));
    }
    /**
     * 文章删除
     */
    public function articleDel($id = 0)
    {
        
        $this->jump(self::$articleLogic->articleDel(['id' => $id]));
    }
    /**
     * 文章状态更新
     */
    public function articleCstatus($id = 0,$status,$field)
    {
        
        $this->jump(self::$articleLogic->setArticleValue(['id' => $id],$field,$status));
    }

}
