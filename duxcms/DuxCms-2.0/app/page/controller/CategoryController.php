<?php
namespace app\page\controller;
use app\home\controller\SiteController;
/**
 * 单页面
 */
class CategoryController extends SiteController {

	/**
     * 栏目页
     */
    public function index(){
    	$classId = request('get.class_id',0,'intval');
        $urlName = request('get.urlname');
        if (empty($classId)&&empty($urlName)) {
            $this->error404();
        }
        //获取栏目信息
        $model = target('Page/CategoryPage');
        if(!empty($classId)){
            $categoryInfo=$model->getInfo($classId);
        }else if(!empty($urlName)){
        	$map = array();
        	$map['urlname'] = $urlName;
            $categoryInfo=$model->getWhereInfo($map);
        }else{
            $this->error404();
        }

        $classId = $categoryInfo['class_id'];
        //信息判断
        if (!is_array($categoryInfo)){
            $this->error404();
        }
        if($categoryInfo['app']<>APP_NAME){
            $this->error404();
        }
        //位置导航
        $crumb = target('duxcms/Category')->loadCrumb($classId);
        //内容处理
        $categoryInfo['content'] = html_out($categoryInfo['content']);
        //查询上级栏目信息
        $parentCategoryInfo = target('duxcms/Category')->getInfo($categoryInfo['parent_id']);
        //获取顶级栏目信息
        $topCategoryInfo = target('duxcms/Category')->getInfo($crumb[0]['class_id']);
        //MEDIA信息
        $media = $this->getMedia($categoryInfo['name'],$categoryInfo['keywords'],$categoryInfo['description']);
        //模板赋值
        $this->assign('categoryInfo', $categoryInfo);
        $this->assign('parentCategoryInfo', $parentCategoryInfo);
        $this->assign('topCategoryInfo', $topCategoryInfo);
        $this->assign('crumb', $crumb);
        $this->assign('media', $media);
        $this->siteDisplay($categoryInfo['class_tpl']);
    }
}