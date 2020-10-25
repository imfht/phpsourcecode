<?php
namespace app\admin\Controller;

use think\Controller;
use app\admin\Model\AuthRule;
use app\admin\Model\AuthGroup;
use think\Request;
use think\Db;
use think\Config;

class Admin extends Controller
{
    /**
     * 后台基类 控制器
     */
    public $_seo;

    public $is_root;

    public function _initialize()
    {   
        $this->_seo = ['title' => '','setKeywords' => '', 'description' => ''];
        // 判断登陆
        $this->needLogin();
        // 是否是超级管理员
        $this->is_root = is_administrator();
        
        if (!$this->is_root && config('ADMIN_ALLOW_IP')) {
            // 检查IP地址访问
            if (!in_array(request()->ip(), explode(',', config('ADMIN_ALLOW_IP')))) {
                $this->error(lang('_FORBID_403_'));
            }
        }

        // 检测访问权限
        $access = $this->accessControl();
        if ($access === false) {
            $this->error(lang('_FORBID_403_'));
        } elseif ($access === null) {
            $dynamic = $this->checkDynamic();//检测分类栏目有关的各项动态权限
            if ($dynamic === null) {
                //检测非动态权限
                $rule = strtolower(request()->module() . '/' . request()->controller() . '/' . request()->action());
                if (!$this->checkRule($rule, array('in', '1,2'))) {
                    $this->error(lang('_VISIT_NOT_AUTH_'));
                }
            } elseif ($dynamic === false) {
                $this->error(lang('_VISIT_NOT_AUTH_'));
            }
        }
        
        // 获取插件后台管理列表
        $addons_admin = model('admin/Addons')->getAdminList();
        //获取本地版本
        $version = $this->localVersion();
        //获取管理员数据
        $auth_user = query_user(['nickname','username','sex','avatar32','title','fans', 'following','signature'],is_login());
        
        $this->assign('seo', $this->_seo);
        $this->assign('__AUTH_USER__',$auth_user);
        $this->assign('__MANAGE_COULD__',$this->checkRule('admin/module/lists',array('in','1,2')));
        $this->assign('__MENU__', $this->getTreeMenus());
        $this->assign('__ADDONS_MENU__', $addons_admin );
        $this->assign('version',$version);
        $this->checkUpdate();
    }

    public function needLogin(){

        $uid = is_login();
        if (!$uid) {// 还没登录 跳转到登录页面
            $this->redirect('admin/common/login');
        }
        return $uid;
    }

    public function setTitle($title)
    {
        $this->_seo['title'] = $title;
        $this->assign('seo', $this->_seo);
    }

    public function setKeywords($keywords)
    {
        $this->_seo['keywords'] = $keywords;
        $this->assign('seo', $this->_seo);
    }

    public function setDescription($description)
    {
        $this->_seo['description'] = $description;
        $this->assign('seo', $this->_seo);
    }

