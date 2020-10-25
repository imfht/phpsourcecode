<?php

namespace Admin\Controller;
use Think\Controller;

/**
 *后台首页控制器
 */
class AdminController extends Controller {
	/**
     * 后台控制器初始化
     */
    protected function _initialize(){
    	// 获取当前用户ID
        if(defined('UID')) return ;
        define('UID',is_login());
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('Public/login');
        }
        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置


        //一级菜单显示
        $this->assign('__MENU__', $this->getMenus());
    }


    /**
     * 获取控制器菜单数组
     */
    final public function getMenus($controller=CONTROLLER_NAME){
        $menus  =   session('ADMIN_MENU_LIST.'.$controller);
        if(empty($menus)){
            //查询条件
            $where['parentId']   =   0;
            $menus['main']  =   M('menu','u_')->where($where)->order('menuIndex asc')->field('id,menuName,menuUrl')->select();

            $menus['child'] =   array(); //设置子节点
            foreach ($menus['main'] as $key => $item) {
                if(strtolower(CONTROLLER_NAME.'/'.ACTION_NAME)  == strtolower($item['menuUrl'])){
                    $menus['main'][$key]['class']='current';
                }
            }

            // 查找当前子菜单
            $pid = M('Menu','u_')->where("menuIndex !=0 AND menuUrl like '%{$controller}%'")->getField('id');

            if($pid){
                // 查找当前主菜单
                $nav =  M('Menu','u_')->find($pid);
                if($nav['parentId']){
                    $nav    =   M('Menu','u_')->find($nav['parentId']);
                }
                foreach ($menus['main'] as $key => $item) {
                    // 获取当前主菜单的子菜单项
                    if($item['id'] == $nav['id']){
                        $menus['main'][$key]['class']='current';
                        
                        // 按照分组生成子菜单树
                        $map['parentId']     =   $item['id'];

                        $menuList = M('Menu','u_')->where($map)->field('id,parentId,menuName,menuUrl')->order('menuIndex asc')->select();
                        $menus['child'][$item['menuName']] = list_to_tree($menuList, 'id', 'parentId', 'operater', $item['id']);
                    }
                }
            }

            session('ADMIN_MENU_LIST.'.$controller,$menus);
        }
        return $menus;
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
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$tablePrefix,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model,$tablePrefix);
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

        $result = $model->field($field)->select();
        return $result;
    }
}