<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;

//模型自定义字段管理
abstract class F extends AdminBase
{
    use AddEditList;
    
    protected $validate = 'Field';
    protected $model;
    protected $form_items = [];
    protected $list_items = [];
    protected $tab_ext = [];
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'field');
        $this->set_config();
        
        if ($this->request->action()=='index'||$this->request->param()['plugin_action'] =='index'){
            if(!table_field($dirname.'_field','input_width')) {
                query("ALTER TABLE  `qb_{$dirname}_field` ADD  `input_width` VARCHAR( 7 ) NOT NULL COMMENT  '输入表单宽度',ADD  `input_height` VARCHAR( 7 ) NOT NULL COMMENT  '输入表单高度',ADD  `unit` VARCHAR( 20 ) NOT NULL COMMENT  '单位名称',ADD  `match` VARCHAR( 150 ) NOT NULL COMMENT  '表单正则匹配',ADD  `css` VARCHAR( 20 ) NOT NULL COMMENT  '表单CSS类名';");
            }            
            if(!table_field($dirname.'_field','script')) {
                query("ALTER TABLE  `qb_{$dirname}_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';");
            }            
            if(!table_field($dirname.'_field','range_opt')) {
                query("ALTER TABLE  `qb_{$dirname}_field` ADD  `range_opt` TEXT NOT NULL COMMENT  '范围选择,比如价格范围',ADD  `group_view` VARCHAR( 255 ) NOT NULL COMMENT  '允许哪些用户组查看',ADD  `index_hide` TINYINT( 1 ) NOT NULL COMMENT  '是否前台不显示并不做转义处理';");
            }
            if(!table_field($dirname.'_field','group_post') ){
                query("ALTER TABLE  `qb_{$dirname}_field` ADD  `group_post` VARCHAR( 255 ) NOT NULL COMMENT  '允许使用此字段的用户组'");
                query("ALTER TABLE  `qb_{$dirname}_field` CHANGE  `title`  `title` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '字段标题'");
            }
        }
        
    }
    
    protected function set_config(){
        
        $this->list_items = [
                ['title', '字段名称', 'text'],
                ['name', '字段变量名', 'text'],
                ['type', '表单类型', 'select',config('form')],
                ['name', '字段变量名', 'text'],
                ['list', '排序值', 'text.edit'],
        ];
        
        $this->form_items = [
                ['text', 'name', '字段变量名','创建后不能随意修改,否则会影响其它地方的数据调用,只能数字或字母及下画线，但必须要字母开头',"title_".rand(0,100)],
                ['text', 'title', '字段名称'],
                ['select', 'type', '表单字段类型','',config('form'),'text'],
                ['textarea', 'options', '参数选项', '用于单选、多选、下拉等类型,如果某项值要关联某个字段,格式如下:值|描述|字段名A,字段名B'],
                ['text', 'value', '字段默认值'],
                ['text', 'field_type', '数据库字段类型','','varchar(128) NOT NULL'],
                ['radio', 'listshow', '是否在列表显示', '', ['不在列表显示', '显示'], 0],
                ['radio', 'ifsearch', '是否作为内容搜索选项', '', ['否', '是'], 0],
                ['textarea', 'range_opt', '列表页范围筛选参数', '(每个值换一行)<br>如下示例:<br>-1,0|免费<br>0,100|100元以下<br>100,500|100元-500元 <br>默认搜索的条件“&gt;=,&lt;=”是大于或等于第一项并且小于或等于第二项,如果要调整的话,就需要加多一项参数,比如“0,100|一百元以下|&gt;,&lt;=”代表大于0而不包含0,小于100包含100'],
                ['radio', 'ifmust', '是否属于必填项', '', ['可不填', '必填'], 0],
                ['checkbox', 'group_view', '仅限哪些用户组查看', '', getGroupByid()],
                ['radio', 'index_hide', '详情页是否隐藏', '二次开发才会用到隐藏', ['显示', '隐藏'], 0],
                ['text', 'about', '描述说明'],
                ['text', 'list', '排序值'],
                ['text', 'nav', '分组名[:对于不重要的字段,你可以添加组名,让他在更多那里显示]'],
                
        ];
        
        $this->tab_ext = [
                'js_file'=>'field',
                'warn_msg'=>'字段名称可随意修改，但字段变量名创建好后，就不要修改，不然其它地方的调用会受影响，只能字母或数字或下画线，并且只能字母开头',
                'page_title'=>'表单字段管理',
                'trigger'=>[
                        ['type', 'checkbox,radio,select', 'options'],
                        ['type', 'text,money,number', 'range_opt'],
                        ['ifsearch', '1', 'range_opt'],
                ],
        ];
        
    }
    
    //添加字段
    public function add($mid=0)
    {
        
        $this->form_items[] = ['hidden','mid',$mid]; //记录一下往哪个模型加字段
        
        // 保存数据
        if ($this->request->isPost()) {
        
            // 表单数据
            $data = $this->request->post();
            
            if(!empty($this->validate)){
                // 验证
                $result = $this->validate($data, $this->validate);
                if(true !== $result) $this->error($result);
            }
            
            $result = $this->model->newField($data['mid'],$data);  //新增字段信息
            if ($result===true) {
                if ( $this->saveAddContent() ) {    //字段表进一步保存字段信息
                    \think\Cache::clear();  //插件要用到清除缓存
                    $this->success('字段添加成功', auto_url('index',['mid'=>$data['mid']]));
                }
            }else{
                $this->error('操作失败:'.$result);
            }            
        }
        return $this->addContent();
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
        if(empty($mid)){
            $this->error('ID不存在');
        }
        $this->tab_ext['top_button']=[
                [
                        'type'=>'add',
                        'title'=>'添加字段',
                        'href' => auto_url('add', ['mid' => $mid])
                ],
                [
                        'type'=>'back',
                        'title'=>'返回模型管理',
                        'href' => auto_url('module/index')
                ],
        ];
       
        $data = self::getListData(['mid'=>$mid],'list desc',50);
        return $this->getAdminTable($data);
    }
    
    //修改字段
    public function edit($id = null)
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            
            if(!empty($this->validate)){
                // 验证
                $result = $this->validate($data, $this->validate);
                if(true !== $result) $this->error($result);
            }
            $data['group_view'] = is_array($data['group_view']) ? implode(',', $data['group_view']) : $data['group_view'].'';   //强制变成字符串,避免数组的时候没东西提交导致修改不成功
            
            $this -> request -> post(['group_view'=>$data['group_view']]);
            
            $result = $this->model->updateField($id,$data); // 更新字段信息
            if ($result===true) {
                if ( $this->saveEditContent() ) {
                    $mid = $this->model->where('id',$id)->value('mid');
                    \think\Cache::clear();  //插件要用到清除缓存
                    $this->success('字段修改成功', auto_url('index',['mid'=>$mid]) );
                }
            }
            $this->error('字段更新失败:'.$result);
        }
        
        if(empty($id)) $this->error('缺少参数');
        
        $info = $this->getInfoData($id);
        
        return $this->editContent($info);
    }
    
    public function delete($ids = null)
    {        
        $field_array = $this->getInfoData( $ids );
        $this->model->deleteField($field_array);
        if( $this->deleteContent($ids) ){
            \think\Cache::clear();  //插件要用到清除缓存
            $this->success('删除成功', auto_url('index',['mid'=>$field_array['mid']]) );
        }else{
            
            $this->error('删除失败');
        }
    }
    
}