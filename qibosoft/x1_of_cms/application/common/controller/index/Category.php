<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase;
use app\common\traits\ModuleContent;

//辅栏目
abstract class Category extends IndexBase
{
    use ModuleContent;
    protected $model;                  //内容
    //protected $mid;                    //模型ID
    protected $c_model;            //模块
    protected $m_model;            //模块
    protected $f_model;              //字段
    protected $s_model;              //栏目

    public function add(){
        die('出错了!');
    }
    public function edit(){
        die('出错了!');
    }
    public function delete(){
        die('出错了!');
    }
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'category');
        $this->s_model = get_model_class($dirname,'sort');
        $this->c_model = get_model_class($dirname,'content');
        $this->m_model = get_model_class($dirname,'module');
        $this->f_model = get_model_class($dirname,'field');
        $this->info_model = get_model_class($dirname,'info');
    }
    
    /**
     * 辅栏目列表页
     * @param number $fid
     * @return mixed|string
     */
    public function index($fid=0)
    {
        if(!$fid){
            $this->error('参数有误！');
        }
        
        //获取列表数据
        $data_list = $this->getListData(['cid'=>$fid]);
        //模板里要用到的变量值
        $vars = [
            'listdb'=>getArray($data_list)['data'],
            'fid'=>$fid,
            'info'=>$this->model->getInfoById($fid),
            'pages'=>$data_list->render()
        ];
        
        $template = getTemplate('index');
        
        return $this->fetch($template,$vars);
    }
    
    /**
     * 辅栏目列表页取数据
     * @param array $map
     * @param number $rows
     * @return array
     */
    protected function getListData($map=[],$rows=20)
    {
        $cid_array = [];
        if(!empty($map['cid'])){    //把子栏目也取出来
            $cid_array = $this->model->getSonsId($map['cid']);
        }
        
        $map = array_merge($this->getMap(),$map);
        
        if(!empty($cid_array)){
            unset($map['cid']);
            $cid_array = array_merge($cid_array,[$map['cid']]);
            $data_list = $this->info_model->where($map)->where('cid','in',$cid_array)->group('aid')->order('list DESC,id DESC')->paginate($rows);
        }else{
            $data_list = $this->info_model->where($map)->order('list DESC,id DESC')->paginate($rows);
        }
        
        foreach ($data_list AS $key=>$rs){
            //获取内容的详细数据
            $info = $this->c_model->getInfoById($rs['aid']);            
            if ($info) {
                $data_list[$key] = array_merge($info,getArray($rs));
            }else{
                unset($data_list[$key]);
                $this->info_model->where('id',$rs['id'])->delete();
            }
        }
        return $data_list;
    }

}