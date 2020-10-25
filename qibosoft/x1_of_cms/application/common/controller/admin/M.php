<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;

//模型管理
abstract class M extends AdminBase
{
    use AddEditList;
    
    protected $validate = 'Module';
    protected $model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'module');
        $this->set_config();
    }
    
    protected function set_config(){
        
        $this->form_items = [
            ['text', 'title', '模型名称'],
            ['text', 'keyword', '关键字(只能字母或数字)','可为空,但确定后,不能随意修改,模板会跟此挂钩'],
            //['text', 'layout', '模板路径','一般请留空,否则必须放在/template/index_style/目录下,然后补全路径:比如:“qiboxxx/cms/content/list2.htm”'],     
            ['text', 'haibao', '海报模板路径',fun('haibao@get_haibao_list').'可留空,多个用逗号隔开,需要补全路径(其中haibao_style不用填):比如:“xxx/show.htm”'],
        ];
        
        $this->list_items = [
                ['title', '模型名称', 'text'],
                //['keyword', '关键字', 'text'],
                ['create_time', '创建时间', 'text'],
        ];
        
        $this->tab_ext = [
                'page_title'=>'模型管理',
                'top_button'=>[
                        [
                                'title' => '创建模型',
                                'icon'  => 'fa fa-fw fa-cubes',
                                'class' => '',
                                'href'  => auto_url('add')
                        ],
                ],
                'right_button'=>[
                        [
                                'title' => '管理内容',
                                'icon'  => 'fa fa-fw fa-file-text-o',
                                'href'  => auto_url('content/index', ['mid' => '__id__'])
                        ],
                        [
                                'title' => '发布内容',
                                'icon'  => 'glyphicon glyphicon-plus',
                                'href'  => auto_url(config('post_need_sort')?'content/postnew':'content/add', ['mid' => '__id__'])
                        ],
                        [
                                'title' => '字段管理',
                                'icon'  => 'fa fa-fw fa-table',
                                'href'  => auto_url('field/index', ['mid' => '__id__'])
                        ],
                        [
                                'title' => '复制模型',
                                'icon'  => 'fa fa-copy',
                                'href'  => auto_url('add', ['type' => 'copy','mid' => '__id__'])
                        ],
                        ['type'=>'delete',],
                        ['type'=>'edit',],
                ],
        ];
    }
    
    /**
     * 模型列表
     * @return unknown|mixed|string
     */
    public function index() {
        if ($this->request->isPost()) {
            //修改排序
            return $this->edit_order();
        }
        $listdb = $this->getListData($map = [], $order = [], 50);
        return $this -> getAdminTable($listdb);
    }
    
    /**
     * 创建模型
     * @param string $type 等于copy的时候就是复制模型
     * @param number $mid 原型ID
     * @return mixed|string
     */
    public function add($type='',$mid=0){
        
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();            
            if (empty($data['title'])) {
                $this->error('模型名称不能为空');
            }
            if ($data['type']=='copy') {
                return $this->copy($data['title'],$data['mid']);
            }
            if ($result = $this->saveAddContent()) {
                if ($this->model->createTable($result->id,$data['title'])) {
                    \think\Cache::clear();  //插件要用到清除缓存
                    $this->success('模型创建成功', auto_url('index'));
                }else{
                    $this->model->where('id','=',$result->id)->delete();
                    $this->error('模型数据表创建失败');
                }
            }
            $this->error('模型创建失败');
        }        
        if ($type=='copy') {
            $this->tab_ext['page_title'] = '复制模型';
            $this->form_items = [
                    ['text', 'title', '复制后新的模型名称'],
                    ['hidden','type',$type],
                    ['hidden','mid',$mid]
            ];
        }        
        return $this->addContent();        
    }
    
    /**
     * 复制模型
     * @param string $name 新模型名称
     * @param number $mid 原模型ID
     */
    public function copy($name='新模型',$mid=0){
        if (empty($mid)) {
            $this->error('原模型ID不存在!');;
        }
        $newid = $this->model->copyTable($name,$mid);
        if ($newid) {
            \think\Cache::clear();  //插件要用到清除缓存
            $this->success('模型复制成功', auto_url('index'));
        }else{
            $this->error('模型复制失败');
        }
    }
    
    /**
     * 修改模型基本信息,不包含字段
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id = null) {
        if (empty($id)) $this -> error('缺少参数');
        
        if ($this->request->isPost()) {
            $data = $this->request->post();
            preg_match_all('/([_a-z]+)/',get_called_class(),$array);
            $dirname = $array[0][1];
            if ( !table_field($dirname.'_module','haibao') ) {
                query("ALTER TABLE  `qb_{$dirname}_module` ADD  `haibao` VARCHAR( 255 ) NOT NULL COMMENT  '海报模板';");
            }
            if ($data['haibao']) {
                $detail = explode(',',$data['haibao']);
                foreach($detail AS $value){
                    if($value!='' && !is_file(TEMPLATE_PATH.'haibao_style/'.$value)){
                        $this->error('当前文件不存在:'.TEMPLATE_PATH.'haibao_style/'.$value);
                    }
                }
            }
        }
        
        $info = $this -> getInfoData($id);
        return $this -> editContent($info);
    }
    
    /**
     * 删除模型
     * @param unknown $ids
     */
    public function delete($ids = null)
    {
        //删除对应的模型分表
        $this->model->deleteModule($ids);
        
        //模块表删除记录
        if( $this->deleteContent($ids) ){
            $this->success('删除成功', auto_url('index') );
        }else{
            
            $this->error('删除失败');
        }
    }
    
}