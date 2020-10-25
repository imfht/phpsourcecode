<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Verify;
/**
 * 后台公共统一控制器
 * @author jlb <[<email address>]>
 * @since 2016年12月7日09:57:28 
 */
class CommonController extends Controller
{
    //登录者id
    protected $admin_id;

    //登录者角色id
    protected $role_id;

    //登陆者权限菜单列表
    protected $privilegeList;


    public function __construct()
    {
    	parent::__construct();

        //session初始化
        session(array('name'=>'session_id','expire'=>3600,'prefix'=>SESSION_PREFIX));

        if ( session('?adminInfo.admin_id') ) {
            $this->admin_id = session('adminInfo.admin_id');
            $this->role_id = session('adminInfo.role_id');
            $this->savePrivilege();
        }

    }

    /**
     * 赋予权限权限
     * @return [type] [description]
     */
    protected function savePrivilege()
    {
        //权限菜单列表
        $privilegeList = [];

        //超级管理员则显示所有菜单
        if ( $this->role_id == 1 ) 
        {
            $privilegeList = M('Menu')->order("step asc")->select();
        }
        else 
        { 
            //取对应角色的权限
            $menuToRoleList = M('RoleMenu')->where(array('role_id' => $this->role_id))->select();
            if ( !empty($menuToRoleList) ) 
            {
                $menuIds = implode(',', array_column($menuToRoleList, 'menu_id'));
                $privilegeList = M('Menu')->where("menu_id in (".$menuIds.")")->select();
            }

        }

        //取出用户对应的角色,赋予权限
        session('adminInfo.privilegeList', $privilegeList);

        $this->privilegeList = $privilegeList;
    }


    /**
     *  验证码获取
     * @return void
     * @author Gison
     * @since 2016年10月27日
     */
    public function verify()
    {
        $config = array(
            'fontSize' => 30,
            'length' => 4,
            'useCurve' => true,
            'useNoise' => false,
        );
        $verify = new Verify($config);
        $verify->entry();
    }

}