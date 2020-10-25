<?php
namespace app\common\controller\member;

use app\common\controller\MemberBase;
use app\common\traits\AdminSort;

/**
 * 
 * 会员中心用到的我的分类功能
 *
 */
abstract class Mysort extends MemberBase
{
    use AdminSort;
    
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext;
    protected $mid;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'mysort');
        $this->list_items = [                
                ['logo','图标','icon'],
                ['name','分类名称','text'],
        ];
        $this->tab_ext['page_title'] = '我的商品分类管理';
        $this->tab_ext['right_button'] = [
                [
                        'type'=>'delete',
                        'title'=>'删除',
                ],
                [
                        'type'=>'edit',
                        'title'=>'修改',
                ],
        ];
    }
    
    
    public function index($ext_id=0) {
        if ($this->request->isPost()) {
            //修改排序
            return $this->edit_order();
        }
        
        $map = [
                'uid'=>$this->user['uid'],                
        ];
        if ($ext_id) {
            if (empty($ext_sys)) {
                $ext_sys = modules_config('qun')['id'];
            }
            $map = [
                    'ext_id'=>$ext_id,
                    'ext_sys'=>$ext_sys,
            ];
        }
        
        $this->tab_ext['top_button'] = [
                [
                        'type'=>'add',
                        'title'=>'新增分类',
                        'href'=>url('add',[
                                'ext_id'=>$ext_id,
                                'ext_sys'=>$ext_sys,
                        ]),
                ],
        ];
        
        $listdb = $this->getListData($map);
        return $this -> getAdminTable($listdb);
    }
    
    public function add($ext_id=0,$ext_sys=0) {
        $this->form_items = [
                ['hidden','ext_id',$ext_id],
                ['hidden','ext_sys',$ext_sys],
                ['textarea','name','分类名称','同时添加多个,则每个名称换一行'],
        ];
        $url = url('index',[
                'ext_id'=>$ext_id,
                'ext_sys'=>$ext_sys,
        ]);
        return $this -> addContent($url);
    }
    
    public function edit($id = null) {
        $this->form_items = [
                ['text','name','分类名称'],
                ['icon','logo','图标'],
                ['text','list','排序值'],
        ];
        if (empty($id)) $this -> error('缺少参数');
        $info = $this -> getInfoData($id);
        if ($info['uid']!=$this->user['uid']) {
            $this -> error('你没权限');
        }
        $url = url('index',[
                'ext_id'=>$info['ext_id'],
                'ext_sys'=>$info['ext_sys'],
        ]);
        return $this -> editContent($info,$url);
    }
    
    
    public function delete($ids = null) {
        $ids = is_array($ids)?$ids:[$ids];
        foreach($ids AS $key){
            $info = $this -> getInfoData($key);
            if ($info['uid']!=$this->user['uid']) {
                $this -> error('你没权限');
            }
        }        
        if ($this -> deleteContent($ids)) {
            $this -> success('删除成功');
        } else {
            $this -> error('删除失败');
        }
    }
    
}