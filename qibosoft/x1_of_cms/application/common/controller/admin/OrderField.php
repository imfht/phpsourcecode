<?php
namespace app\common\controller\admin;

//商城订单自定义段
class OrderField extends F
{
    protected function _initialize()
    {
        parent::_initialize();        
        if ( $this->request->isPost() ) {
            preg_match_all('/([_a-z]+)/',get_called_class(),$array);
            $dirname = $array[0][1];
            if (!table_field("{$dirname}_order",'mid')) {
                query("ALTER TABLE  `qb_{$dirname}_order` ADD  `mid` MEDIUMINT( 5 ) NOT NULL DEFAULT  '-1' COMMENT  '模型ID,只能是负数,避免跟主题相冲突' AFTER  `id`");
                query("ALTER TABLE  `qb_{$dirname}_field` CHANGE  `mid`  `mid` MEDIUMINT( 5 ) NOT NULL DEFAULT  '0' COMMENT  '所属模型id'");
            }            
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
        
        $this->tab_ext['page_title'] = '用户订单字段管理';
        
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