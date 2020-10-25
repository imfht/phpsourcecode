<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;
use app\common\traits\AddEditList;

/**
 * 消息提醒设置开关
 *
 */
class Remind extends MemberBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $tab_ext;
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new UserModel();
        if ($this->user['sendmsg'] && !is_array($this->user['sendmsg'])) {
            $this->user['sendmsg'] = json_decode($this->user['sendmsg'],true);
        }
        if(!is_array($this->user['sendmsg'])){
            $this->user['sendmsg'] = [];
        }
    }
    
    /**
     * 接口,快速设置
     * @param string $name
     * @param string $value
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function api($name='',$value=''){
        $array = array_merge($this->user['sendmsg'],[$name=>$value]);
        $array = [
                'uid'=>$this->user['uid'],
                'sendmsg'=>json_encode($array),
        ];
        if ( $this->model->edit_user($array) ) {
            return $this->ok_js();
        } else {
            return $this->err_js('数据更新失败');
        }
    }
    
    /**
     * 批量设置
     * @return mixed|string
     */
    public function set()
    {
        if (IS_POST) {            
            $data = get_post('post');
            $array = array_merge($this->user['sendmsg'],$data);
            $array = [
                    'uid'=>$this->user['uid'],
                    'sendmsg'=>json_encode($array),
            ];
            if ( $this->model->edit_user($array) ) {
                $this->success('修改成功');
            } else {
                $this->error('数据更新失败');
            }
        }
        $this->tab_ext['page_title'] = '消息提醒设置';
        $this->form_items = config('remind');
        foreach ($this->form_items AS $key=>$v){
            if($v[1]=='weibo_pop' && !modules_config('weibo')){
                unset($this->form_items[$key]);
            }
            if($v[1]=='bbs_reply_wxmsg' && !modules_config('bbs')){
                unset($this->form_items[$key]);
            }
        }
        $this->form_items = array_values($this->form_items);
        
        return $this->editContent($this->user['sendmsg']);
    }
    
    public function index(){
    }
    public function delete(){
    }
    public function add(){
    }
    
    
}
