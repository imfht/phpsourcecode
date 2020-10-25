<?php
namespace app\common\controller;

use app\common\utility\Hash;
use app\common\utility\TpText;

class Home extends App
{
    public function __construct()
    { 
        if (request()->isMobile() && setting('is_use_wap')) {
            config('template.view_path', \Env::get('module_path') . 'wap' . DS);
        }
        call_user_func(array('parent', __FUNCTION__));
    }
    
    protected function initialize()
    {
        call_user_func(array('parent', __FUNCTION__));
    }

    public function show()
    {
        ##开启审核
        if ($this->mdl->form['is_verify'] && setting('is_verify')) {
            $this->local['where']['is_verify'] = ['is_verify', '=', 1];
        }
        
        if ($this->mdl->form['menu_id']) {
            if (!$this->getShowMenu()) return false;
        }

        
        ##排序
        if ($this->mdl->form['list_order'] && empty($this->local['order']['list_order']) && empty($this->local['order']["list_order"])) {
            $this->local['order']["list_order"] = "DESC";
        }
        if (empty($this->local['order']['id']) && empty($this->local['order']["id"])) {
            $this->local['order']["id"] = "DESC";
        }
        ##字段
        if (empty($this->local['fields'][$this->m])) {
            $this->local['fields'][$this->m] = array_keys($this->mdl->form);
        }
        ##关联
        //$this->local['contain'] = ['Menu'] ; 

        /*[20170718列表使用缓存*/
        $requestUrl = $this->request->url();
        $isQuery = true;

        $listCache = \think\facade\Cache::get($requestUrl);
        if ($listCache) {
            $isQuery = false;
            $this->assign->list = $this->list = $listCache['list'];
            $this->assign->page = $this->page = $listCache['page'];
            $this->assign->render = $this->render = $listCache['render'];
        }

        if ($isQuery) {
            $this->getPage($this->mdl);
            $this->assign->list = $this->list;
            unset($this->page['data']);
            $this->assign->page = $this->page;
            $this->assign->render = $this->render;
            //\think\facade\Cache::tag($this->m)->set($requestUrl, ['list'=>$this->list, 'page'=>$this->page, 'render'=>$this->render], 3600);
        }

        if (!$this->fetch) {
            $this->fetch = 'show';
        }
        config('default_ajax_return', 'html');
        return true;
    }

    public function view()
    {
        if (!$this->local['id']) {
            $this->local['id'] = intval($this->args['id']);
        }
        
        if (!$this->local['id']) {
            return $this->notFound();
        }
        
        ##开启审核
        if ($this->mdl->form['is_verify'] && setting('is_verify')) {
            $this->local['where']['is_verify'] = ['is_verify', '=', 1];
        }
        ##字段
        if (empty($this->local['fields'][$this->m])) {
            $this->local['fields'][$this->m] = array_keys($this->mdl->form);
        }
        if ($this->mdl->form['menu_id'] && !in_array('menu_id', $this->local['fields'][$this->m])) {
            $this->local['fields'][$this->m][] = 'menu_id';
        }


        $data = $this->mdl->with($this->mdl->parseWith($this->local['contain']))->field($this->local['fields'][$this->m])->where(array_merge((array)$this->local['where'], ['id' => ['id', '=',  $this->local['id']]]))->find();
       
        if (empty($data)) {
            return $this->notFound();
        }
        
        $data = $data->getArray();
        
        if (isset($data['menu_id'])) {
            $this->local['where']['menu_id'] = $data['menu_id'];
        }        
        
        $prev = $this->mdl->with($this->mdl->parseWith($this->local['contain']))->field($this->local['fields'][$this->m])->where(array_merge((array)$this->local['where'], ['id' => ['id', 'lt', $this->local['id']]]))->order(['id' => 'DESC'])->find();
        $next = $this->mdl->with($this->mdl->parseWith($this->local['contain']))->field($this->local['fields'][$this->m])->where(array_merge((array)$this->local['where'], ['id' => ['id', 'gt', $this->local['id']]]))->order(['id' => 'ASC'])->find();

        $this->assign->data = $data;
        $this->assign->prev = $prev ? $prev->getArray() : [];
        $this->assign->next = $next ? $next->getArray() : [];

        if ($this->mdl->form['visit_count']) {
            $this->mdl->where('id', $this->local['id'])->setInc('visit_count');
        }

        if ($data['menu_id']) {
            $this->processMenu($data['menu_id']);
        }


        if ($data['title']) {
            $this->addTitle($data['title']);
        }
        if ($data['keywords']) {
            $this->setKeywords($data['keywords']);
        }
        if ($data['description']) {
            $this->setDescription($data['description']);
        }
        
        if (!$this->fetch) {
            $this->fetch = 'view';
        }

        return true;
    }

