<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase; 
use app\common\traits\AddEditList;

//论坛回复管理
class Reply extends AdminBase
{
    use AddEditList;
    protected $model;
    protected $c_model;
    protected $list_items;
    protected $form_items;
    protected $tab_ext = [
            'page_title'=>'评论回复管理',
            'top_button'=>[ ['type'=>'delete']],
    ];
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'reply');
        $this->c_model        = get_model_class($dirname,'content');
    }
    
    /**
     * 列表
     * @return unknown|mixed|string
     */
    public function index() {
        
        $this->list_items = [
                ['content', '评论内容', 'callback',function($value,$rs){
                    $info = $this->c_model->getInfoByid($rs['aid']);
                    return '主题:'.$info['title'].'<br>回复:'.get_word(del_html($value), 50);
                }],
                ['status', '状态','select', ['-1'=>'回收站','0'=>'未审核','1'=>'已审']],
                ['uid', '发布者', 'username'],
                ['create_time', '发布日期', 'text'],
        ];
        
        $this -> tab_ext['search'] = ['uid'=>'用户uid','aid'=>'主题id'];
        
        //筛选字段
        $this->tab_ext['filter_search'] = [
                'status'=>['未审核','已审核'],
        ];
        
        
        //右边菜单
        $this -> tab_ext['right_button'] = [
                ['type'=>'delete'],
               // ['type'=>'edit'],
                [
                        'icon'=>'fa fa-file-o',
                        'title'=>'详情',
                        'url'=>iurl('content/show','id=__aid__'),
                        'target'=>'_blank',
                ],
        ];
        
        $listdb = self::getListData($map = [], $order = []);
        return $this -> getAdminTable($listdb);
    }
    
    /**
     * 查看详情
     * @param number $id
     * @return \app\common\traits\unknown
     */
    public function show($id=0){
        
        $this->form_items = [
                ['text','content', '内容'],
                ['date','create_time', '发布日期'],
                ['radio','status', '审核与否','',['未审核','已审核']],
        ];
        
        $info = getArray( $this->getInfoData($id) );
        
        return $this->getAdminShow($info) ;
    }
    
    
    /**
     * 删除评论
     * @param unknown $ids
     * @return boolean
     */
    protected function deleteContent($ids) {
        if (empty($ids)) {
            $this -> error('ID有误');
        }
        
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)) {
            return false;
        }
        $ck = 0;
        foreach ($ids AS $id){
            $info = getArray($this -> model -> get($id));
            if ($this -> model -> destroy($id)) {
                $this->c_model->addField($info['aid'],'replynum',false);    //评论数减一
                $ck++;
            }
        }
        if ($ck) {
            return true;
        } else {
            return false;
        }
    }

    
    
}
