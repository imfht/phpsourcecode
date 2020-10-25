<?php
namespace app\run\controller;

use app\common\controller\Run;

class PowerTree extends Run
{
    //初始化 需要调父级方法
    public function initialize()
    {        
        call_user_func(['parent', __FUNCTION__]); 
    }
    
    //列表 
    public function lists()
    {
        $this->addAction("新增一级{$this->mdl->cname}", array('PowerTree/create', ['parent_id' => 1]), 'fa-plus-circle');
        $this->addAction("一级{$this->mdl->cname}排序", array('PowerTree/sort', ['parent_id' => 1]), 'fa-sort');
        $this->setTitle("{$this->mdl->cname}结构", 'operation');
        $this->fetch = 'tree';
    }
    
    public function start(){
        
        if (!$this->request->isAjax()) {
            return $this->message('error', '请求方式错误');
        }
        
        if ($this->mdl->count()) {
            return $this->ajax('error', '节点已存在，不能再执行初始化');
        }
        
        $map = [
            'lists' => '查看列表',
            'create' => '新增',
            'modify' => '更新',
            'delete' => '删除',
            'sort' => '排序',            
            'batch_delete' => '批量删除',
            'ajax_switch' => '列表开关',
            'ajax_set_field' => '列表值设置'
        ];
        
        $map_else = [
            'Dustbin' => [
                'recover' => '还原数据'
            ],
            'Menu' => [
                'create_position' => '栏目广告位'
            ],
            'Model' => [
                'datadict' => '数据字典'
            ],
            'Power' => [
                'content' => '用户授权',
                'remove' => '删除授权',
                'lists' => false,
                'create' => false,
                'modify' => false,
                'delete' => false,
                'sort' => false,            
                'batch_delete' => false,
                'ajax_switch' => false,
                'ajax_set_field' => false
            ],
            'Setting' => [
                'set' => '设置值'
            ],
            'Member' => [
                'create' => false
            ]
        ];
        ##其他权限
        $else = [
            [
                'title' => '模型生成',
                'controller' => 'Tool',
                'action' => 'addm'
            ],
            [
                'title' => '模板创建',
                'controller' => 'Tool',
                'action' => 'addv'
            ],
            [
                'title' => '控制器生成',
                'controller' => 'Tool',
                'action' => 'addc'
            ],            
            [
                'title' => '超级权限',
                'controller' => 'All',
                'action' => 'all'
            ],
        ];
        
        $db = db($this->m); 
        $rootId = $db->insertGetId(['id' => 1, 'parent_id' => 0, 'title' =>'根节点', 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')]);  
        
        unset($GLOBALS['Model_title']['Exlink']); ##外链没有操作 
        unset($GLOBALS['Model_title']['Picture']); ##不同的图片类型单独设置
        
              
        foreach ($GLOBALS['Model_title'] as $model => $model_name) {
            $data = [
                'id' => null,
                'parent_id' => $rootId,
                'title' => $model_name,
                'controller' => $model,
                'together' => strtolower(trim($model)) . '::',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ];
            $parentId = $db->insertGetId($data);  
                 
            $children = $map;
            if (isset($map_else[$model])) {
                $children = array_merge($map, $map_else[$model]);
            }
            
            foreach ($children as $action => $action_name) {
                if ($action_name !== false) {
                    $data = [
                        'id' => null,
                        'parent_id' => $parentId,
                        'title' => $action_name,
                        'controller' => $model,
                        'action' => $action,
                        'together' => strtolower(trim($model)) . '::' . strtolower(trim($action)),
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s')
                    ];
                    $db->insertGetId($data);
                }              
            }
        }
        
        $elseId = $db->insertGetId(['id' => null, 'parent_id' => $rootId, 'title' =>'其他权限', 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')]);  
        foreach ($else as $each) {
            $data = [
                'id' => null,
                'parent_id' => $elseId,
                'title' => $each['title'],
                'controller' => $each['controller'],
                'action' => $each['action'],
                'together' => strtolower(trim($each['controller'])) . '::' . strtolower(trim($each['action'])),
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ];
            $db->insertGetId($data);
        }
        $this->mdl->writeToFile();     
        
        $this->loadModel('Dictionary');
        if (!$this->Dictionary->where(['model' => $this->m, 'field' => 'action'])->count()) {
            $this->Dictionary->save(['title' => '权限节点.方法名', 'model' => $this->m, 'field' => 'action','dictionary_item_count' => count($map)]);
            $dictId = $this->Dictionary->id;
            foreach ($map as $action => $action_name) {
                db('DictionaryItem')->insert([
                    'id' => null,
                    'dictionary_id' => $dictId,
                    'value' => $action,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s')
                ]);
            }
            $this->Dictionary->write_file($dictId);
        }
        $this->loadModel('Power');
        $this->Power->save([
            'id' => null,
            'type' => 'user',
            'foreign_id' => $this->Auth->user('id'),
            'user_id' => $this->Auth->user('id'),
            'content' => ['all::all']
        ]);
        
        return $this->ajax('success', '处理完成(当前用户自动拥有超级权限，请自行修改)');
        
    }
    
    public function sort()
    {
        $this->local['order'] = array('list_order' => 'ASC', 'id' => 'ASC');
        if (empty($this->args)) $this->args['parent_id'] = 1;
        call_user_func(array('parent', __FUNCTION__));
    }
    
    //添加
    public function create()
    {       
        if ($this->args['parent_id']) {
            $controller  = powertree(intval($this->args['parent_id']), 'controller');
            if ($controller) {
                $this->assignDefault('controller', $controller);
            }
        }
        return call_user_func(['parent', __FUNCTION__]);
    }
    
    //修改
    public function modify()
    {   
        return call_user_func(['parent', __FUNCTION__]);
    } 
    
    //删除
    public function delete()
    {        
        return call_user_func(['parent', __FUNCTION__]);
    }  
}