    protected function getShowMenu()
    {
        if (empty($this->local['menu_id'])) {
            $this->local['menu_id'] = intval($this->args['menu_id']);
        }

        if (empty($this->local['menu_id'])) {
            return $this->notFound();
        }

        $menu_data = menu($this->local['menu_id']);
        if ($this->m != $menu_data['type']) {
            return $this->message('error', '当前访问栏目类型不匹配', ['返回首页' => url('Index/index')]);
        }
        
        if (empty($menu_data)) {
            return $this->notFound();
        }

        if ($menu_data['child_count'] && !$this->local['where']["menu_id"]) {
            $this->local['where']['menu_id'] = ['menu_id', 'IN', getClosestMenus($this->local['menu_id'])];
        } else {
            if (!$this->local['where']['menu_id']) {
                $this->local['where']['menu_id'] = ['menu_id', '=', $this->local['menu_id']];
            }
        }

        $this->processMenu($this->local['menu_id'], (array)$this->local['menu_options'] + array('limit' => true));

        return true;
    }

    protected function processMenu($menu_id, $options = [])
    {
        $menu_data = $this->menu_data = menu($menu_id);
        if (empty($menu_data)) {
            return $this->notFound();
        }

        $this->local['options'] = $options;

        if (!$this->local['options']['no_redirect'] && $this->menu_data['child_count'] && $this->menu_data['is_redirect']) {
            $children_menus = menu('children', $this->menu_data['id']);
            $first_child_id = reset($children_menus);
            if ($first_child_id) {
                if (menu($first_child_id, 'type') != 'Exlink') {
                    $alias = trim(menu($first_child_id, 'alias'));
                    if (!$alias) {
                        //$this->redirect($this->absroot . \think\Loader::parseName(menu($first_child_id,'type')) .'/show/'.$first_child_id.'.' . config('url_html_suffix'));
                        $this->redirect(menu($first_child_id, 'type') . '/show', ['menu_id' => $first_child_id]);
                    } else {
                        $this->redirect($this->absroot . $alias . '.' . config('url_html_suffix'));
                    }
                } else {
                    $this->redirect($this->furl(menu($first_child_id, 'ex_link')));
                }
            }
        }

        $this->assign->menu_data = $menu_data;
        $this->addTitle($menu_data['title']);


        $top_id = $menu_id;
        $path = array($menu_id);

        while (true) {
            $parent_id = menu($top_id, 'parent_id');
            if (!$limit) {
                $limit = menu($top_id, 'list_count');
            }
            if ($parent_id == 1 || !$parent_id) {
                break;
            }
            if (menu($parent_id, 'is_nav')) {
                array_unshift($path, $parent_id);
            }
            $top_id = $parent_id;
        }

        $this->assign->top_id = $top_id;
        $this->assign->path = $path;
        $local_menu_children = menu('nav_children');
        //sidemenu
        if ($menu_data['child_count']) {
            $this->assign->side_menu['top_menu'] = $menu_data['id'];
            $this->assign->side_menu['menus'] = (array)$local_menu_children[$menu_data['id']];
        } else {
            $this->assign->side_menu['top_menu'] = $menu_data['parent_id'];
            $this->assign->side_menu['menus'] = array();

            if ($menu_data['parent_id'] == 1) {
                $source_ids = array_keys(menu('nav'));
            } else {
                $source_ids = $local_menu_children[$menu_data['parent_id']];
            }
            $this->assign->side_menu['menus'] = (array)$source_ids;
        }

        if ($menu_data['template']) {
            $tpls = json_decode(str_replace("'", '"', $menu_data['template']), true);

            if ($tpls[strtolower($this->params['action'])]) {
                $this->fetch = (strpos($tpls[strtolower($this->params['action'])], '/') !== false) ? $tpls[strtolower($this->params['action'])] : $this->params['controller'] . '/' . $tpls[strtolower($this->params['action'])];
            }
        }
        if ($menu_data['keywords']) {
            $this->assign->meta['keywords'] = array($menu_data['keywords']);
        }
        if ($menu_data['description']) {
            $this->assign->meta['description'] = array($menu_data['description']);
        }
        if (isset($options['limit']) && !$this->local['limit']) {
            $this->local['limit'] = $limit;
        }
        if (!$this->local['limit']) {
            $this->local['limit'] = intval(setting('list_count'));
        }
    }

