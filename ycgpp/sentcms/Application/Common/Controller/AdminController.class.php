<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Common\Controller;
use Admin\Model\AuthRuleModel;

class AdminController extends BaseController {

	public $meta_title;

	protected function _initialize(){
		parent::_initialize();
		// 获取当前用户ID
		if(defined('UID')) return ;
		define('UID',is_login());
		if( !UID ){
			// 还没登录 跳转到登录页面
			$this->redirect('Public/login');
		}

		// 是否是超级管理员
		define('IS_ROOT',   is_administrator());
		if(!IS_ROOT && C('ADMIN_ALLOW_IP')){
			// 检查IP地址访问
			if(!in_array(get_client_ip(),explode(',',C('ADMIN_ALLOW_IP')))){
				$this->error('403:禁止访问');
			}
		}

		// 检测系统权限
		if(!IS_ROOT){
			$access =   $this->accessControl();
			if ( false === $access ) {
				$this->error('403:禁止访问');
			}elseif(null === $access ){
				//检测访问权限
				$rule  = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
				if ( !$this->checkRule($rule,array('in','1,2')) ){
					$this->error('未授权访问!');
				}else{
					// 检测分类及内容有关的各项动态权限
					$dynamic    =   $this->checkDynamic();
					if( false === $dynamic ){
						$this->error('未授权访问!');
					}
				}
			}
		}

		//检测用户是否被删除或者假登录
		$this->is_del();

		//获得后台菜单
		$this->assign('Menu', $this->getMenu());
	}

	/**
	 * 检测用户是否被删除或者假登录
	 * @author colin <colin@tensent.cn>
	 */
	protected function is_del(){
		$find = D('Member')->where(array('uid'=>session('user_auth.uid')))->find();
		if(!$find){
			session('user_auth',null);
			$this->error('您的账户存在异常！请重新登录！',U('Public/login'));
		}
	}

	/**
	* 权限检测
	* @param string  $rule    检测的规则
	* @param string  $mode    check模式
	* @return boolean
	* @author 朱亚杰  <xcoolcc@gmail.com>
	*/
	final protected function checkRule($rule, $type=AuthRuleModel::RULE_URL, $mode='url'){
		static $Auth    =   null;
		if (!$Auth) {
			$Auth       =   new \Think\Auth();
		}
		if(!$Auth->check($rule,UID,$type,$mode)){
			return false;
		}
		return true;
	}

	/**
	 * 检测是否是需要动态判断的权限
	 * @return boolean|null
	 *      返回true则表示当前访问有权限
	 *      返回false则表示当前访问无权限
	 *      返回null，则表示权限不明
	 *
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	protected function checkDynamic(){}

	/**
	 * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
	 *
	 * @return boolean|null  返回值必须使用 `===` 进行判断
	 *
	 *   返回 **false**, 不允许任何人访问(超管除外)
	 *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
	 *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	final protected function accessControl(){
		$allow = C('ALLOW_VISIT');
		$deny  = C('DENY_VISIT');
		$check = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);
		if ( !empty($deny)  && in_array_case($check,$deny) ) {
			return false;//非超管禁止访问deny中的方法
		}
		if ( !empty($allow) && in_array_case($check,$allow) ) {
			return true;
		}
		return null;//需要检测节点权限
	}

	protected function getMenu(){
		// 获取主菜单
		$where['hide']  =   0;
		$where['type']  =   'admin';
		if(!C('DEVELOP_MODE')){ // 是否开发者模式
			$where['is_dev']    =   0;
		}
		$menus  =   M('Menu')->index('id')->where($where)->order('sort asc')->field('id,pid,title,url,icon,group')->select();

		foreach ($menus as $key => $item) {
			if ($item['url']) {
				// 判断主菜单权限
				if ( !IS_ROOT && !$this->checkRule(strtolower(MODULE_NAME.'/'.$item['url']),AuthRuleModel::RULE_MAIN,null) ) {
					unset($menus[$key]);
					continue;//继续循环
				}
				if(strtolower(CONTROLLER_NAME.'/'.ACTION_NAME)  == strtolower($item['url'])){
					if ($item['pid']) {
						$menus[$item['pid']]['class'] = 'active';
					}
					$menus[$key]['class'] = 'active';
				}
			}
		}
		$menus = list_to_tree($menus);
		foreach ($menus as $key => $value) {
			if (!empty($value['_child']) || $value['url']) {
				$data[$value['group']][] = $value;
			}
		}
		return $data;
	}

	//内容模型左侧菜单
	public function ContentMenu(){
		$map['status'] = 1;
		$map['extend'] = 0;
		$map['list_grid'] = array('neq','');
		$order = "";
		$menu = D('Model')->where($map)->order($order)->select();
		foreach ($menu as $key => $value) {
			$_extra_menu[$value['title'].'管理'][] = array('title'=>$value['title'].'列表','url'=>'Content/index?model='.$value['name']);
			$_extra_menu[$value['title'].'管理'][] = array('title'=>$value['title'].'添加','url'=>'Content/add?model='.$value['name']);
		}
		$this->assign('_extra_menu',$_extra_menu);
	}

	/**
	 * 返回后台节点数据
	 * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
	 * @retrun array
	 *
	 * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
	 *
	 * @author 朱亚杰 <xcoolcc@gmail.com>
	 */
	final protected function returnNodes($tree = true){
		static $tree_nodes = array();
		if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
			return $tree_nodes[$tree];
		}
		if((int)$tree){
			$list = M('Menu')->field('id,pid,title,url,tip,hide,group')->order('sort asc')->select();
			foreach ($list as $key => $value) {
			if( stripos($value['url'],MODULE_NAME)!==0 ){
				$list[$key]['url'] = MODULE_NAME.'/'.$value['url'];
			}
		}
		$nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
		foreach ($nodes as $key => $value) {
			if(!empty($value['operator'])){
				$nodes[$key]['child'] = $value['operator'];
				unset($nodes[$key]['operator']);
			}
		}
		}else{
			$nodes = M('Menu')->index('id')->field('id,title,url,tip,pid,group')->order('sort asc')->select();

			foreach ($nodes as $key => $value) {
				if ($value['url']) {
					if (!$value['group']) {
						$nodes[$key]['group'] = $nodes[$value['pid']]['group'];
					}
					if( stripos($value['url'],MODULE_NAME)!==0 ){
						$nodes[$key]['url'] = MODULE_NAME.'/'.$value['url'];
					}
				}else{
					unset($nodes[$key]);
				}
			}
		}
		$tree_nodes[(int)$tree]   = $nodes;
		return $nodes;
	}


    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $get = (array)I('get.');
        $post = (array)I('post.');
        $REQUEST    =   array_merge($get,$post);
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        return $model->field($field)->select();
    }
    
    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    final protected function editRow ( $model ,$data, $where , $msg ){
        $id    = array_unique((array)I('id',0));
        $id    = is_array($id) ? implode(',',$id) : $id;
        //如存在id字段，则加入该条件
        $fields = M($model)->getDbFields();
        if(in_array('id',$fields) && !empty($id)){
            $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
        }

        $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
        if( M($model)->where($where)->save($data)!==false ) {
            $this->success($msg['success'],$msg['url'],$msg['ajax']);
        }else{
            $this->error($msg['error'],$msg['url'],$msg['ajax']);
        }
    }

    protected function setMeta($title){
    	$this->assign('meta_title',$title);
    }
}