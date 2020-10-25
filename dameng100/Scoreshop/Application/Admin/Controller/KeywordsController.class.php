<?php

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

/*
**系统关键字管理控制器
*/
class keywordsController extends AdminController
{
	protected $keywordsModel;

    public function _initialize()
    {
        $this->keywordsModel=D('Common/Keywords');
        parent::_initialize();
    }

	public function index($page=1,$r=20) 
	{	
		$aTitle=I('get.title','','op_t');
		$aOrder=I('get.order','create_time','text');
        $aOrder=$aOrder.' desc';
        $aStatus=I('get.status',0,'intval');
        switch($aStatus){
            case 1:
                $map['status']=1;
                break;
            case 2:
                $map['status']=0;
                break;
            default:
                $map['status']=array('in','0,1,-1');
        }
        if($aTitle){
        	$map['title']=$aTitle;
        }

		list($list,$totalCount) = $this->keywordsModel->getListPage($map,$page,$aOrder,$r);

        $status=array('qiyong'=>'');

        $builder = new AdminListBuilder();
        $builder->title(关键字列表);
        $builder->buttonModalPopup(U('Keywords/setTrueDel'),'','彻底删除',array('data-title'=>'是否彻底删除关键字','target-form'=>'ids'));

        $builder->setSelectPostUrl(U('Keywords/index'))
	            ->select('','status','select','','','',array(array('id'=>0,'value'=>'全部'),array('id'=>1,'value'=>'启用'),array('id'=>2,'value'=>'禁用')))
            	->select('排序方式：','order','select','','','',array(array('id'=>'create_time','value'=>'创建时间'),array('id'=>'num','value'=>'数量')))
	            ->search('','title','','关键字','','','');
        $builder->keyText('id', 'ID')
		        ->keyText('title', '关键字')
		        ->keyText('app','App')
                ->keyText('row','应用ID')
		        ->setStatusUrl(U('setKeyStatus'))
		        ->keyStatus()
		        ->keyCreateTime()
		        ->keyDoActionModalPopup('Keywords/setTrueDel?ids=###','删除','操作',array('data-title'=>'是否彻底删除关键字'));

        $builder->data($list);
        $builder->pagination($totalCount, $r);
        $builder->display();
	}

	public function setKeyStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('keywords', $ids, $status);
    }

	public function setTrueDel($ids)
	{
	if(IS_POST){
        $ids=I('post.ids','','text');
        $ids=explode(',',$ids);
        $res=$this->keywordsModel->setTrueDel($ids);
        if($res){
            S('articles_home_data',null);
            $this->success('彻底删除成功！',U('Keywords/index'));
        }else{
            $this->error('操作失败！'.$this->keywordsModel->getError());
        }
	}else{
	    $ids=I('ids');
	    if(is_array($ids)){
            $ids=implode(',',$ids);
        }
	    $this->assign('ids',$ids);
	    $this->display();
	    }

	}


}