    public function getMenuData($menu_id, $limit = 4, $options = [])
    {
        /*
        $menu_id = intval($menu_id);
        if (!$menu_id || $menu_id <= 0) {
            return [];
        }
        $modelName = menu($menu_id, 'type');
        $limit = intval($limit);
        if (empty($modelName) || $limit < 1) {
            return [];
        }
        $queryModel = $this->loadModel($modelName);

        $where = $options['where'] ? $options['where'] : [];
        if ($options['family']) {
            $where['menu_id'] = ['IN', getClosestMenus($menu_id)];
        } else {
            $where['menu_id'] = $menu_id;
        }
        if ($queryModel->form['is_verify'] && setting('is_verify') && !$where['is_verify']) {
            $where['is_verify'] = 1;
        }
        
        $contain = $options['contain'] ? $options['contain'] : [];

        $field = $options['field'] ? $options['field'] : [$queryModel->getPk(), $queryModel->display];

        if (!empty($contain)) {
            $contain = Hash::normalize($contain);
            foreach ($contain as $assocModel => $assocInfo) {
                if ($queryModel->assoc[$assocModel]['type'] == 'belongsTo') {
                    ##belongsTo 关联，决解如果没有关联字段，将不会自动with关联
                    $foreignKey = $assocInfo['foreignKey'] ? $assocInfo['foreignKey'] : \think\Loader::parseName($assocModel) . '_id';
                    if (!in_array($foreignKey, $field)) {
                        $field[] = $foreignKey;
                    }
                }
            }
        }

        $order = $options['order'] ? $options['order'] : [];
        if (empty($order)) {
            if ($queryModel->form['list_order']) {
                $order['list_order'] = 'DESC';
            }
            $order['id'] = 'DESC';
        }

        $type = $options['type'] && in_array($options['type'], ['find', 'select']) ? $options['type'] : 'select';
        $data = $queryModel->field($field)->with($queryModel->parseWith($contain))->where($where)->order($order)->limit($limit)->$type();
        
        if (!empty($data)) {
            $this->assign->query_data[$menu_id] = $queryModel->getArray($data);
        } else {
            $this->assign->query_data[$menu_id] = [];
        }*/
        $this->assign->query_data[$menu_id] = getMenuData($menu_id, $limit, $options);
        return true;
    }
    
    public function getAdData($vari, $limit = 0)
    { 
        $this->assign->ad = getAdData($vari, $limit) + (array)$this->assign->ad;
        /*
        if (!$this->AdPosition) {
			$this->loadModel('AdPosition');
		}        
        $ad[$vari] = $this->AdPosition->where(['vari' => $vari])->field(array_keys($this->AdPosition->form))->find();
        if ($ad[$vari]) {
            $ad[$vari] = $ad[$vari]->getArray();
            if (!$this->Ad) {
    			$this->loadModel('Ad');
    		}
            if ($limit && $ad[$vari]['limit'] && $limit > $ad[$vari]['limit']) { 
                $limit = $ad[$vari]['limit'];
            } else {
                if ($limit == 0) {
                    $limit = $ad[$vari]['limit'];
                }
            }            
            $ads = $this->Ad->field(array_keys($this->Ad->form))->where(['ad_position_id' => $ad[$vari]['id'], 'is_verify' => 1])->order(['list_order' => 'DESC', 'id' => 'DESC'])->limit($limit)->select();
            if ($ads) {
                $ads = $this->Ad->getArray($ads);
                foreach ($ads as &$each) {
                    if (!$each['thumb']) {
                        $each['thumb'] = $each['image'];
                    }                     
                    if (!$each['mobile_image']) {
                        $each['mobile_image'] = $each['image'];
                    }                    
                    if(!$each['mobile_thumb']) {
                       $each['mobile_thumb']  = $each['mobile_image'];
                    }
                }
            }
            
            //$ad[$vari]['Ad'] = $ads;
            $this->assign->ad = $ad + (array)$this->assign->ad;
        }*/
    }

}
