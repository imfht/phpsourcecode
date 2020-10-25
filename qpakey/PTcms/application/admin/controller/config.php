<?php
//配置项管理
class ConfigController extends AdminController{

    /**
     * @var ConfigModel
     */
    protected $model;
    public function init() {
        parent::init();
        $this->model=$this->model('config');
    }

    public function indexAction() {
        $where=$this->_parsemap();
        $this->view->page=$this->input->get('page','int',1);
        $this->view->pagesize=$this->config->get('admin_pagesie',20);
        $this->view->list=$this->model->field('id,title,key,intro,group,create_user_id,update_user_id,create_time,update_time,status')->where($where)->page($this->view->page)->limit($this->view->pagesize)->getlist();
        $this->view->totalnum=$this->model->where($where)->count();
        $this->view->pagenum=ceil($this->view->totalnum/$this->view->pagesize);
        if ($this->request->isAjax()){
            $this->ajax(array('data'=>$this->view->list,'totalnum'=>$this->view->totalnum,'pagenum'=>$this->view->pagenum));
        }
    }

    public function addAction() {
        if ($this->request->ispost()){
            $param['title']=$this->input->post('title','str','');
            $param['key']=$this->input->post('key','en','');
            $param['intro']=$this->input->post('intro','str','');
            $param['value']=$this->input->post('value','str','');
            $param['extra']=$this->input->post('extra','str','');
            $param['type']=$this->input->post('type','en','');
            $param['group']=$this->input->post('group','int',0);
            $param['ordernum']=$this->input->post('ordernum','int',50);
            $param['level']=$this->input->post('level','int',1);
            $param['status']=$this->input->post('status','int',1);
            if($this->model->add($param)){
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败');
            }
        }
        $this->view->grouplist=$this->config->get('config_group');
    }

    public function editAction() {
        $id=$this->input->request('id','int',0);
        $info=$this->model->where(array('id'=>$id))->find();
        if ($this->request->ispost()){
            $param['title']=$this->input->post('title','str','');
            $param['key']=$this->input->post('key','en','');
            $param['intro']=$this->input->post('intro','str','');
            $param['value']=$this->input->post('value','str','');
            $param['extra']=$this->input->post('extra','str','');
            $param['type']=$this->input->post('type','en','');
            $param['group']=$this->input->post('group','int',0);
            $param['ordernum']=$this->input->post('ordernum','int',50);
            $param['level']=$this->input->post('level','int',1);
            $param['status']=$this->input->post('status','int',1);
            $param['id']=$id;
            if ($this->model->edit($param)){
                $this->success('修改成功',U('index'));
            }else{
                $this->error('修改失败');
            }
        }
        $this->view->info=$info;
        $this->view->grouplist=$this->config->get('config_group');
    }

    public function setAction(){
        if($this->request->ispost()){
            foreach($_POST as $k=>$v){
                $this->model->where(array('key'=>$k))->edit(array('value'=>$v));
            }
            $this->success('操作成功');
        }
        $grouplist=$this->config->get('config_group');
        $data=array();
        foreach($grouplist as $k=>$v){
            if ($k>0){
                $data[$k]['name']=$v;
                $data[$k]['list']=$this->model->where(array('status'=>1,'group'=>$k))->order('ordernum asc,id asc')->field('title,key,value,type,extra,intro')->select();
                foreach($data[$k]['list'] as &$item){
                    if (in_array($item['type'],array('radio','checkbox'))){
                        $value=($item['type']=='checkbox')?explode(',',trim($item['value'])):array($item['value']);
                        $tmp=explode("\n",trim($item['extra']));
                        foreach($tmp as $q){
                            $t=explode(':',trim($q));
                            $item['list'][]=array(
                                'value'=>$t['0'],
                                'title'=>empty($t['1'])?'___':$t['1'],
                                'status'=>in_array($t['0'],$value)?'checked':'',
                            );
                        }
                    }elseif($item['type']=='select'){
                        $tmp=json_decode($item['extra'],true);
                        foreach($tmp as $kkk=>$vvv){
                            $item['list'][]=array(
                                'value'=>$kkk,
                                'title'=>$vvv,
                                'status'=>($item['value']==$kkk)?'selected':'',
                            );
                        }
                    }
                }
            }
        }
        $this->view->list=$data;
    }

}