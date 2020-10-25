<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;

//辅栏目内容管理
abstract class Info extends AdminBase
{
    use AddEditList;
    
    protected $validate = '';
    protected $model;
    protected $m_model;
    protected $c_model;
    protected $s_model;
    protected $category_model;
    protected $form_items;
    
    protected $list_items;
    protected $tab_ext;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'info');
        $this->c_model = get_model_class($dirname,'content');
        $this->s_model = get_model_class($dirname,'sort');
        $this->m_model = get_model_class($dirname,'module');
        $this->category_model = get_model_class($dirname,'category');
        $this->set_config();
    }
    
    protected function set_config(){
        $this->list_items = [
                ['title', '标题', 'link', iurl('content/show',['id'=>'__aid__']),'_blank',''],
                ['list', '排序值',  'text.edit'],
                ['cid', '所属辅栏目',  'select',$this->category_model->getTitleList()],
                ['fid', '所属主栏目',  'select2',$this->s_model->getTitleList()],
                ['mid', '所属模型', 'select2',$this->m_model->getTitleList()],
        ];
        
        $this->tab_ext = [
                'page_title'=>'辅栏目内容管理',
                'top_button'=>[
                        ['type'=>'delete']
                ],
        ];
    }
    
    protected function getListData($map = [], $order = '',$rows=20,$pages=[])
    {
        $cid_array = [];
        if(!empty($map['cid'])){    //把子栏目也取出来
            $cid_array = $this->category_model->getSonsId($map['cid']);
        }
        
        $map = array_merge($this->getMap(),$map);
        $order || $order='list DESC,id DESC';
        
        if(!empty($cid_array)){
            unset($map['cid']);
            $cid_array = array_merge($cid_array,[$map['cid']]);            
            $data_list = $this->model->where($map)->where('cid','in',$cid_array)->group('aid')->order($order)->paginate($rows);
        }else{
            $data_list = $this->model->where($map)->order($order)->paginate($rows);
        }
        
        foreach ($data_list AS $key=>$rs){
            //获取内容的详细数据
            $info = $this->c_model->getInfoById($rs['aid']);
            if ($info) {
                $data_list[$key] = array_merge($info,getArray($rs));
            }else{
                $this->model->where('id',$rs['id'])->delete();
                unset($data_list[$key]);
            }
        }
        return $data_list;
    }
    
    public function index($cid=0)
    {
        $data = $this->getListData($cid?['cid'=>$cid]:[]);
        return $this->getAdminTable($data);
    }
    
    public function edit($id = null)
    {
        if (empty($id)) $this->error('缺少参数');
        $info = $this->model->get($id);
        
        header("location:".auto_url('content/edit',['id'=>$info['aid']]));exit;
    }
    
    public function add()
    {
        
        $data = get_post();
        $ids = $data['ids'];
        if(empty($ids)){
            $this->error('参数不存在');
        }
        
        if(IS_POST){
            if(empty($data['fid'])){
                $this->error('栏目参数不存在');
            }
            if ($num = $this->model->save_data($ids,$data['fid']) ) {
                $this->success('成功添加'.$num.'条数据', auto_url('index'));
            } else {
                $this->error('请不要重复添加');
            }
        }
        
        $this->tab_ext = [
                'page_title'=>'辅栏目添加内容',
        ];
        
        //if (is_array($ids)) {
        //    $ids = implode(',', $ids);
        //}
        
        $this->form_items = [
                // ['hidden', 'ids',$ids],
                ['select', 'fid', '归属哪个栏目','',$this->category_model->getTreeTitle()],
        ];
        
        return $this->addContent();
    }
}