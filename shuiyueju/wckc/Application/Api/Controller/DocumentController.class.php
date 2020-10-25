<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 1/16/14
 * Time: 9:40 PM
 */

namespace Api\Controller;
use Addons\Avatar\AvatarAddon;
//use Addons\Digg\DiggAddon;
//use Addons\Favorite\FavoriteAddon;
//use Addons\LocalComment\LocalCommentAddon;

class DocumentController extends ApiController {
    public function viewDocument($document_id, $comment_count=10) {
        //读取文章的详细信息
        $document = $this->getTopicStructure($document_id, $comment_count);
        if(!$document) {
            $this->apiError(2001,"文章不存在");
        }
        //增加浏览次数
        $map = array('id'=>$document_id);
        $data = array('view'=>$document['view_count']+1);
        $model = D('Home/Document');
        $model->where($map)->save($data);
        //返回成功
        $this->apiSuccess("获取成功", null, array('document'=>$document));
    }

    public function diggDocument($document_id) {
        $this->requireLogin();
        //调用赞插件增加赞数量
        $addon = new DiggAddon();
        $digg_count = $addon->vote($this->getUid(), $document_id);
        if($digg_count === false) {
            $this->apiError(2101,"您已经赞过，不能重复赞");
        }
        //返回成功消息
        $this->apiSuccess("操作成功", null, array('digg_count'=>$digg_count));
    }

    public function newFavorite($document_id) {
        $this->requireLogin();
        //将文章添加到收藏夹
        $addon = new FavoriteAddon;
        $model = $addon->getFavoriteModel();
        $result = $model->addFavorite($this->getUid(), $document_id);
        if(!$result) {
            $this->errorCode = 2201;
            $this->error = "收藏失败：".$model->getError();
            return false;
        }
        //增加文章收藏数
        $document = D('Home/Document')->detail($document_id);
        $model_name = D('Admin/Model')->getNameById($document['model_id']);
        if($model_name == 'weibo') {
            D('Home/Weibo','Logic')->where(array('id'=>$document['id']))->save(array('bookmark'=>$document['bookmark']+1));
        }
        //返回收藏编号
        $this->apiSuccess("收藏成功", null, array('favorite_id'=>$result));
    }

    public function newComment($document_id, $content) {
        $this->requireLogin();
        //调用评论插件写入数据库
        $addon = new LocalCommentAddon;
        $model = $addon->getCommentModel();
        $comment_id = $model->addComment($this->getUid(), $document_id, $content);
        if(!$comment_id) {
            $this->apiError(2301,"评论失败");
        }
        //返回成功消息
        $this->apiSuccess("评论成功", null, array('comment_id'=>$comment_id));
    }

    public function listComment($document_id, $offset=2, $count=10) {
        $result = $this->getCommentList($document_id, $offset, $count);
        $totalCount = $this->getCommentCount($document_id);
        $this->apiSuccess("获取成功", null, array('list'=>$result,'total_count'=>$totalCount));
    }

    public function newDocument($content) {
        $this->requireLogin();
        //获取文档默认分类
        $category = M('category')->where(array('name'=>'default_blog','status'=>1))->find();
        $categoryId = $category['id'];
        if(!$categoryId) {
            $this->apiError(0,'找不到默认的文章分类');
        }
        //新建基础文档
        $model_id = D('Admin/Model')->getIdByName('weibo');
        if(!$model_id) {
            $this->apiError(2401,'找不到微博模型');
        }
        $row = array(
            'uid'=>$this->getUid(),
            'name'=>'',
            'title'=>'微博',
            'category_id'=>$categoryId,
            'description'=>'',
            'root'=>0,
            'pid'=>0,
            'model_id'=>$model_id,
            'type'=>2,
            'position'=>0,
            'link_id'=>0,
            'cover_id'=>0,
            'display'=>1,
            'deadline'=>0,
            'attach'=>0,
            'view'=>0,
            'comment'=>0,
            'extend'=>0,
            'level'=>0,
            'create_time'=>time(),
            'update_time'=>time(),
            'status'=>1
        );
        $model = D('Home/Document');
        $document_id = $model->add($row);
        //新建扩展文档
        $row = array(
            'id'=>$document_id,
            'parse'=>0,
            'content'=>$content,
            'bookmark'=>0,
        );
        $model = D('Home/Weibo','Logic');
        $model->add($row);
        //返回结果
        $this->apiSuccess("发表成功", null, array('document_id'=>$document_id));
    }

    public function editDocument($document_id, $content) {
        $this->requireLogin();
        //确认有权限编辑文档
        $document = D('Home/Document')->detail($document_id);
        if($document['uid'] != $this->getUid()) {
            $this->apiError(2501,'您没有编辑权限');
            return false;
        }
        //更新基础文档
        $row['update_time'] = time();
        D('Home/Document')->where(array('id'=>$document_id))->save($row);
        //更新扩展文档
        D('Home/Weibo','Logic')->where(array('id'=>$document_id))->save(array('content'=>$content));
        //返回成功信息
        $this->apiSuccess("编辑成功");
    }
}