    /**
     * 权限检测
     * @param string $rule 检测的规则
     * @param string $mode check模式
     * @return boolean
     */
    final protected function checkRule($rule, $type = AuthRule::RULE_URL, $mode = 'url')
    {
        if ($this->is_root) {
            return true;//管理员允许访问任何页面
        }
        static $Auth = null;
        if (!$Auth) {
            $Auth = new \muucmf\Auth();
        }
        if (!$Auth->check($rule, is_login(), $type, $mode)) {
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic()
    {
        if ($this->is_root) {
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     */
    final protected function accessControl()
    {
        if ($this->is_root) {
            return true;//管理员允许访问任何页面
        }
        $allow = config('ALLOW_VISIT');

        $deny = config('DENY_VISIT');
        $check = strtolower(request()->controller() . '/' . request()->action());
        if (!empty($deny) && in_array($check, $deny)) {
            return false;//非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array($check, $allow)) {
            return true;
        }
        return null;//需要检测节点权限
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data 修改的数据
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    final protected function editRow($model, $data, $where, $msg)
    {
        $id = array_unique((array)input('id/a', 0));
        $id = is_array($id) ? implode(',', $id) : $id;

        if($where) {
            $where = $where;
        }else{
            $where = ['id' => array('in', $id)];
        }
        //$where = array_merge([['id' => array('in', $id)], (array)$where);
        $msg = array_merge(array('success' => lang('_OPERATION_SUCCESS_'), 'error' => lang('_OPERATION_FAILED_'), 'url' => '', 'ajax' => request()->isAjax()), (array)$msg);

        if (Db::name($model)->where($where)->update($data) !== false) {
            $this->success($msg['success'], $msg['url'], $msg['ajax']);
        } else {
            $this->error($msg['error'], $msg['url'], $msg['ajax']);
        }
    }

    /**
     * 禁用条目
     * @param string $model 模型名称
     * @param array  $where 查询时的 where()方法的参数
     * @param array  $msg 执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function forbid($model, $where = [], $msg = array('success' => '状态禁用成功！', 'error' => '状态禁用失败！'))
    {
        $data = array('status' => 0);
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 恢复条目
     * @param string $model 模型名称
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     */
    protected function resume($model, $where = [], $msg = array('success' => '状态恢复成功！', 'error' => '状态恢复失败！'))
    {
        $data = ['status' => 1];
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 还原条目
     * @param string $model 模型名称
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function restore($model, $where = [], $msg = ['success' => '状态还原成功！', 'error' => '状态还原失败！'])
    {
        $data = ['status' => 1];
        $where = array_merge(array('status' => -1), $where);
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 条目假删除
     * @param string $model 模型名称
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function delete($model, $where = [], $msg =['success' => '删除成功！', 'error' => '删除失败！'])
    {
        $data['status'] = -1;
        //$data['update_time'] = time();
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 设置一条或者多条数据的状态
     */
    public function setStatus()
    {   
        $ids = input('ids/a');
        $status = input('status');
        if(empty($Model)) $Model = request()->controller();
        if (empty($ids)) {
            $this->error(lang('_PLEASE_CHOOSE_THE_DATA_TO_BE_OPERATED_'));
        }

        $map['id'] = array('in', $ids);
        switch ($status) {
            case -1 :
                $this->delete($Model, $map, array('success' => lang('_DELETE_SUCCESS_EXCLAMATION_'), 'error' => lang('_DELETE_FAILED_EXCLAMATION_')));
                break;
            case 0  :
                $this->forbid($Model, $map, array('success' => lang('_DISABLE_SUCCESS_'), 'error' => lang('_DISABLE_FAIL_')));
                break;
            case 1  :
                $this->resume($Model, $map, array('success' => lang('_ENABLE_SUCCESS_'), 'error' => lang('_ENABLE_FAILED_')));
                break;
            default :
                $this->error(lang('_PARAMETER_ERROR_'));
                break;
        }
    }

    /**
     * 获取模块列表，用于显示在左侧
     */
    public function getModules()
    {
        $modules=collection(model('Module')->getAll())->toArray();
        foreach($modules as $key=> &$v){
            $rule = strtolower($v['admin_entry']);
        }
        return $modules;
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于'_child'子元素中
     */
    final public function getTreeMenus()
    {   
        
        if (empty($menus)) {
            // 获取主菜单
            $where['pid'] = '0';
            //$where['hide'] = 0;
            if (!config('DEVELOP_MODE')) { // 是否开发者模式
                $where['is_dev'] = 0;
            }
            $menus = Db::name('Menu')->where($where)->order('sort asc')->select();
            //dump($menus);exit;

            foreach ($menus as $key => $item) {

                $menus[$key]['_child'] = [];

                if (!is_array($item) || empty($item['title']) || empty($item['url'])) {
                        $this->error(lang('_CLASS_CONTROLLER_ERROR_PARAM_',array('menus'=>$menus)));
                    }
                    if (stripos($item['url'], request()->module()) !== 0) {
                        $item['url'] = request()->module() . '/' . $item['url'];
                    }
                    // 判断主菜单权限
                    if (!$this->is_root && !$this->checkRule($item['url'], AuthRuleModel::RULE_MAIN, null)) {
                        unset($menus['main'][$key]);
                        continue;//继续循环
                    }
    
                    //生成child树
                    $groups = Db::name('Menu')->where("pid = '{$item['id']}'")->distinct(true)->field("`group`")->order('sort asc')->select();

                    if ($groups) {
                        $groups = array_column($groups, 'group');
                    } else {
                        $groups = array();
                    }

                    //获取二级分类的合法url
                    $where = array();
                    $where['pid'] = $item['id'];
                    $where['hide'] = 0;
                    if (!config('DEVELOP_MODE')) { // 是否开发者模式
                        $where['is_dev'] = 0;
                    }
                    $second_urls = Db::name('Menu')->where($where)->field('id,url')->select();

                    if (!$this->is_root) {
                        // 检测菜单权限
                        $to_check_urls = array();
                        foreach ($second_urls as $key => $to_check_url) {
                            if (stripos($to_check_url, request()->module()) !== 0) {
                                $rule = request()->module() . '/' . $to_check_url;
                            } else {
                                $rule = $to_check_url;
                            }
                            if ($this->checkRule($rule, AuthRuleModel::RULE_URL, null))
                                $to_check_urls[] = $to_check_url;
                        }
                    }
                    // 按照分组生成子菜单树
                    foreach ($groups as $g) {
                        $map = array('group' => $g);
                        if (isset($to_check_urls)) {
                            if (empty($to_check_urls)) {
                                // 没有任何权限
                                continue;
                            } else {
                                $map['url'] = array('in', $to_check_urls);
                            }
                        }
                        $map['pid'] = $item['id'];
                        $map['hide'] = 0;
                        if (!config('DEVELOP_MODE')) { // 是否开发者模式
                            $map['is_dev'] = 0;
                        }
                        $menuList = Db::name('Menu')->where($map)->field('id,pid,title,url,icon,tip')->order('sort asc')->select();

                        
                        $menus[$key]['_child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                    }
                }
            }

        return $menus;
    }


    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     */
    final public function getMenus($controller=null)
    {   
        if(empty($controller)) $controller = request()->controller();
        $menus  =   session('ADMIN_MENU_LIST'.$controller);
        if (empty($menus)) {
            // 获取主菜单
            $where['pid'] = '0';
            //$where['hide'] = 0;
            if (!config('DEVELOP_MODE')) { // 是否开发者模式
                $where['is_dev'] = 0;
            }
            $menus['main'] = Db::name('Menu')->where($where)->order('sort asc')->select();


            $menus['child'] = array(); //设置子节点

            //高亮主菜单
            $current = Db::name('Menu')->where("url like '{$controller}/" . request()->action() . "%' OR url like '%/{$controller}/" . request()->action() . "%'  ")->field('id')->find();
            
            if ($current) {
                $nav = model('Menu')->getPath($current['id']);
                $nav_first_title = $nav[0]['title'];

                echo $nav_first_title;
                foreach ($menus['main'] as $key => $item) {
                    if (!is_array($item) || empty($item['title']) || empty($item['url'])) {
                        $this->error(lang('_CLASS_CONTROLLER_ERROR_PARAM_',array('menus'=>$menus)));
                    }
                    if (stripos($item['url'], request()->module()) !== 0) {
                        $item['url'] = request()->module() . '/' . $item['url'];
                    }
                    // 判断主菜单权限
                    if (!$this->is_root && !$this->checkRule($item['url'], AuthRuleModel::RULE_MAIN, null)) {
                        unset($menus['main'][$key]);
                        continue;//继续循环
                    }

                    // 获取当前主菜单的子菜单项
                    if ($item['title'] == $nav_first_title) {

                        $menus['main'][$key]['class'] = 'active';
                        //生成child树
                        $groups = Db::name('Menu')->where("pid = '{$item['id']}'")->distinct(true)->field("`group`")->order('sort asc')->select();

                        if ($groups) {
                            $groups = array_column($groups, 'group');
                        } else {
                            $groups = array();
                        }

                        //获取二级分类的合法url
                        $where = array();
                        $where['pid'] = $item['id'];
                        $where['hide'] = 0;
                        if (!config('DEVELOP_MODE')) { // 是否开发者模式
                            $where['is_dev'] = 0;
                        }
                        $second_urls = Db::name('Menu')->where($where)->field('id,url')->select();

                        if (!$this->is_root) {
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key => $to_check_url) {
                                if (stripos($to_check_url, request()->module()) !== 0) {
                                    $rule = request()->module() . '/' . $to_check_url;
                                } else {
                                    $rule = $to_check_url;
                                }
                                if ($this->checkRule($rule, AuthRuleModel::RULE_URL, null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group' => $g);
                            if (isset($to_check_urls)) {
                                if (empty($to_check_urls)) {
                                    // 没有任何权限
                                    continue;
                                } else {
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid'] = $item['id'];
                            $map['hide'] = 0;
                            if (!config('DEVELOP_MODE')) { // 是否开发者模式
                                $map['is_dev'] = 0;
                            }
                            $menuList = Db::name('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();

                            
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                        dump($menus);
                    }
                }
            }
        }

        return $menus;
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree 是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes($tree = true)
    {
        header("Content-Type: text/html;charset=utf-8"); 
        static $tree_nodes = array();
        if ($tree && !empty($tree_nodes[(int)$tree])) {
            return $tree_nodes[$tree];
        }
        if ((int)$tree) {
            $list = Db::name('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if (stripos($value['url'], request()->module()) !== 0) {
                    $list[$key]['url'] = request()->module() . '/' . $value['url'];
                }
            }
            //由于menu表id更改为字符串格式，root必须设置成字符串0
            $nodes = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'operator', $root = '0');
            foreach ($nodes as $key => $value) {
                if (!empty($value['operator'])) {
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }

        } else {
            $nodes = Db::name('Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if (stripos($value['url'], request()->module()) !== 0) {
                    $nodes[$key]['url'] = request()->module() . '/' . $value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree] = $nodes;
        return $nodes;
    }

    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  userList.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: userList.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: userList.html?r=5
     *
     * @param sting|Model  $model 模型名或模型实例
     * @param array        $where where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order 排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base 基本的查询条件
     * @param boolean      $field 单表模型用不到该参数,要用在多表join时为field()方法指定参数
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists($model, $where = [], $order = '', $base = ['status' => ['egt', 0]], $field = true)
    {
        $options = [];
        $REQUEST = (array)input('request.');
        if (is_string($model)) {
            $model = Db::name($model);
        }

        $pk = $model->getPk();
        if ($order === null) {
            //order置空
        } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), ['desc', 'asc'])) {
            $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk . ' desc';
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);

        $options['where'] = array_filter(array_merge((array)$base, /*$REQUEST,*/
            (array)$where), function ($val) {
            
            if ( $val === null) {
                return false;
            } else {
                return true;
            }
        });
        if (empty($options['where'])) {
            unset($options['where']);
        }

        //$total = $model->where($options['where'])->count();

        if (input('r')!==null) {
            $listRows = (int)input('r');
        } else {
            $listRows = 20;
        }

        //获取列表
        $list = $model->where($options['where'])->order($options['order'])->paginate($listRows);
        // 获取分页显示
        $page = $list->render();
        // 模板变量赋值
        $this->assign('list', $list);
        $this->assign('page', $page);
        return [$list,$page];
    }

    public function  _empty(){
        $this->error(lang('_ERROR_404_2_'));
    }

    protected function checkUpdate()
    {
        if (config('AUTO_UPDATE')) {
            $can_update = 1;
        } else {
            $can_update = 0;
        }
        $this->assign('can_update', $can_update);
    }

    /**
     * 本地版本号
     * @return [type] [description]
     */
    private function localVersion()
    {
        return config('muucmf.version');
    }
}
