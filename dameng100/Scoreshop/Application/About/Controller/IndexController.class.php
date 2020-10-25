<?php

namespace About\Controller;
use Common\Controller\CommonController;

class IndexController extends CommonController{

    protected $aboutModel;
    protected $aboutCategoryModel;

    function _initialize()
    {
        parent::_initialize();
        $this->aboutModel = D('About/About');
        $this->aboutCategoryModel = D('About/AboutCategory');

        $catTitle=modC('PAPER_CATEGORY_TITLE','网站简介','About');

        $catList=$this->aboutCategoryModel->getCategoryList(array('status'=>1));
        if(count($catList)){
            $cat_ids=array_column($catList,'id');
            $catList=array_combine($cat_ids,$catList);
            $map['category']=array('in',array_merge($cat_ids,array(0)));
        }else{
            $map['category']=0;
            $catList=array();
        }
        $map['status']=1;
        $aboutArtiles=$this->aboutModel->getList($map,'id,title,sort,category,template');
        foreach($aboutArtiles as $val){
            $val['type']='article';
            if($val['category']==0){
                $catList[]=$val;
            }else{
                $catList[$val['category']]['children'][]=$val;
            }
        }
        $catListSort=list_sort_by($catList,'sort');
        $this->assign('cat_list',$catListSort);
    }

    public function index()
    {
        $aId=I('id',0,'intval');
        $defaultId = modC('ABOUT_DEFAULT_ID','默认id','about');
        if($aId==0){
            /*
            foreach($catList as $val){
                if($val['type']=='article'){
                    $aId=$val['id'];
                    break;
                }else{
                    if($val['children'][0]['id']){
                        $aId=$val['children'][0]['id'];
                        break;
                    }
                }
            }
            */
            $aId = $defaultId;
        }

        if($aId){
            $aboutArtiles=array_combine(array_column($aboutArtiles,'id'),$aboutArtiles);
            $contentTitle=$aboutArtiles[$aId];
            $this->assign('content_title',$contentTitle);
            if($aboutArtiles[$aId]['category']!=0){
                $cate=$catList[$aboutArtiles[$aId]['category']];
                $this->assign('cate',$cate);
                $this->assign('top_id',$cate['id']);

            }else{
                $this->assign('top_id',0);
                $this->assign('id',$aId);
            }
            $data=$this->aboutModel->getData($aId);
        }
        
        /* 获取模板 */
        if (!empty($data['template'])) { //已定制模板
            $tmpl = 'Index/'.$data['template'];
        } else { //使用默认模板
            $tmpl = 'Index/index';
        }
        
        $this->assign('data',$data);
        $this->setTitle('{$data.title|text}');
        $this->setDescription('北京火木科技有限公司简介');
        $this->display($tmpl);
    }
    /**
     * 分类列表
     * @param  integer $page [description]
     * @param  integer $r    [description]
     */
    public function category($page=1,$r=20)
    {
        $category = I('cid',0,'intval');
        if($category){
            $cates=$this->aboutCategoryModel->getCategory(array('id'=>$category));
            if($cates){
                $map['category']=$category;
            }else{
                $this->error('参数错误');
            }
        }else{
            $this->error('参数错误');
        }
        //dump($cates);exit;
        $map['status']=1;

        list($list,$totalCount) = $this->aboutModel->getListByPage($map,$page,'sort desc,update_time desc','*',$r);
        foreach($list as &$val){
            $val['user']=query_user(array('space_url','avatar32','nickname'),$val['uid']);
        }
        unset($val);
        /* 模板赋值并渲染模板 */
        
        $this->assign('list', $list);
        $this->assign('cates', $cates);
        $this->setTitle('{$cates.title|text}');
        $this->assign('totalCount',$totalCount);
        $this->display();
    }

    public function feedBack() 
    {
        if(IS_POST){
            $aId = $_GET['id']; //获取当前文档ID
            $data['email']=I('post.email','','text');
            $data['content']=I('post.content','','text');
            $data['create_time']=time();

            // 自动验证规则
            $rules = array(
                array('email','require','email不能为空！',1),
                array('email','email','邮箱格式不正确！'),
                array('content','require','内容不能为空',1),
                array('content','20,500','内容必须大于20个字符',0,length),
            );
            $feedBack=M('feedback');
            // 创建数据对象
            if ($feedBack->validate($rules)->create($data)){
                // 创建数据对象成功，写入数据
                $result=$feedBack->add();
                if($result){
                    $this->success('反馈成功！',U('About/Index/index',array('id'=>$aId)));
                }else{
                    $this->error('反馈失败！');
                }
            }else{
                // 创建数据对象失败
                $this->error($feedBack->getError());
            }

        }else{
            $this->display();
        }

    }
} 