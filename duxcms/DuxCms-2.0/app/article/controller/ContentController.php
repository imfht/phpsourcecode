<?php
namespace app\article\controller;
use app\home\controller\SiteController;
/**
 * 栏目页面
 */

class ContentController extends SiteController {

	/**
     * 栏目页
     */
    public function index()
    {
        $contentId = request('get.content_id',0,'intval');
        $urlTitle = request('get.urltitle');
        if (empty($contentId)&&empty($urlTitle)) {
            $this->error404();
        }
        $model = target('ContentArticle');
        //获取内容信息
        if(!empty($contentId)){
            $contentInfo=$model->getInfo($contentId);
        }else if(!empty($urlTitle)){
            $where = array();
            $where['urltitle'] = $urlTitle;
            $contentInfo=$model->getWhereInfo($where);
        }else{
            $this->error404();
        }
        $contentId = $contentInfo['content_id'];
        //信息判断
        if (!is_array($contentInfo)){
            $this->error404();
        }
        if(!$contentInfo['status']){
            $this->error404();
        }
        //获取栏目信息
        $modelCategory = target('CategoryArticle');
        $categoryInfo=$modelCategory->getInfo($contentInfo['class_id']);
        if (!is_array($categoryInfo)){
            $this->error404();
        }
        if($categoryInfo['app']<>APP_NAME){
            $this->error404();
        };
        //判断跳转
        if (!empty($contentInfo['url']))
        {
            $link = $this->show($contentInfo['url']);
            $this->redirect($link,301);
        }
        //位置导航
        $crumb = target('duxcms/Category')->loadCrumb($contentInfo['class_id']);
        //查询上级栏目信息
        $parentCategoryInfo = target('duxcms/Category')->getInfo($categoryInfo['parent_id']);
        //获取顶级栏目信息
        $topCategoryInfo = target('duxcms/Category')->getInfo($crumb[0]['class_id']);
        //更新访问计数
        $where = array();
        $where['content_id'] = $contentId;
        target('duxcms/Content')->where($where)->setInc('views');
        //内容处理
        $contentInfo['content'] = html_out($contentInfo['content']);
        //扩展模型
        if($categoryInfo['fieldset_id']){
            $extInfo = target('duxcms/FieldsetExpand')->getDataInfo($categoryInfo['fieldset_id'],$contentId);
            $contentInfo = array_merge($contentInfo , (array)$extInfo);
        }
        //上一篇
        $prevWhere = array();
        $prevWhere['A.status'] = 1;
        $prevWhere[] = 'A.time < '.$contentInfo['time'];
        $prevWhere['C.class_id'] = $categoryInfo['class_id'];
        $prevInfo=$model->getWhereInfo($prevWhere,' A.time DESC,A.content_id DESC');
        if(!empty($prevInfo)){
            $prevInfo['aurl']=target('duxcms/Content')->getUrl($prevInfo,$appConfig);
            $prevInfo['curl']=target('duxcms/Category')->getUrl($prevInfo,$appConfig);
        }
        //下一篇
        $nextWhere = array();
        $nextWhere['A.status'] = 1;
        $nextWhere[] = 'A.time > '.$contentInfo['time'];
        $nextWhere['C.class_id'] = $categoryInfo['class_id'];
        $nextInfo=$model->getWhereInfo($nextWhere,' A.time ASC,A.content_id ASC');
        if(!empty($nextInfo)){
            $nextInfo['aurl']=target('duxcms/Content')->getUrl($nextInfo,$appConfig);
            $nextInfo['curl']=target('duxcms/Category')->getUrl($nextInfo,$appConfig);
        }
        //MEDIA信息
        $media = $this->getMedia($contentInfo['title'],$contentInfo['keywords'],$contentInfo['description']);
        //模板赋值
        $this->assign('contentInfo', $contentInfo);
        $this->assign('categoryInfo', $categoryInfo);
        $this->assign('parentCategoryInfo', $parentCategoryInfo);
        $this->assign('topCategoryInfo', $topCategoryInfo);
        $this->assign('crumb', $crumb);
        $this->assign('count', $count);
        $this->assign('page', $page);
        $this->assign('media', $media);
        $this->assign('prevInfo', $prevInfo);
        $this->assign('nextInfo', $nextInfo);
        if($contentInfo['tpl']){
            $this->siteDisplay($contentInfo['tpl']);
        }else{
            $this->siteDisplay($categoryInfo['content_tpl']);
        }
    }
}