<?php
namespace app\common\model;

use app\common\utility\Hash;

class ManageMenu extends App
{
    //关联模型
    public $assoc = [];
    public $display = 'title';
    public $cache = [];

    ##父模型名
    public $parentModel = 'parent';

    public function initialize()
    {
        $this->form = [
            'id' => [
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden'
            ],
            'parent_id' => array(
                'type' => 'integer',
                'name' => '父级栏目',
                'elem' => 'nest_select.ManageMenu'
            ),            
            'title' => array(
                'type' => 'string',
                'name' => '栏目标题',
                'elem' => 'text'
            ),
            'ex_title' => array(
                'type' => 'string',
                'name' => '副标题',
                'elem' => 'text'
            ),
            'icon' => array(
                'type' => 'string',
                'name' => '栏目图标',
                'elem' => 'icon'
            ),
            'module' => array(
                'type' => 'string',
                'name' => '模块',
                'elem' => 'select',
                'options' =>['manage' => 'manage'], //Hash::combine(config('allow_module_list'), '{n}', '{n}'),
                'info' => '没有链接，就无需处理'
            ),
            'controller' => array(
                'type' => 'string',
                'name' => '控制器',
                'elem' => 'select',
                'info' => '没有链接，就无需处理'
            ),
            'action' => array(
                'type' => 'string',
                'name' => '方法名',
                'elem' => 'text',
                'quick' => true,
                'info' => '没有链接，就无需处理'
            ),
            'args' => array(
                'type' => 'string',
                'name' => '参数',
                'elem' => 'keyvalue',
                'list' => 'keyvalue',
            ),  
            'target' => array(
                'type' => 'string',
                'name' => '开发方式',
                'elem' => 'select',
                'options' => [
                    '_self' => '当前窗口',
                    '_blank' => '新窗口'
                ]
            ),
                      
            'image' => array(
                'type' => 'string',
                'name' => '封面图片',
                'elem' => 'image.upload',
                'list' => 'image',
                'upload' => array(
                    'maxSize' => 2048,
                    'validExt' => array('jpg', 'png', 'gif')
                )
            ),
            'is_nav' => array(
                'type' => 'integer',
                'name' => '导航显示',
                'elem' => 'checker'
            ),
            'tips' => array(
                'type' => 'string',
                'name' => '提示信息',
                'elem' => 'text'
            ),
            'summary' => array(
                'type' => 'text',
                'name' => '文字简介',
                'elem' => 'textarea',
                'list' => 'show'
            ),
            'list_order' => array(
                'type' => 'integer',
                'name' => '排序权重',
                'elem' => 'number',
                'list' => 'edit'
            ),
            'created' => array(
                'type' => 'datetime',
                'name' => '添加时间',
                'elem' => 0,
                'list' => 'datetime',
                'elem_group' => 'advanced',
            ),
            'modified' => array(
                'type' => 'datetime',
                'name' => '修改时间',
                'elem' => 0,
                'list' => 'datetime',
                'elem_group' => 'advanced',
            )
        ]; 
        $options = array_keys($GLOBALS['Model_title']);
        sort($options);
        $options = Hash::combine($options, '{n}', '{n}');
        $this->form['controller']['options'] = $options;
        call_user_func_array(['parent', __FUNCTION__], func_get_args());
    }

    //数据验证    
    protected $validate = [
        'title' => array(
            'rule' => 'require',
            'message' => '请填写栏目标题'
        ),
        'parent_id' => array(
            array(
                'rule' => array('egt', 1),
                'message' => '请选择父级导航'
            ),
            array(
                'rule' => array('call', 'checkParent'),
                'on' => 'edit'
            )
        )
    ];
    
    public function checkParent($value, $rule, $data)
    {
        if ($value == $data['id'] || in_array($value, (array)managemenu('children', $data['id']))) {
            return '不能选择本栏目以及其子栏目做为父级';
        }
        return true;
    }    
    
    public function after_write()
    {
        $parent_rslt = call_user_func(array('parent', __FUNCTION__));
        $this->writeToFile();
        return $parent_rslt;
    }
    
    public function after_delete()
    {                  
        $parent_rslt = call_user_func(array('parent', __FUNCTION__));
        if ($this['id']) {                   
            $this->where(array('id' => array('id', 'IN', managemenu('children', $this['id']))))->delete();##删除掉所有子级
        }        
        $this->writeToFile();
        return $parent_rslt;
    }
    
    public function writeToFile()
    {
        $listStore = array();
        $list = $this->order(['list_order' => 'ASC', 'id' => 'ASC'])->select();
        if ($list) {
            foreach ($list as $item) {
                $listStore[$item['id']] = $item->toArray();
            }
        }
        $first = reset($listStore);
        
        $this->cache['threaded'][$first['id']] = $this->threaded($first['id'], $listStore);
        $this->getChildren($this->cache['threaded'], 'children');

        $navStore = array();
        $list = $this->order(['list_order' => 'ASC', 'id' => 'ASC'])->where(['is_nav' => 1])->select();
        if ($list) {
            foreach ($list as $item) {
                $navStore[$item['id']] = $item->toArray();
            }
        }
        $this->cache['nav'] = $this->threaded($first['id'], $navStore);
        $this->getChildren($this->cache['nav'], 'nav_children');
        
        $this->cache['list'] = $listStore;
        $this->cache['query'] = [];
        if (!empty($listStore)) {
            foreach($listStore as $nav) {
                if (trim($nav['controller']) && trim($nav['action'])) {
                    $module = trim($nav['module']) ? parse_name(trim($nav['module'])) : 'manage';
                    $together = $module . '/' . parse_name(trim($nav['controller'])). '/' . parse_name(trim($nav['action']));
                    $this->cache['query'][$together][$nav['id']] = [];
                    if ($nav['args']) {
                        $this->cache['query'][$together][$nav['id']] = json_decode($nav['args'], true);
                    }
                }
            }
        }
        write_file_cache('ManageMenu', $this->cache);
        return true;
    }
}
