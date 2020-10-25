<?php
namespace app\common\controller\admin;

//栏目自定义字段管理
class SortField extends F
{
    protected function _initialize()
    {
        parent::_initialize();        
        if ( $this->request->isPost() ) {
            preg_match_all('/([_a-z]+)/',get_called_class(),$array);
            $dirname = $array[0][1];
            query("ALTER TABLE  `qb_{$dirname}_field` CHANGE  `mid`  `mid` MEDIUMINT( 5 ) NOT NULL DEFAULT  '0' COMMENT  '所属模型id'");
        }
    }
    
    //列出模型下的所有字段
    public function index($mid=0)
    {
        if ($this->request->isPost()) {
            //修改字段排序
            $data = $this->request->Post();
            foreach($data['orderdb'] AS $id=>$list){
                $map = [
                        'id'=>$id,
                        'list'=>$list
                ];
                $this->model->update($map); 
            }
            $this->success('修改成功');
        }
        $this->tab_ext['nav'] = [
                [
                        '-2'=>[
                                'title'=>'栏目字段管理',
                                'url'=>auto_url('index','mid=-2'),
                        ],
                        '-3'=>[
                                'title'=>'辅栏目字段管理',
                                'url'=>auto_url('index','mid=-3'),
                        ],
                ],
                $mid,
        ];
        if (empty(config('use_category'))) {    //没启用辅栏目
            unset($this->tab_ext['nav'][0]['-3']);
        }
        $this->tab_ext['page_title'] = ($mid==-2?'栏目':'辅栏目') . '字段管理';
        
        $this->tab_ext['top_button']=[
                [
                        'type'=>'add',
                        'title'=>'添加字段',
                        'href' => auto_url('add', ['mid' => $mid])
                ],
        ];
       
        $data = self::getListData(['mid'=>$mid],['list'=>'desc']);
        return $this->getAdminTable($data);
    }
}