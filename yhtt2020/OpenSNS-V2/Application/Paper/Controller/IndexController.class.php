<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-28
 * Time: 下午01:33
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Paper\Controller;


use Think\Controller;

class IndexController extends Controller{

    protected $paperModel;
    protected $paperCategoryModel;

    function _initialize()
    {
        $this->paperModel = D('Paper/Paper');
        $this->paperCategoryModel = D('Paper/PaperCategory');

        $catTitle=modC('PAPER_CATEGORY_TITLE',L('_MODULE_'),'Paper');

        $sub_menu['left'][]= array('tab' => 'home', 'title' => $catTitle, 'href' =>  U('index'));
        $sub_menu['first']=array('title'=>L('_MODULE_'));
        $this->assign('sub_menu', $sub_menu);
        $this->assign('current','home');
    }

    public function index()
    {
        $catList=$this->paperCategoryModel->getCategoryList(array('status'=>1));
        if(count($catList)){
            $cat_ids=array_column($catList,'id');
            $catList=array_combine($cat_ids,$catList);
            $map['category']=array('in',array_merge($cat_ids,array(0)));
        }else{
            $map['category']=0;
            $catList=array();
        }
        $map['status']=1;
        $pageArtiles=$this->paperModel->getList($map,'id,title,sort,category');
        foreach($pageArtiles as $val){
            $val['type']='article';
            if($val['category']==0){
                $catList[]=$val;
            }else{
                $catList[$val['category']]['children'][]=$val;
            }
        }
        $catListSort=list_sort_by($catList,'sort');
        $this->assign('cat_list',$catListSort);

        $aId=I('id',0,'intval');
        if($aId==0){
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
        }
        if($aId){
            $pageArtiles=array_combine(array_column($pageArtiles,'id'),$pageArtiles);
            $contentTitle=$pageArtiles[$aId];
            $this->assign('content_title',$contentTitle);
            if($pageArtiles[$aId]['category']!=0){
                $cate=$catList[$pageArtiles[$aId]['category']];
                $this->assign('cate',$cate);
                $this->assign('top_id',$cate['id']);
            }else{
                $this->assign('top_id',0);
                $this->assign('id',$aId);
            }
        }

        $data=$this->paperModel->getData($aId);
        $this->assign('data',$data);
        $this->display();
    }
} 