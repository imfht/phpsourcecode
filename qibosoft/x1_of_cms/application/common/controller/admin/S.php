<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AdminSort;


//主栏目管理
abstract class S extends AdminBase
{
    use AdminSort;
    
    protected $validate = 'Sort';
    protected $model;
    protected $m_model;
    protected $c_model;
    protected $form_items;
    
    protected $list_items;
    protected $tab_ext;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'sort');
        $this->m_model = get_model_class($dirname,'module');
        $this->c_model = get_model_class($dirname,'content');
        $this->set_config();
    }
    
    protected function set_config(){
        $this->list_items = [
                ['name', '栏目名称', 'link',iurl('content/index',['fid'=>'__id__']),'_blank'],
                ['mid', '所属模型', 'select2',$this->m_model->getTitleList()],
                ['id','统计','callback',function($value,$rs){
                    return $this->c_model->getNumByMid($rs['mid'],['fid'=>$value]);
                }],
                ['list', '排序值', 'text.edit'],
        ];
        
        $this->form_items = [                
                ['textarea', 'name', '栏目名称','若要同时创建多个分类的话,每个分类名称换一行即可'],
                ['select', 'pid', '归属上级分类','不选择，则为顶级分类',$this->model->getTreeTitle()],
                ['select', 'mid', '所属模型','创建后不能随意修改',$this->m_model->getTitleList(),1],
        ];
        
        $this->tab_ext = [
                'page_title'=>'栏目管理',
                'top_button'=>[
                        [
                                'title' => '创建栏目',
                                'icon'  => 'fa fa-fw fa-th-list',
                                'class' => '',
                                'href'  => auto_url('add')
                        ],
                ],
                'right_button'=>[
                        [
                                'title' => '管理内容',
                                'icon'  => 'fa fa-fw fa-file-text-o',
                                'href'  => auto_url('content/index', ['fid' => '__id__'])
                        ],
                        [
                                'title' => '发布内容',
                                'icon'  => 'glyphicon glyphicon-plus',
                                'href'  => auto_url('content/add', ['fid' => '__id__'])
                        ],                        
                        ['type'=>'delete'],
                        ['type'=>'edit'],
                ],
        ];
    }
    
    protected function get_tpl($data){
        foreach($data['templates'] AS $key=>$value){
            if(empty($value)){
                unset($data['templates'][$key]);
            }elseif(!is_file(TEMPLATE_PATH.'index_style/'.$value)){
                $this->error('模板不存在，或者路径有误！请确认把模板放在以下目录'.TEMPLATE_PATH.'index_style/'.$value);
            }
        }
        if(!empty($data['templates'])){
            return serialize($data['templates']);
        }
    }
    
    /**
     * 分组显示设置项
     * @param number $id 当前栏目ID
     * @return string[][][]|unknown[][][]|string[][][][]|NULL[][][]
     */
    protected function set_field_form($id=0){
        $msg = '请把模板放在此目录下: '.TEMPLATE_PATH.'index_style/ 然后输入相对路径,比如 default/abc.htm';
        
        return [
                '基础设置'=>[
                        ['text', 'name', '栏目名称'],
                        ['select', 'pid', '归属上级分类','不选择，则为顶级分类',$this->model->getTreeTitle($id)],
                        ($this->c_model->getNumByMid($this->c_model->getMidByFid($id),['fid'=>$id])>0?['hidden','mid']:['select', 'mid', '所属模型','有内容后就不能再修改',$this->m_model->getTitleList()]),
                        ['icon', 'logo', '图标',],
                        ['checkbox', 'allowpost', '允许发布内容的用户组','全留空,则不作限制',getGroupByid()],
                        ['checkbox', 'allowview', '允许查看内容的用户组','全留空,则不作限制。注意标题不能限制。',getGroupByid()],
                        ['checkbox', 'allow_viewtitle', '允许查看标题的用户组','全留空,则不作限制。注意标签调用可能无效。',getGroupByid()],
                ],
                '模板设置'=>[
                        ['text', 'haibao', '海报模板路径',fun('haibao@get_haibao_list').'可留空,多个用逗号隔开,需要补全路径(其中haibao_style不用填):比如:“xxx/show.htm”'],
                        ['text', 'templates[waplist]', 'wap列表页模板[:可留空，将用默认的。点击右边图片选择模板]',$msg,'',['','<i class="fa fa-plus-square" onclick="alert(9)"></i>']],
                        ['text', 'templates[wapshow]', 'wap内容页模板[:可留空，将用默认的。点击右边图片选择模板]',$msg,'',['','<i class="fa fa-plus-square" onclick="alert(9)"></i>']],
                        ['text', 'templates[pclist]', 'PC列表页模板[:可留空，将用默认的。点击右边图片选择模板]',$msg,'',['','<i class="fa fa-plus-square" onclick="alert(9)"></i>']],
                        ['text', 'templates[pcshow]', 'PC内容页模板[:可留空，将用默认的。点击右边图片选择模板]',$msg,'',['','<a class="fa fa-plus-square pop" href="/"></a>']],
                        
                ],
                'SEO优化设置'=>[
                        ['text', 'seo_title', 'SEO标题'],
                        ['text', 'seo_keywords', 'SEO关键字'],
                        ['text', 'seo_description', 'SEO描述'],
                ],
        ];
    }
    
    
    /**
     * 默认列表页
     * @return mixed|string
     */
    public function index() {
        if ($this->request->isPost()) {
            //修改排序
            return $this->edit_order();
        }
        $listdb = $this->getListData($map = [], $order = 'list desc,id asc');
        return $this -> getAdminTable($listdb);
    }
    
    /**
     * 修改栏目信息
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id = null)
    {
        if($this->request->isPost()){
            $data = $this -> request -> post();
            $data = \app\common\field\Post::format_all_field($data,-2); //对一些特殊的字段进行处理,比如多选项,以数组的形式提交的
            if (!empty($this -> validate)) {    // 验证
                //$result = $this -> validate($data, $this -> validate);
                //if (true !== $result) $this -> error($result);
            }
            
            if ($data['haibao']) {
                preg_match_all('/([_a-z]+)/',get_called_class(),$array);
                $dirname = $array[0][1];
                if ( !table_field($dirname.'_sort','haibao') ) {
                    query("ALTER TABLE  `qb_{$dirname}_sort` ADD  `haibao` VARCHAR( 255 ) NOT NULL COMMENT  '海报模板';");
                }
                $detail = explode(',',$data['haibao']);
                foreach($detail AS $value){
                    if($value!='' && !is_file(TEMPLATE_PATH.'haibao_style/'.$value)){
                        $this->error('当前文件不存在:'.TEMPLATE_PATH.'haibao_style/'.$value);
                    }
                }
            }
            
            $data['allowpost'] = implode(',', $data['allowpost']);  //允许发布内容的用户组
            $data['allowview'] = implode(',', $data['allowview']);  //允许查看内容的用户组
            $data['allow_viewtitle'] = implode(',', $data['allow_viewtitle']);  //允许查看标题的用户组
            $data['allowreply'] = implode(',', $data['allowreply']);  //允许评的用户组
            $data['template'] = $this->get_tpl($data);                  //栏目自定义模板
            
            
            if ($this -> model -> update($data)) {
                $this -> success('修改成功', 'index');
            } else {
                $this -> error('修改失败');
            }
        }
        
        if(empty($id)) $this->error('栏目ID不存在');
        
        $this->form_items = []; //销毁掉,使用下面分组的形式                
        $this -> tab_ext['group'] = $this->set_field_form($id);
        
        $form_field =  \app\common\field\Form::get_all_field(-2);
        if ($form_field) {  //把用户自定义字段,追加到基础设置那里,不过也可以另起一个分组的
            $this -> tab_ext['group']['基础设置'] = array_merge($this -> tab_ext['group']['基础设置'],$form_field);
        }
        
        //联动字段,比如点击哪项就隐藏或者显示哪一项
        $this->tab_ext['trigger'] = \app\common\field\Form::getTrigger($this->mid);
        
        $info = $this->getInfoData($id);
        
        if($info['template']){
            $array = unserialize($info['template']);
            
            if (is_array($array)){
                $info = array_merge($info,['templates'=>$array]);
            }
        }
        
        if ( !isset($info['allow_viewtitle']) ) {
            preg_match_all('/([_a-z]+)/',get_called_class(),$array);
            $dirname = $array[0][1];
            into_sql("ALTER TABLE  `qb_".$dirname."_sort` ADD  `allow_viewtitle` VARCHAR( 255 ) NOT NULL COMMENT  '允许查看标题的用户组'");
        }
        
        return $this->editContent($info);
    }
    
    public function delete($ids = null)
    {
        if( $this->deleteContent($ids) ){
            $this->success('删除成功', 'index');
        }else{
            
            $this->error('删除失败');
        }
    }
    
}