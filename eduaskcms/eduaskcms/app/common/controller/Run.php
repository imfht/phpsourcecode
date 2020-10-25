<?php
namespace app\common\controller;

use app\common\utility\Hash;
use app\common\utility\TpText;

class Run extends App
{
    public $iconMap = array(
        'create' => 'fa-plus-circle',
        'sort' => 'fa-sort',
        'view' => 'qa_view',
        'parent' => 'fa-reply',
        'export' => 'qa_list',
        'batch_delete' => 'fa-trash'
    );

    protected function initialize()
    {
        $this->addTitle('后台管理系统');
        $this->assign->dev = read_file_cache('Developer');        
        call_user_func(array('parent', __FUNCTION__));
        
        $is_power = $this->Auth->check_power();        
        if (!$is_power) {
            $this->redirect('nopower');
            exit;
        }
    }
    
    public function nopower()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '没有权限的操作', ['close' => true, 'back' => false]);
        } else {
            return $this->ajax('nopower', '没有权限的操作');
        }
        exit;
    }

    public function index()
    {
        $this->redirect('lists');
    }
    
    public function create()
    {
        $pk = $this->mdl->getPk();

        if (!isset($this->local['contain'])) {
            $this->local['contain'] = null;
        }

        //设置默认值
        if ($this->mdl->form['user_id'] && !isset($this->args['user_id'])) {
            $this->assignDefault('user_id', $this->login['id']);
        }

        foreach ($this->args as $field => $value) {
            if ($field == 'parent_id') {
                if (isset($this->mdl->parentModel)) {
                    if ($this->mdl->form[$this->local['parent_conj']]) {
                        $this->assignDefault($this->local['parent_conj'], intval($this->args['parent_id']));
                    }
                }
            } else {
                if ($this->mdl->form[$field]) {
                    $this->assignDefault($field, $value);
                }
            }
        }
        
		if($this->mdl->form['is_verify']){
			$this->assignDefault('is_verify',!setting('is_verify') || setting('default_verify'));
		}
        

        if ($this->request->isPost()) {
            $data = $this->Form->data;
            unset($this->Form->data[$this->m][$pk]);

            ##添加前回调方法
            $this->before_create($this->Form->data);
            ##设置数据对象值
            $this->mdl->data($this->Form->data[$this->m]);
            ##执行添加
            
            $result = $this->mdl->isUpdate(false)->allowField((array)$this->local['whiteList'])->save();

            if ($result) {
                ##获取主键                
                $save_id = $this->mdl->{$pk};
                ##关联新增

                $save_data = $this->mdl->get($save_id);

                if ($save_data) $save_data = $save_data->toArray();
                ##添加后回调方法
                $this->after_create($save_data);
                
                if (!isset($this->local['success_redirect'])) {
                    return $this->message('success', "{$this->mdl->cname}[ID:{$save_id}]添加成功", array('返回列表' => array($this->m . '/lists', ['parent_id' => $save_data[$this->local['parent_conj']]])));
                } else {
                    return $this->message('success', "{$this->mdl->cname}[ID:{$save_id}]添加成功", $this->local['success_redirect']);
                }
                
            } else {
                $this->Form->data[$this->m] = $this->mdl->getData();
                if (!$this->mdl->getError())
                    $this->assign('error', '添加失败【未知原因】');
            }

        } else {
            if(isset($this->args['copy_id'])) {
                $copy_id = intval($this->args['copy_id']);
                $copy_data = $this->mdl->where(array_merge((array)$this->local['where'], array($pk => array($pk, 'eq', $copy_id))))->find();
                
                if ($copy_data) {
                    $this->Form->data[$this->m] = $copy_data->getArray();
                }
            }
        }
        $this->assignAssocValue();
        if (!isset($this->local['title'])) {
            $this->local['title'] = "新增{$this->mdl->cname}";
        }
        
        $this->setTitle($this->local['title'], 'operation');
        $this->addAction("返回列表", array($this->m . '/lists', array('parent_id' => $this->args['parent_id'])), 'fa-reply');

        $this->assign->addJs('/editor-4.5.6/ckeditor.js');
        $this->assign->addJs('/editor-4.5.6/adapters/jquery.js');
        
        $this->assign->addCss('/files/colorpicker/css/colorpicker.css');
        $this->assign->addJs('/files/colorpicker/js/colorpicker.js');

        $this->assign->addJs('artTemplate.js');
        $this->assign->addJs('json2.js');
        return $this->fetch = 'form';
    }

    ##添加之前执行
    protected function before_create(&$data)
    {

    }

    ##添加成功之后执行
    protected function after_create($save_data)
    {

    }

    public function ajax_set_field()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        $id = intval($this->args['id']);
        if (empty($id)) {
            return $this->ajax('error', '缺少参数:ID');
        }
        $pk = $this->mdl->getPk();
        $old_data = $this->mdl->where(array_merge((array)$this->local['where'], [$pk => [$pk ,'eq', $id]]))->find();
        if (empty($old_data)) {
            return $this->ajax('error', '数据【ID:' . $id . '】不存在');
        }
        $this->mdl->old_data = $old_data->toArray();

        $field = strval($this->args['field']);
        if (empty($field)) {
            return $this->ajax('error', '需要设置的字段不存在');
        }
        if (!isset($this->mdl->form[$field])) {
            return $this->ajax('error', "模型{$this->m}中不包括{$field}字段");
        }

        $value = trim(urldecode($this->args['value']));
        $data = [$field => $value, $pk => $id];
        $this->before_modify($data);

        $data = $data + $this->mdl->old_data;

        $this->mdl->data($data);
        $result = $this->mdl->isUpdate(true)->save();

        if ($result || !$this->mdl->getError()) {
            $save_data = $this->mdl->getData();
            $this->after_modify($save_data);
            return $this->ajax('success', $this->mdl->form[$field]['name'] . '设置成功');
        } else {
            return $this->ajax('error', reset($this->mdl->getError()));
        }
    }

    public function ajax_switch()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        $id = intval($this->args['id']);
        if (empty($id)) {
            return $this->ajax('error', '缺少参数:ID');
        }

        $pk = $this->mdl->getPk();

        $old_data = $this->mdl->where(array_merge((array)$this->local['where'], [$pk => [$pk ,'eq', $id]]))->find();
        if (empty($old_data)) {
            return $this->ajax('error', '数据【ID:' . $id . '】不存在');
        }
        $this->mdl->old_data = $old_data->toArray();

        $field = strval($this->args['field']);
        if (empty($field)) {
            return $this->ajax('error', '需要设置的字段不存在');
        }
        if (!isset($this->mdl->form[$field])) {
            return $this->ajax('error', "模型{$this->m}中不包括{$field}字段");
        }

        $value = intval($this->args['value']);

        $data = [$field => !$value, $pk => $id];
        $this->before_modify($data);

        $this->mdl->is_validate = false;
        $this->mdl->data($data);
        $result = $this->mdl->isUpdate(true)->save();

        if ($result) {
            $save_data = $this->mdl->getData();
            $this->after_modify($save_data);
            return $this->ajax('success', $this->mdl->form[$field]['name'] . '设置成功', array(
                'result' => !$value,
                'url' => url('ajax_switch', ['id' => $id, 'field' => $field, 'value' => !$value])
            ));

        } else {
            return $this->ajax('error', $this->mdl->form[$field]['name'] . '设置失败');
        }
    }

    public function modify()
    {
        if ($this->local['id']) $id = intval($this->local['id']);
        else $id = intval($this->args['id']);


        if (!$id) $this->redirect($this->m . '/create', $this->args);
        $pk = $this->mdl->getPk();

        $old_data = $this->mdl->where(array_merge((array)$this->local['where'], [$pk => [$pk, 'eq', $id]]))->find();

        if ($this->request->isPost() && $old_data) {
            ##获取到数据
            $this->mdl->old_data = $old_data->toArray();

            $this->Form->data[$this->m][$pk] = $id; 
            ##更新前回调方法
            $this->before_modify($this->Form->data);  
            ##设置数据对象值
            $this->mdl->data($this->Form->data[$this->m]);
            ##执行更新            
            $result = $this->mdl->isUpdate(true)->allowField((array)$this->local['whiteList'])->force()->save();

            if ($result) {
                ##关联更新
                ##更新后回调方法
                $save_data = $this->mdl->getData();
                $this->after_modify($save_data);
                if (!isset($this->local['success_redirect'])) {
                    return $this->message('success', "{$this->mdl->cname}[ID:{$id}]修改成功", array('返回列表' => array($this->m . '/lists', ['parent_id' => $save_data[$this->local['parent_conj']]])));
                } else {
                    return $this->message('success', "{$this->mdl->cname}[ID:{$id}]修改成功", $this->local['success_redirect']);
                }
                
            } else {
                $this->Form->data[$this->m] = array_merge($this->Form->data[$this->m], $this->mdl->getData());
                if (!$this->mdl->getError()) {
                    //$this->assign('error','修改失败，可能没有修改任何数据');
                    if (!isset($this->local['success_redirect'])) {
                        return $this->message('success', "{$this->mdl->cname}[ID:{$id}]修改成功", array('返回列表' => array($this->m . '/lists', ['parent_id' => $save_data[$this->local['parent_conj']]])));
                    } else {
                        return $this->message('success', "{$this->mdl->cname}[ID:{$id}]修改成功", $this->local['success_redirect']);
                    }
                }
            }

        } else {
            if (empty($old_data)) {
                $this->redirect($this->m . '/create');
            }
            ##获取到数据
            $this->mdl->old_data = $old_data->toArray();
            $old_form_data = $this->Form->data;
            if ($old_form_data[$this->m]) {
                $this->Form->data[$this->m] = $this->mdl->old_data;
                $this->Form->data[$this->m] = array_merge($this->Form->data[$this->m], $old_form_data[$this->m]);
            } else {
                $this->Form->data[$this->m] = $this->mdl->old_data;
            }
        }        
        $this->assignAssocValue();        
        if (!isset($this->local['title'])) {
            $this->local['title'] = "修改{$this->mdl->cname}[ID:{$id}]";
        }
        $this->setTitle($this->local['title'], 'operation');
        $this->addAction("返回列表", array($this->m . '/lists', array('parent_id' => $this->mdl->old_data[$this->local['parent_conj']])), 'fa-reply');

        $this->assign->addJs('/editor-4.5.6/ckeditor.js');
        $this->assign->addJs('/editor-4.5.6/adapters/jquery.js');
        
        $this->assign->addCss('/files/colorpicker/css/colorpicker.css');
        $this->assign->addJs('/files/colorpicker/js/colorpicker.js');
        
        $this->assign->addJs('artTemplate.js');
        $this->assign->addJs('json2.js');

        return $this->fetch = 'form';
    }
    
    protected function assignAssocValue()
    {
        if (empty($this->Form->data[$this->m])) {
            return false;
        }
        foreach ($this->Form->data[$this->m] as $field => $value) {
            if (!in_array($this->mdl->form[$field]['elem'], ['assoc_select']) || !$value) {
                continue;
            }
            list($assoc_model, $assoc_field) = PluginSplit($this->mdl->form[$field]['foreign']);
            if ($assoc_model) {
                $this->loadModel($assoc_model);
                $this->assign->assoc_value[$field] = $this->$assoc_model->where(['id' => $value])->value($assoc_field);
            }
        }
    }

    ##更新之前执行
    protected function before_modify(&$data)
    {

    }

    ##更新成功之后执行
    protected function after_modify($save_data)
    {

    }

    public function delete()
    {
        if (!isset($this->args['id'])) {
            return $this->message('error', "缺少【id】参数");
        }
        
        $id = intval($this->args['id']);
        $pk = $this->mdl->getPk();
        ##获取到需要删除的数据
        $delete_model = $this->mdl->where(array_merge((array)$this->local['where'], [$pk => [$pk, 'eq', $id]]))->find();

        if (empty($delete_model)) {
            return $this->message('error', "{$this->mdl->cname}[ID:{$id}]数据不存在或条件不允许删除");
        }

        ##删除之前回调，返回false将不会执行删除
        if (false === $this->before_delete($delete_model)) return false;
        ##删除的数据
        $delete_data = $delete_model->toArray();
        ##删除对象
        $result = $delete_model->delete();
        if ($result) {
            ##删除成功
            ##删除成功以后，
            $this->after_delete($delete_data);
            if (!isset($this->local['success_redirect'])) {
                return $this->message('success', "{$this->mdl->cname}[ID:{$id}]删除成功", array('返回列表' => array($this->m . '/lists', ['parent_id' => $delete_data[$this->local['parent_conj']]])));
            } else {
                return $this->message('success', "{$this->mdl->cname}[ID:{$id}]删除成功", $this->local['success_redirect']);
            }
            
        } else {
            ##删除失败
            if (!isset($this->local['error_redirect'])) {
                return $this->message('error', "{$this->mdl->cname}[ID:{$id}]删除失败", array('返回列表' => array($this->m . '/lists', ['parent_id' => $delete_data[$this->local['parent_conj']]])));
            } else {
                return $this->message('error', "{$this->mdl->cname}[ID:{$id}]删除失败", $this->local['error_redirect']);
            }
        }
    }

    public function batch_delete()
    {
        if (!$this->request->isPost()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        $pk = $this->mdl->getPk();

        $selected_data = $this->request->post();
        $selected_ids = $selected_data['selected_id'];
        ##获取到需要删除的数据
        $delete_model = $this->mdl->where(array_merge((array)$this->local['where'], array($pk => array($pk, 'in', $selected_ids))))->select();

        if (empty($delete_model)) {
            return $this->message('error', "{$this->mdl->cname}[ID:" . implode(',', $selected_ids) . "]数据不存在或条件不允许删除");
        }

        ##删除之前回调，返回false将不会执行删除
        if (false === $this->before_delete($delete_model)) {
            return false;
        }
        ##删除的数据
        $delete_data = array();
        $delete_ids = array();
        foreach ($delete_model as $item) {
            $data = $item->toArray();
            $delete_ids[] = $data[$pk];
            $delete_data[] = $data;
        }
        $selected_count = count($selected_ids);
        $result = $this->mdl->destroy($delete_ids);##返回删除数量
        if ($result) {
            $this->after_delete($delete_data);
            $undelete = $selected_count - $result;
            if ($undelete) {
                return $this->message('success', "批量删除{$this->mdl->cname}成功，{$result}条删除成功，{$undelete}条删除失败");
            } else {
                return $this->message('success', "批量删除{$this->mdl->cname}成功，{$result}条删除成功");
            }

        } else {
            ##删除失败
            return $this->message('error', "{$this->mdl->cname}[ID:" . implode(',', $selected_ids) . "]批量删除失败");
        }
    }


    ##删除之前执行  参数是对象
    protected function before_delete($delete_model)
    {

    }

    ##删除成功之后执行 参数是删除的数据
    protected function after_delete($data)
    {

    }

    public function sort()
    {
        //order 默认值
        if (!$this->mdl->form['list_order']) {
            return $this->message('error', '模型【' . $this->m . '】form属性中不含有list_order字段');
        }

        if ($this->mdl->form['list_order'] && empty($this->local['order']['list_order'])) {
            $this->local['order']["list_order"] = "DESC";
        }
        if (empty($this->local['order']['id'])) {
            $this->local['order']["id"] = "DESC";
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $order_list = explode(',', $data['data']['order']);
            if (strtoupper($this->local['order']['list_order']) != 'ASC')
                $order_list = array_reverse($order_list);

            foreach ($order_list as $order => $id) {
                $updateData[] = $eachData = array('id' => $id, 'list_order' => $order);
                $this->mdl->isValidate(false)->isUpdate(true)->save($eachData);
            }
            $this->after_modify($updateData);
            if (!isset($this->local['success_redirect'])) {
                return $this->message('success', "{$this->mdl->cname}排序操作成功", ['返回列表' => array($this->m . '/lists', $this->args)]);
            } else {
                return $this->message('success', "{$this->mdl->cname}排序操作成功", $this->local['success_redirect']);
            }
        } else {
            
            $this->local['parent_return_url'] = false;
            $this->parseListParent();            

            if (!isset($this->local['contain'])) {
                $this->local['contain'] = null;
            }
            $list = $this->mdl->with($this->mdl->parseWith($this->local['contain']))->where($this->local['where'])->order($this->local['order'])->field([])->select();
            if (empty($list)) {
                return $this->message('error', '当前排序数据不存在');
            }

            $list = $this->mdl->getArray($list);


            $this->assign->addJs('/files/jquery-ui-1.12.1/jquery-ui.min.js');
            $this->assign->list = $list;
            
            if (!isset($this->local['title'])) {
                $this->local['title'] = "{$this->mdl->cname}排序";
            }
            $this->setTitle($this->local['title'], 'operation');
            $this->addAction("返回列表", array($this->m . '/lists', $this->args), 'fa-reply');
            return $this->fetch = 'sort';
        }
    }

    public function lists()
    {
        ##搜索
        if ($this->request->isPost()) {
            if (isset($this->Form->data['Search'])) {
                $search = Hash::flatten($this->Form->data['Search']);
                foreach($search as $field=>$value){
					if(trim($value) === ''){
						unset($search[$field]);
						unset($this->args[$field]);
					}
				}
				unset($search['controller']);
				unset($search['action']);
                $this->redirect('lists', $search + $this->args);
				exit;
            }
        }

        ##顶部方法
        if (!isset($this->local['actions']['create'])) {
            $this->local['actions']['create'] = array("新增" => array($this->m . '/create', $this->args));
        }
        if ($this->local['sortable'] && $this->args['parent_id'] && $this->mdl->form['list_order']) {
            $this->local['actions']['sort'] = array("排序" => array($this->m . '/sort', $this->args));
        }
        if (!isset($this->local['actions']['batch_delete'])) {
            $this->local['actions']['batch_delete'] = array('批量删除' => 'javascript:void(0)');
        }

        ##图片关联
        $use_picture_model = setting('use_picture_model');
        if ($use_picture_model) {
            $use_picture_model = json_decode($use_picture_model, true);
        } else {
            $use_picture_model = [];
        }
        
        if (in_array($this->m, $use_picture_model)) {
            if (!$this->local['list_fields']['picture_count'] && isset($this->mdl->form['picture_count'])) {
                $this->local['list_fields'] = $this->local['list_fields'] + array('picture_count' => $this->mdl->form['picture_count']);
            }
            $this->addItemAction('添加图片', array($this->m . 'Picture/create', ['parent_id' => 'id'], 'parse' => ['parent_id']), '&#xe654;');
        }
        
        foreach ($this->local['actions'] as $key => $item) {
            if (is_array($item)) {
                $this->addAction(key($item), current($item), $this->iconMap[$key], $key);
            } else {
                $this->addAction($item);
            }
        }
        
        if ($this->local['model'] === null) {
            $this->local['model'] = $this->mdl;  
            $this->parseListParent();        
            $this->parseListFields();            
            $this->parseListFilters();
        }
        
        ##列表方法 
        if (!isset($this->local['item_actions']['modify'])) {
            $this->assign->item_actions[] = 'modify';
        }
        if (isset($this->local['item_actions']['copy']) && $this->local['item_actions']['copy']) {
            $this->assign->item_actions[] = 'copy';
        }
        if (!isset($this->local['item_actions']['delete'])) {
            $this->assign->item_actions[] = 'delete';
        }
        $this->assign->item_actions = Hash::merge(
            (array)$this->assign->item_actions,
            (array)$this->local['item_actions']
        );
        ##排序        
        if (isset($this->args['sort']) && $this->mdl->form[$this->args['sort']]) {
            $direction = isset($this->args['direction']) ? (in_array(strtoupper($this->args['direction']), ['DESC', 'ASC']) ? strtoupper($this->args['direction']) : 'DESC') : 'DESC';
            $this->local['order'][$this->args['sort']] = $direction;
        }        
        //order 默认值
        if ($this->mdl->form['list_order'] && !isset($this->local['order']['list_order'])) {
            $this->local['order']["list_order"] = "DESC";
        }
        if (!isset($this->local['order']['id'])) {
            $this->local['order']["id"] = "DESC";
        }
        ##limit 默认值
        if (!$this->local['limit']) {
            $this->local['limit'] = intval(setting('admin_list_count'));
            if ($this->local['limit'] <= 0) {
                $this->local['limit'] = 15;
            }
        }

        /*[20170718列表使用缓存*/
        $requestUrl = $this->request->url();
        $isQuery = true;

        if (setting('is_admin_cache')) {
            $listCache = \Cache::get($requestUrl);
            if ($listCache) {
                $isQuery = false;
                $this->assign->list = $this->list = $listCache['list'];
                $this->assign->page = $this->page = $listCache['page'];
            }
        }
        if ($isQuery) {
            $this->getPage($this->mdl);
            $this->assign->list = $this->list;
            unset($this->page['data']);
            $this->assign->page = $this->page;
            if (setting('is_admin_cache')) {
                \Cache::tag($this->m)->set($requestUrl, ['list' => $this->list, 'page' => $this->page], 3600);
            }
        }
        /*
        $this->list($this->mdl);        
        $this->assign->list  = $this->list;
        unset($this->page['data']);        
        $this->assign->page  = $this->page;
        */
        /*]*/
        
        $this->setTitle("{$this->mdl->cname}列表", 'operation');
        $this->assign->addJs('tableResize');
        $this->assign->addJs('/files/lightense/lightense.min');
        return $this->fetch = 'lists';
    }
    
    public function assignDefault($col, $val, $mdl = null, $over_write_empty = false, $index = null)
    {
        if (empty($mdl)) $mdl = $this->m;

        if ($index !== null) {
            if (!isset($this->Form->data[$mdl][$index][$col]) || ($over_write_empty && empty($this->Form->data[$mdl][$index][$col]))) {
                $this->assign->default_data[$mdl][$index][$col] = $this->Form->data[$mdl][$index][$col] = $val;
            }
        } else {
            if (!isset($this->Form->data[$mdl][$col]) || ($over_write_empty && empty($this->Form->data[$mdl][$col]))) {
                $this->assign->default_data[$mdl][$col] = $this->Form->data[$mdl][$col] = $val;
            }
        }
    }

    public function assignValue($col, $val, $mdl = null, $index = null)
    {
        if (empty($mdl)) {
            $mdl = $this->m;
        }

        if ($index !== null) {
            $this->assign->default_data[$mdl][$index][$col] = $this->Form->data[$mdl][$index][$col] = $val;
        } else {
            $this->assign->default_data[$mdl][$col] = $this->Form->data[$mdl][$col] = $val;
        }
    }
        
    protected function addFilter($col, $value)
    {
        if (is_array($value)) {
            $value = implode(' - ', Hash::flatten($value));
        }
        $this->assign->filter[$col] = $value;
    }

    protected function addAction($title, $url = null, $icon = null, $class = null)
    {
        $this->assign->actions = (array)$this->assign->actions;
        if (!isset($url)) {
            array_push($this->assign->actions, $title);
        } else {
            array_push($this->assign->actions, compact('title', 'url', 'icon', 'class'));
        }
    }

    protected function addItemAction($title, $url = null, $icon = null, $class = null)
    {
        $this->assign->item_actions = (array)$this->assign->item_actions;
        if (!isset($url)) {
            $this->assign->item_actions[] = $title;
        } else {
            $this->assign->item_actions[] = compact('title', 'url', 'icon', 'class');
        }
    }
    
    protected function  parseListParent()
    {
        $parent_info = [];
        if (isset($this->mdl->parentModel)) {
            if ($this->mdl->parentModel != 'parent') {
                $parent_mdl = $this->loadModel($this->mdl->parentModel);
            } else {
                $parent_mdl = $this->mdl;
            }            
            if (isset($this->args['parent_id'])) {
                $parent_id = intval($this->args['parent_id']);
                $parent_data = $parent_mdl->where([$parent_mdl->getPk() => $parent_id])->find();
                if ($parent_data) {
                    $parent_info = [
                        'mdl' => $this->mdl->parentModel,
                        'id' => $parent_id,
                        'cname' => $parent_mdl->cname,
                        'title' => $parent_data[$parent_mdl->display ? $parent_mdl->display : 'title']
                    ];
                }
                if (!isset($this->local['where'][$this->local['parent_conj']])) {
                    $this->local['where'][$this->local['parent_conj']] = $parent_id;
                }
                
                $this->local['filter'] = Hash::normalize((array)$this->local['filter']);
                if (key_exists($this->local['parent_conj'], $this->local['filter']) && !isset($this->local['filter'][$this->local['parent_conj']]['hide'])) {
                    $this->local['filter'][$this->local['parent_conj']]['hide'] = true;
                }
                
                $this->local['list_fields'] = Hash::normalize((array)$this->local['list_fields']);
                if (key_exists($this->local['parent_conj'], $this->local['list_fields']) && !isset($this->local['list_fields'][$this->local['parent_conj']]['list'])) {
                    $this->local['list_fields'][$this->local['parent_conj']]['list'] = false;
                }
            }
            
            if (isset($this->mdl->parentModel) && $this->mdl->parentModel != 'parent' && $this->local['parent_return_url'] !== false) {
                if (!$this->local['parent_return_url']) {
                    if ($this->mdl->parentModel == 'Menu') {
                        $this->local['parent_return_url'] = array($this->mdl->parentModel . '/content');
                    } else {
                        $parent_parent_id = null;
                        if (isset($parent_mdl->parentModel)) {
                            $parent_parent_foreign_key  = isset($parent_mdl->assoc[$parent_mdl->parentModel]['foreignKey']) ? $parent_mdl->assoc[$parent_mdl->parentModel]['foreignKey'] : parse_name($parent_mdl->parentModel) . '_id'; 
                            $parent_parent_id = $parent_data[$parent_parent_foreign_key] ? $parent_data[$parent_parent_foreign_key] : null;
                        }
                        if ($parent_parent_id) {
                            $this->local['parent_return_url'] = array($this->mdl->parentModel . '/lists', ['parent_id' => $parent_parent_id]);
                        } else {
                            $this->local['parent_return_url'] = array($this->mdl->parentModel . '/lists');
                        }
                    }   
                }
                $this->addAction("返回父级", $this->local['parent_return_url'], $this->iconMap['parent']);
            }
        }
        $this->assign->parent_info  = $parent_info;
    }
    
    public function parseListFilters()
    {
        if (!isset($this->local['filter'])) {
            $this->local['filter'] = [];
        }
        
        if(!is_array($this->local['filter'])) $this->local['filter'] = [];
        $fields = Hash::normalize((array)$this->local['filter']);
        
        foreach ($fields as $field => &$info) {
            if (!isset($info['name'])) {
                if (array_key_exists($field, (array)$this->mdl->form)) {
                    $info['name'] = $this->mdl->form[$field]['name'];
                } else {
                   $info['name'] = $field; 
                }
            }            
            if (!isset($info['elem'])) {
                if ($this->mdl->form[$field]['elem'] === 'date') {
                    $info['elem'] = 'date'; 
                    if (!isset($info['options'])) {
                        $info['options'] =  $this->mdl->form[$field]['options'];
                    }
                    if ($info['options'] == 'datetime' && !isset($info['type'])) {
                        $info['type'] = 'datetime';
                    }                
                } elseif (isset($info['options']) && is_array($info['options'])) {
                    $info['elem'] = 'options';
                } elseif (isset($this->mdl->form[$field]['options'])) {
                    $info['elem'] = 'options';
                    $info['options'] = $this->mdl->form[$field]['options'];
                } else {
                    $info['elem'] = 'text';
                }
            }
            if (!isset($info['type'])) {
                if (isset($this->mdl->form[$field]['type'])) {
                    $info['type'] = $this->mdl->form[$field]['type'];
                } else {
                    $info['type'] = 'string';
                }                
                if ($info['elem'] == 'datetime' || $info['options']['type'] == 'datetime') {
                    $info['type'] = 'datetime';
                }                
                if ($info['elem'] == 'date_range' || $info['elem'] == 'number_range') {
                    $info['type'] = 'range';
                }                
            }
            
            
            $info['value'] = Hash::get((array)$this->args, $field);
            if (!isset($info['hide'])) {
                $info['hide'] = false;
            }   
                     
            if (isset($this->mdl->form[$field]['foreign'])) {
                $info['foreign'] = $this->mdl->form[$field]['foreign'];
            }
            
            if (!isset($this->args[$field])) {
                continue;
            }
            $val = trim($this->args[$field]);
            
            if (isset($this->local['where'][$field])) {
                $where_result = true;
            }
            
            if(isset($info['where']) && is_callable($info['where']) && !isset($where_result)){
                $where_result = call_user_func_array($info['where'], array($val, $field, &$info));
                $this->local['where'] = array_merge_recursive((array)$this->local['where'], (array)$where_result);
				$where_result = true;
            }
            
            if (isset($info['foreign']) && !$where_result) {
                list($foreign_mdl, $foreign_field) = pluginSplit($info['foreign']);
                if ($this->mdl->assoc[$foreign_mdl]['type'] == 'belongsTo') {
                    $this->loadModel($foreign_mdl);
                    $foreign_pk = $this->$foreign_mdl->getPk();                    
                    $foreign_ids  = $this->$foreign_mdl->where($foreign_field, $val)->column($foreign_pk);
                    if (empty($foreign_ids)) {
                        $foreign_ids = 0;
                    }
                    $this->local['where'][$field]= [$field, 'IN', $foreign_ids];
                    $where_result = true;
                }
            }
            
            ##只有是当前模型字段，或者自定义了where回调才处理条件，其他自定义字段需要自己处理条件
            if (array_key_exists($field, (array)$this->mdl->form) && !isset($where_result)) {
                if (in_array($info['type'], ['text', 'string'])) {
                    $this->local['where'][$field] = [$field, 'LIKE', '%' . urldecode($val) . '%'];
                } elseif ($info['type'] == 'datetime') {
                    $this->local['where'][$field] = [$field, 'LIKE', urldecode($val) . '%'];
                } elseif ($info['type'] == 'range') {
                    $rangeVal = explode('~', urldecode($val));
                    if (trim($rangeVal[0]) !== '') {
                        $this->local['where'][$field][] = [$field, 'egt', trim($rangeVal[0])];
                    }
                    if (trim($rangeVal[1]) !== '') {
                        $this->local['where'][$field][] = [$field, 'elt', trim($rangeVal[1])];
                    }                    
                } else {
                    $this->local['where'][$field] = urldecode($val);
                }
            }
            /*
            if($info['options']){
				if (array_key_exists($val, $info['options'])) {
					$local_val=$info['options'][$val];
				} else {
					continue;
				}
			}*/
            if(!isset($local_val))$local_val=$val;
            $this->addFilter($field, $local_val);
			unset($local_val);
            unset($where_result);
        }
        $this->assign->list_search_fields = $fields;
        return true;
    }
    
    public function parseListFields()
    {
        if (!isset($this->local['list_fields'])) {
            return $this->error('该模块未设置列表字段');
        }

        if ($this->mdl && $this->mdl->form['id']) {
            array_unshift($this->local['list_fields'], 'id');
        }

        $this->local['list_fields'] = Hash::normalize($this->local['list_fields']);


        $this->local['fields'] = array();

        foreach ($this->local['list_fields'] as $col => &$info) {

            list($local_mdl, $field) = pluginSplit($col);
            if (isset($info) && !$info) {
                $this->local['fields'][$this->m][] = "$field";
                continue;
            }
            $is_foreign_filed = false;
            if (!$local_mdl || $local_mdl == $this->m) {
                if (isset($this->mdl->form[$field]['foreign'])) {
                    list($foreign_mdl, $foreign_field) = pluginSplit($this->mdl->form[$field]['foreign']);
                    if (!isset($info['list']))
                        $info['list'] = 'assoc';
                    $info['foreign']['model'] = $foreign_mdl;
                    $info['foreign']['field'] = $foreign_field;
                }
            } else {
                //$field=$this->mdl->foreign[$col];
                list($foreign_mdl, $foreign_field) = pluginSplit($col);
                if ($foreign_mdl && $foreign_field) {
                    $info['list'] = 'assoc';
                    $info['foreign']['model'] = $foreign_mdl;
                    $info['foreign']['field'] = $foreign_field;
                    $is_foreign_filed = true;
                }
            }

            if (is_string($info)) {
                $info = array('list' => $info);
            }

            if ($info['list'] != 'assoc') {
                if ($this->mdl->form[$field]['type'] && ($this->mdl->form[$field]['type'] != 'none')) {
                    $this->local['fields'][$this->m][] = "$field";
                }

                settype($info, 'array');

                if (isset($this->mdl->form[$field]) && !is_array($this->mdl->form[$field]) && $info['type'] != 'none') {
                    return $this->message('error', "未能在{$this->m}的{$field}字段上得到array");
                }
                $info += (array)$this->mdl->form[$field];

                if (isset($this->mdl->form[$field]['image']['thumb'])) {
                    $thumb_field = $this->mdl->form[$field]['image']['thumb']['field'] ? $this->mdl->form[$field]['image']['thumb']['field'] : 'thumb';
                    $this->local['fields'][$this->m][] = "$thumb_field";
                }
            } else {
                $this->local['contain'][$foreign_mdl]['field'][] = $foreign_field;
                if (!$is_foreign_filed) {
                    $info += (array)$this->mdl->form[$field];
                }

                settype($info['foreign'], 'array');

                $this->loadModel($foreign_mdl);
                $info['foreign'] += (array)$this->$foreign_mdl->form[$foreign_field];
                if ($field && $this->mdl->form[$field]) {
                    $this->local['fields'][$this->m][] = "$field";
                }
            }

            if (!isset($info['list'])) {
                if (isset($info['options'])) {
                    $info['list'] = 'options';
                } else {
                    $info['list'] = 'show';
                }
            }
            $info['field'] = $field;

            $sortable_types = array('integer', 'float', 'date', 'datetime');
            if (!isset($info['sortable']) && (/*in_array($info['foreign']['type'], $sortable_types, true) ||*/ in_array($info['type'], $sortable_types, true))) {
                $info['sortable'] = true;
            }
        }
        $this->assign->list_fields = array_diff_key((array)$this->local['list_fields'], array_flip((array)$this->local['list_ignore_fields']));

        return true;
    }
}
