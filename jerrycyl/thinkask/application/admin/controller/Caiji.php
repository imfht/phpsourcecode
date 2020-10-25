<?php
namespace app\Admin\controller;
use app\common\controller\AdminBase;
use QL\QueryList;
class Caiji extends AdminBase{

	public function index(){
    $map = $this->getMap();
    $order = $this->getOrder();
    $data = $this->getbase->getdb('tool_caiji')
                          ->order($order)
                          ->where($map)
                          ->paginate();
    // 分页数据
    $page = $data->render();
      // 使用ZBuilder构建数据表格
    return $this->builder('table')
    ->setPageTitle('采集规则列表')
    // ->setSearch(['id' => 'id', 'title' => '导航标题']) // 设置搜索参数
    ->setTableName('tool_caiji')
    ->setPrimaryKey('id')
    ->addOrder('id')
    ->addColumn('id', 'id')
    ->addColumn('title', '标题')
    ->addColumn('star_id', '开始的ID')
    ->addColumn('end_id', '结束的ID')
    ->addColumn('title_choose', 'TITLE选择器')
    ->addColumn('title_attr', 'TITL属性')
    ->addColumn('answer_choose', '回答选择器')
    ->addColumn('answer_attr', '回答属性')
    ->addColumn('url', '采集的URL')
    ->addColumn('type', '类型','text','',['article'=>'文章','question'=>'问题'])

    ->addColumn('right_button', '操作', 'btn')
    ->addTopButton('edit',['class'=>"btn btn-default",'title'=>'添加规则','href'=>'/admin/caiji/edit/']) // 添加顶部按钮
    ->addRightButtons([
                        'caiji' =>['data-tips' => '马上开始执行采集','href'=>'/admin/caiji/docaiji/id/__id__','icon'=>'fa fa-chain','class'=>'btn btn-default btn-xs','title'=>'开始采集'],
                        'edit'=>['href'=>'/admin/caiji/edit/id/__id__'], 
                        'delete' => ['data-tips' => '删除后无法恢复。','field'=>'id'],
                        
                        ]) 
    ->setRowList($data) // 设置表格数据
    ->setRowList($re) // 设置表格数据
    ->setPages($page) // 设置分页数据
    ->fetch();
	}
	 /**
    * [edit  规则添加]
    * @return [type] [description]
    */
   public function edit(){
     if($id = input('id')){
      $data = $this->getbase->getdb('tool_caiji')->where("id = '{$id}'")->find();
      extract($data);
    }
    return $this->builder('form')
      ->setUrl(url('systems/ajax/tmkedit'))
      ->addHidden('field','id')
      ->addHidden('gourl','/admin/caiji/index/')
      ->addHidden('id',$id)
      ->addHidden('table','tool_caiji')
      ->setPageTitle('采集规则处理')
      ->addText('title', '标题', '',$title)
      ->addRadio('type', '所属类型', '',['article'=>'文章','question'=>'问题'],$type?$type:"question")
      ->addText('url', '采集的URL', '',$url)
      ->addNumber('star_id', 'id选择范围S', '',$star_id?$star_id:1)
      ->addNumber('end_id', 'id选择范围E', '',$end_id?$end_id:2000)
       ->addNumber('star_uid', '用户IDS', '',$star_uid?$star_uid:20)
      ->addNumber('end_uid', '用户IDE', '',$end_uid?$end_uid:700)
      ->addDaterange('creat_star_time,creat_end_time', '问题用户发起时间的范围','',"{$creat_star_time},{$creat_end_time}")
      ->addDaterange('answer_star_time,answer_end_time', '用户回答时间的范围','',"{$answer_star_time},{$answer_end_time}")
      ->addText('title_choose', '标题选择器', '',$title_choose)
      ->addRadio('title_attr', '标题选择器属性', '',['text'=>'text','html'=>'html','img'=>'img','link'=>'link'],$title_attr?$title_attr:"text")
      ->addText('tag_choose', 'TAG选择器', '',$tag_choose)
      ->addRadio('tag_attr', 'TAG选择器属性', '',['text'=>'text','html'=>'html','img'=>'img','link'=>'link'],$tag_attr?$tag_attr:"text")
      ->addText('content_choose', '内容选择器', '',$content_choose)
      ->addRadio('content_attr', '内容选择器属性', '',['text'=>'text','html'=>'html','img'=>'img','link'=>'link'],$content_attr?$content_attr:"html")
      ->addText('answer_choose', '回答选择器', '',$answer_choose)
      ->addRadio('answer_attr', '回答选择器属性', '',['text'=>'text','html'=>'html','img'=>'img','link'=>'link'],$answer_attr?$answer_attr:"html")
      ->addValidate([
                  'title'          => 'require|max:50',
                  'title_choose'   => 'require',
                  'title_attr'   => 'require',
                  'tag_choose'   => 'require',
                  'tag_attr'   => 'require',
                  'content_choose'   => 'require',
                  'content_attr'   => 'require',
                  'answer_choose'   => 'require',
                  'answer_attr'   => 'require',
                    ],[
                  'name.require'           => 'title为必填',
                  'name.max'               => 'title不能超过50个字符',
                  'title_attr.require'     => '标题选择器属性为必填',
                  'title_choose.require'   => '标题选择器为必填',
                  'tag_choose.require'     => 'TAG选择器为必填',
                  'tag_attr.require'       => 'TAG选择器属性为必填',
                  'content_choose.require' => '内容选择器为必填',
                  'content_attr.require'   => '内容选择器属性为必填',
                  'answer_choose.require'  => '回答选择器为必填',
                  'answer_attr.require'    => '回答选择器属性为必填',
                    ])
      ->fetch(); 

    $id = $this->request->only(['id']);
    $id = (int)$id['id'];
    if($id>0){
      
      $this->assign($info = model('Base')->getone('tool_caiji',['where'=>['id'=>$id],'cache'=>false]));
      // show($info);
    }
    $this->assign('category',model('Base')->getall('category',['cache'=>false]));
   return $this->fetch('admin/caiji/edit'); 
   }
   public function test(){
    echo "测试选择的ID是开始的ID值，所有请保证开始ID的文章可以访问";
    //hash不存在时查找规则信息
      $id = $this->request->only(['id']);
      $id = (int)$id['id'];
      if($id>0){
        $data = model('Base')->getone('tool_caiji',['where'=>['id'=>$id],'cache'=>false]);
        }else{
          die('id参数错误');
        }
        // show($data);
    show($this->simplequery($data));
   }
   public function docaiji(){
    
    $hash = $this->request->only(['hash']);
    if($hash){
      //开始执行
      $s = $this->request->only(['s']);
      $e = $this->request->only(['e']);
      //开始条大于结尾条，采集完成
      if($s['s']>$e['e']){
          $this->success('采集完成',url('admin/caiji/index'));
      }
      echo "开始采集第".$s['s']."条";
      //采信的相关规则信息
      $data = session($hash['hash']);
      $data['star_id'] = $s['s'];
      //采集成功时
      if($re = $this->simplequery($data)){
         // 区分文章还是问题分开插入
         if($data['type']=="question"){
             if($this->insertquestion($re,$hash['hash'])){
               $s['s']++;
                $data['star_id'] = $s['s'];
                gourl($this->creaturl($hash['hash'],$data)); 
            }
         }else{
             if($this->insertarticle($re,$hash['hash'])){
               $s['s']++;
              $data['star_id'] = $s['s'];
              gourl($this->creaturl($hash['hash'],$data)); 
          }
         }
         

      }else{
        // 为空，说明没有采集到数据，或者说数据ID不存在
        $s['s']++;
        $data['star_id'] = $s['s'];
        // show($data);
       gourl($this->creaturl($hash['hash'],$data));
      }


    }else{
      //hash不存在时查找规则信息
      $id = $this->request->only(['id']);
      $id = (int)$id['id'];
      if($id>0){
        $caiji = model('Base')->getone('tool_caiji',['where'=>['id'=>$id],'cache'=>false]);
        if($caiji){
         
          $hash = "caiji".rand_str('3',1);
          session($hash,$caiji);
          gourl($this->creaturl($hash,$caiji));
        }else{
          $this->error('没有些规则');
        }
      }else{
         $this->error('没有指定ID');
      }
    }
    
   }
   //跳转的URL方便后面维护
   private function creaturl($hash,$data){
    return url('admin/caiji/docaiji',['hash'=>$hash,'s'=>$data['star_id'],'e'=>$data['end_id']]);
   }
   /**
    * [simplequery 简单的采集版本单一版]
    * @param  [type] $data [description]
    * @return [type]       [description]
    */
   private function simplequery($datas){
    require EXTEND_PATH.'querylist/vendor/autoload.php';
    // //需要采集的目标页面
    // show($data);
    $page = $datas['url'].$datas['star_id'];
    // show($page);
    //可以先手动获取要采集的页面源码
    $html = @file_get_contents($page);
    // print($html);
    // show($html);
    // show($page);
    // die;
    if($html){
         //然后可以把页面源码或者HTML片段传给QueryList
        // $data = QueryList::Query($html,array(
        //     'title' => array($data['title_choose'],$data['title_attr']),
        //     'anwser' => array($data['answer_choose'],$data['answer_attr']),
        //     'content' => array($data['content_choose'],$data['content_attr']),
        //     'tags' => array($data['tag_choose'],$data['tag_attr']),
        //     ))->data;
        // show($data);
        $data = QueryList::Query($html,array(
              'title' => array($datas['title_choose'],$datas['title_attr']),
            'anwser' => array($datas['answer_choose'],$datas['answer_attr']),
            'content' => array($datas['content_choose'],$datas['content_attr']),
            'tags' => array($datas['tag_choose'],$datas['tag_attr']),
              ))->data;
        $newdata=[];
        // show($data);
        // show($newdata);
        // die;
        $title = "";
        $content = "";
        $anwser=[];
        $tags=[];
        foreach ($data as $key => $v) {
          if($v['anwser']){
            $anwser[]=htmlspecialchars($v['anwser']);
          }
          if($v['tags']){
            $tags[]=htmlspecialchars($v['tags']);
          }
          if($v['title']){
            $title=htmlspecialchars($v['title']);
          }
          if($v['content']){
            $content=htmlspecialchars($v['content']);
          }
        }
        $newdata['anwser'] = $anwser;
        $newdata['tags'] = $tags;
        $newdata['title'] = $title;
        $newdata['content'] = $content;
        // show($data);
        // show($newdata);
        // die;
        //打印结果
        return $newdata;
      }else{
        return [];
      }
   

   }
   private function insertarticle($data,$hash){
    //规则数据
    $gz = session($hash);
    //回答计数
    $comments = count($data['anwser'])>0?count($data['anwser']):0;
    $tags = count($data['tags'])>0?count($data['tags']):0;

    // show($data);
    // show($comments);
    // die;
    $quesdb['title'] = $data['title'];
    $quesdb['message'] = $data['content'];
    $quesdb['add_time'] = rand(strtotime($gz['creat_star_time']),strtotime($gz['creat_end_time']));
    $quesdb['comments'] = $comments;
    $quesdb['answer_users'] = $comments;

    $quesdb['views'] = $comments+rand(1,1000);
    $quesdb['comment_count'] = $comments;
    $quesdb['category_id'] = $gz['category_id'];
    $quesdb['uid'] = rand($gz['start_uid'],$gz['end_uid']);
    if(model('Base')->getcount('article',['where'=>["title"=>"{$quesdb['title']}"]])){
      echo "存在的标题数据，不采集";
      return true;
    }
    //插入文章
    $id = model('Base')->getadd('article',$quesdb);
    if($id){
      //插入评论
      if($comments){
        // show($data['anwser']);
        // die;
        foreach ($data['anwser'] as $key => $v) {
          $commendb['article_id'] = $id;
          $commendb['uid'] = rand($gz['start_uid'],$gz['end_uid']);
          $commendb['at_uid'] = $quesdb['uid'];
          $commendb['message'] = $v;
          $commendb['add_time'] = rand(strtotime($gz['answer_star_time']),strtotime($gz['answer_end_time']));
          model('Base')->getadd('article_comments',$commendb);

        }
      }
      //标签
      //标签关注
      //thinkask_topic
      //thinkask_topic_focus
      //thinkask_topic_relation
      if($tags){
        foreach ($data['tags'] as $key => $v) {
          $topic['topic_title'] = $v;
          $topic['add_time'] = rand(strtotime($gz['creat_star_time']),strtotime($gz['creat_end_time']));
          //判断标签是否存在
           if($topicinfo = model('Base')->getcount('topic',['where'=>["topic_title"=>"{$v}"]])){
            $topic_id = $topicinfo['topic_id'];
           }else{
            $topic_id = model('Base')->getadd('topic',$topic);
           }

          if($topic_id){
            //是否关注了
            ////thinkask_topic_focus
              if(!$focus = model('Base')->getcount('topic_focus',['where'=>["topic_id"=>"{$topic_id}",'uid'=>"{$quesdb['uid']}"]])){
                $topic_focus['topic_id'] = $topic_id;
                $topic_focus['uid'] = $quesdb['uid'];
                $topic_focus['add_time'] = time();
                model('Base')->getadd('topic_focus',$topic_focus);
              }
              //thinkask_topic_relation
              $topic_relation['topic_id'] = $topic_id;
              $topic_relation['item_id'] = $id;
              $topic_relation['add_time'] = time();
              $topic_relation['uid']  = $quesdb['uid'];
              $topic_relation['type'] = "article";
              $topic_re_id = model('Base')->getadd('topic_relation',$topic_relation);
          
          }
          // $commendb['article_id'] = $id;
          // $commendb['at_uid'] = rand($gz['start_uid'],$gz['end_uid']);
          // $commendb['message'] = $v;
          // $commendb['add_time'] = rand(strtotime($gz['answer_star_time']),strtotime($gz['answer_end_time']));
          // model('Base')->getadd('question_comments',$commendb);

        }
      }
       return true;
    }
   }
   /**
    * [insertquestion 插入问题]
    * @param  [type] $data [description]
    * @param  [type] $hash [description]
    * @return [type]       [description]
    */
   private function insertquestion($data,$hash){
    //规则数据
    $gz = session($hash);
    //回答计数
     $answer_count = count($data['anwser'])>0?count($data['anwser']):0;
    $tags = count($data['tags'])>0?count($data['tags']):0;
    $quesdb['question_content'] = $data['title'];
    $quesdb['question_detail'] = $data['content'];
    $quesdb['add_time'] = rand(strtotime($gz['creat_star_time']),strtotime($gz['creat_end_time']));
    $quesdb['answer_count'] = $answer_count;
    $quesdb['answer_users'] = $answer_count;

    $quesdb['view_count'] = $answer_count+rand(1,1000);
    $quesdb['comment_count'] = $answer_count;
    $quesdb['category_id'] = $gz['category_id'];
    $quesdb['published_uid'] = rand($gz['start_uid'],$gz['end_uid']);
    if(model('Base')->getcount('question',['where'=>["question_content"=>"{$quesdb['question_content']}"]])){
      echo "存在的标题数据，不采集";
      return true;
    }
    // die;
    //插入问题
    $id = model('Base')->getadd('question',$quesdb);
    if($id){
      //回答
      if($answer_count){
        foreach ($data['anwser'] as $key => $v) {
          $commendb['question_id'] = $id;
          $commendb['uid'] = rand($gz['start_uid'],$gz['end_uid']);
          $commendb['answer_content'] = $v;
          $commendb['add_time'] = rand(strtotime($gz['answer_star_time']),strtotime($gz['answer_end_time']));
          $commendb['category_id'] = $gz['category_id'];
          // $commendb['ip'] = $gz['category_id'];
          
         model('Base')->getadd('answer',$commendb);

        }
      }
      //标签
      //标签关注
      //thinkask_topic
      //thinkask_topic_focus
      //thinkask_topic_relation
      if($tags){
        foreach ($data['tags'] as $key => $v) {
          $topic['topic_title'] = $v;
          $topic['add_time'] = rand(strtotime($gz['creat_star_time']),strtotime($gz['creat_end_time']));
          //判断标签是否存在
           if($topicinfo = model('Base')->getcount('topic',['where'=>["topic_title"=>"{$v}"]])){
            $topic_id = $topicinfo['topic_id'];
           }else{
            $topic_id = model('Base')->getadd('topic',$topic);
           }

          if($topic_id){
            //是否关注了
            ////thinkask_topic_focus
              if(!$focus = model('Base')->getcount('topic_focus',['where'=>["topic_id"=>"{$topic_id}",'uid'=>"{$quesdb['uid']}"]])){
                $topic_focus['topic_id'] = $topic_id;
                $topic_focus['uid'] = $quesdb['uid'];
                $topic_focus['add_time'] = time();
                model('Base')->getadd('topic_focus',$topic_focus);
              }
              //thinkask_topic_relation
              $topic_relation['topic_id'] = $topic_id;
              $topic_relation['item_id'] = $id;
              $topic_relation['add_time'] = time();
              $topic_relation['uid']  = $quesdb['uid'];
              $topic_relation['type'] = "question";
              $topic_re_id = model('Base')->getadd('topic_relation',$topic_relation);
          
          }
          $commendb['article_id'] = $id;
          $commendb['at_uid'] = rand($gz['start_uid'],$gz['end_uid']);
          $commendb['message'] = $v;
          $commendb['add_time'] = rand(strtotime($gz['answer_star_time']),strtotime($gz['answer_end_time']));
          model('Base')->getadd('question_comments',$commendb);

        }
      }

       return true;
    }
   }
   /**
    * [php_shouche php手册]
    * @Author   Jerry
    * @DateTime 2017-05-24T13:02:31+0800
    * @Example  eg:
    * @return   [type]                   [description]
    * 3572 --  12755
    */
   public function php_shouche(){
    require EXTEND_PATH.'querylist/vendor/autoload.php';
    // //需要采集的目标页面
    $page = "http://www.shouce.ren/api/view/a/3572";
    $html = @file_get_contents($page);
    $data = QueryList::Query($html,array(
            'title' => array("h1.text-center","text"),
            'content' => array("#post-content","html"),
              ))->data;
    dump($data);

   }


    public function install(){//安装方法必须实现
        return true;//安装成功返回true，失败false
    }

    public function uninstall(){//卸载方法必须实现
        return true;//卸载成功返回true，失败false
    }
    
  

}
