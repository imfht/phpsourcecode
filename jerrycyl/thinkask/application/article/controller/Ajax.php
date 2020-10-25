<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\article\controller;
use app\common\controller\Base;


class Ajax extends base
{
	public function _initialize()
    {
       //用户是否登陆
       if(!$this->getuid()||!$this->request->isAJax()){
        $this->error('请先登陆');
       }
    }
    /**
     * [edit 编辑]
     * @return [type] [description]
     */
    public function edit(){
       $id = (int)current($this->request->only(['id']));
       //字段判断
       if($title = $this->request->only(['title'])){
            if(empty($title['title'])){
                $this->error('标题不能为空');
            }
       }
        if($category_id = $this->request->only(['category_id'])){
            if(empty($category_id['category_id'])||$category_id['category_id']<1){
                $this->error('请选择分类');
            }
       }
       //内容
       if($message = $this->request->only(['message'])){
        //文章内容必填但问题不需要
            if(empty($message['message'])){
                $this->error('内容不能为空');
            }
       }
       $ardb = $this->request->param();
       $ardb['add_time'] = time();
       $ardb['uid'] = $this->getuid();
       $ardb['category_id'] = $ardb['category_id'];
       if($id>0){
        //修改
         model('Base')->getedit('article',['where'=>"id=$id"],$ardb);
         model('Base')->getedit('posts_index',['where'=>"post_id=$id"],['update_time'=>time()]);
        $this->success('操作成功',url('index/article/index')."?id=".$id);
       }else{
        // 新加
          $id = model('Base')->getadd('article',$ardb);
          if($id){
              //POST_INDEX
              $data['post_id']  =$id;
              $data['post_type']="article";
              $data['add_time']=$ardb['add_time'];
              $data['update_time']=time();
              //是否匿名
              $data['uid']=$this->getuid();
              model('Base')->getadd('posts_index',$data);
              $this->success('操作成功',"/article/{$id}.html");
            
          }

       }
      }
      public function lock(){
          
      }
      /**
       * [comment 评论]
       * @return [type] [description]
       */
      public function comment(){
        //文章存在才给评论
        $id = $this->request->only(['id']);
        // show($id);
        if (model('base')->getone('article',['where'=>$id])) {
            $data = $this->request->param();
            $data['uid'] = $this->getuid();
            $data['add_time'] = time();
            $data['article_id'] = $data['id'];
            $data['message'] = $data['comment'];
            unset($data['id']);
            if(model('base')->getadd('article_comments',$data)){
              $this->success('评论成功');
            }else{
              $this->error('服务器繁忙，稍后再试');
            }
        }
      }
      /**
       * [comment_edit 评论修改]
       * @return [type] [description]
       */
      public function comment_edit(){
        $id = $this->request->only(['id']);
        $data = $this->request->only(['comment']);
        $message['message'] = $data['comment'];
        model('base')->getedit('article_comments',['where'=>$id],$message);
        $this->success('成功');


      }
      /**
       * [comment_zhan 评论点赞]
       * @return [type] [description]
       */
      public function comment_zhan(){
        $id = $this->request->only(['id']);
        $info = model('base')->getone('article_comments',['where'=>$id]);
        //是否和评论者为一人
        if($info['uid']==$this->getuid()){
          $this->error('您不能给自已点赞');
        }else{
          //是否点过赞
            $where['item_id'] = $id['id'];
            $where['uid'] = $this->getuid();
          if(model('base')->getone('article_vote',['where'=>$where])){
             $this->error('您不能重复点赞');
          }else{
            $data['uid'] = $this->getuid();
            $data['type'] = "comment";
            $data['rating'] = 1;
            $data['time'] = time();
            $data['item_id'] = $id['id'];
            $data['item_uid'] = $info['uid'];
            if(model('base')->getadd('article_vote',$data)){
              $vote['votes'] =$info['votes']+1; 
              model('base')->getedit('article_comments',['where'=>$id],$vote);

            }
            $this->success('成功');

          }
          
        }

      }

}
