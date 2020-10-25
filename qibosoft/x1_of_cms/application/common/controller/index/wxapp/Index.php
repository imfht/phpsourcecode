<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\common\traits\ModuleContent;


//小程序或APP调用的列表数据
abstract class Index extends IndexBase
{
    //use ModuleContent;
    protected $model;                  //内容
    protected $mid;                      //模型ID
    
    
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
        $this->model = get_model_class($dirname,'content');
        $this->mid = 1;
    }
    
    /**
     * 列表数据
     * @param number $fid 栏目ID
     * @param string $type 类型筛选
     * @return \think\response\Json
     */
    public function index($fid=0,$type='',$rows=10){
        $map = [];
        $fid && $map['fid'] = $fid;
        //$map['ispic'] = 1;
        $order = 'id desc';
        if($type=='star'){
            $map['status'] = 2;
        }elseif($type=='hot'){
            $order = 'view desc';
        }elseif($type=='new'){
            $order = 'id desc';
        }elseif($type=='reply'){
            $order = 'list desc';
        }
        $mid = $this->model->getMidByFid($fid) ?: $this->mid ;
        $array = getArray( $this->model->getListByMid($mid,$map,$order,$rows) );
        foreach($array['data'] AS $key => $rs){
            $rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
            $rs['picurl'] = tempdir($rs['picurl']);
            $rs['content'] = get_word(del_html($rs['content']), 100);
            unset($rs['_content'],$rs['sncode']);
            $array['data'][$key] = $rs;
        }
        
        return $this->ok_js($array);        
    }
    
    /**
     * 根据用户UID获取其相应的数据
     * @param number $uid
     * @param number $mid
     * @param number $rows
     * @return void|unknown|\think\response\Json
     */
    public function listbyuid($uid=0,$mid=0,$rows=20,$keyword=''){
        if (empty($uid)) {
            $uid = $this->user['uid'];
        }
        if (empty($uid)) {
            return $this->err_js('UID不存在');
        }
        $map=[
            'uid'=>$uid,
        ];
        if ($mid){
            $map['mid'] = $mid;
        }
        if ($keyword!='') {
            $mid || $mid=1;
            if (empty(model_config($mid))) {
                return $this->err_js('模型不存在');
            }
            $map['title'] = ['like','%'.$keyword.'%'];
            $data = $this->model->getListByMid($mid,$map,"id desc",$rows,$pages=[],$format=FALSE);
        }else{
            $data = $this->model->getAll($map,"id desc",$rows,$pages=[],$format=FALSE);
        }
        
        $array = getArray($data);
        foreach ($array['data'] AS $key=>$rs){
            $rs['picurl'] = tempdir($rs['picurl']);
            if(config('system_dirname')=='bbs'){
                $rs['content'] = fun("bbs@getContents",$rs['id'],100);
            }else{
                $rs['content'] = get_word(del_html($rs['content']), 100);
            }
            $rs['time'] = date('Y-m-d H:i',$rs['create_time']);
            $rs['url'] = iurl(config('system_dirname').'/content/show',['id'=>$rs['id']]);
            unset($rs['_content'],$rs['full_content'],$rs['sncode']);
            $array['data'][$key] = $rs;
        }
        return $this->ok_js($array);
    }
    
    /**
     * 首页幻灯片
     * @return \think\response\Json
     */
    public function banner(){
        $map = ['status'=>2];
        $map['ispic'] = 1;
        $rows = 4;
        $array = getArray( $this->model->getListByMid(1,$map,'id desc',$rows) );
        foreach($array['data'] AS $key=>$rs){
            unset($rs['content'],$rs['full_content'],$rs['sncode']);
            $array['data'][$key] = $rs;
        }
        return $this->ok_js($array['data']);
    }
}













