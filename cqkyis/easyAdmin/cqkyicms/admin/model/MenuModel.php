<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/7 13:35
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\model;


use service\AuthService;
use service\LogService;
use think\Model;

class MenuModel extends Model
{
    protected $name = 'system_menu';

    public function checkUserMenu($param){
         $auth = new AuthService();
         $list = $this->where('parent_id=0')->order('orderby asc')->select();

         foreach ($list as $k=>$v) {

            if (!$auth->check($v['menu_role'], $param) && $param != 1) {


                unset($list[$k]);

            }else{
                $list[$k]['sub'] = $this->where('parent_id=' . $v['menu_id'] )->order('orderby asc')->select();
            }

        }
          return $list;
    }

    /*
     * 查询所有的菜单
     */
    public function listMenuAll(){
        return $this->order('orderby asc')->select();
    }

    /**
     * 添加系统菜单
     */
    public function add($data){
        try {
            $this->save($data);
            return easymsg(1,url('menu/index'),'添加菜单成功');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }

    }

   public function del($id){
       try {
           $parent=$this->where('parent_id',$id)->find();
           if($parent){
               return easymsg(-2,'','该菜单下还有子菜单，请先删除子菜单再进行相关操作！');
           }
           $this->where('menu_id',$id)->delete();
           return easymsg(1,url('menu/index'),'删除成功！');
       }catch(PDOException $e){
           return easymsg(-1,'',$e->getMessage());
       }
   }

   public function edit($data){
       try {
           $this->save($data, ['menu_id' => $data['menu_id']]);
           return easymsg(1,url('menu/index'),'修改成功！');
       }catch(PDOException $e){
           return easymsg(-1,'',$e->getMessage());
       }
   }

    /**
     * 查询菜单根据ID
     */
    public function findByMenuId($id){
        return $this->field('menu_id,menu_name')->where('menu_id',$id)->find();
    }

    /*
     * 查询上级菜单
     */
    public function findByParentId($id){
        return $this->field('menu_id,menu_name')->where('parent_id',$id)->find();
    }

    /**
     * 查询所有字段
     */
    public function findByMenuAll($id){
        return $this->where('menu_id',$id)->find();
    }

    /*
     * 查询菜单根据权限标识
     */
    public function findByMenuRole($role){
        return $this->field('menu_id,parent_id')->where('menu_role',$role)->find();
    }

    /**
     * 生成树String形式
     * 后面更新用数组型
     */

    public function bulidTree(){
        $tree='{"id":"0","icon":null,"url":null,"text":"全部权限","state":{"opened":true},"checked":true,"attributes":null,"children":[';
        $pmenu = $this->where('parent_id',0)->select();
        //如果有是顶级菜单有值
        if($pmenu){
            foreach ($pmenu as $k=>$v){
                $tree.='{"id":"'.$v['menu_id'].'","icon":"'.$v['menu_icon'].'","url":null,"text":"'.$v['menu_name'].'","checked":false,"attributes":null,"children":[';
                $smenu = $this->where('parent_id',$v['menu_id'])->select();
                if($smenu){
                    foreach ($smenu as $k1=>$v1){
                        $tree.='{"id":"'.$v1['menu_id'].'","icon":"'.$v1['menu_icon'].'","url":null,"text":"'.$v1['menu_name'].'","checked":false,"attributes":null,"children":[';
                        $tmenu= $this->where('parent_id',$v1['menu_id'])->select();


                        if($tmenu){
                            foreach ($tmenu as $k2=>$v2){
                                $tree.='{"id":"'.$v2['menu_id'].'","icon":"'.$v2['menu_icon'].'","url":null,"text":"'.$v2['menu_name'].'","checked":false,"attributes":null},';
                            }

                        }
                        $tree.=']},';
                    }
                }
                $tree.=']},';
            }
            $tree.=']}';
        }
        return $tree;
    }



    public function editbulidTree($id){
        $role = new RoleModel();
        $list = $role->findById($id);

        $tree='{"id":"0","icon":null,"url":null,"text":"全部权限","state":{"opened":true},"checked":true,"attributes":null,"children":[';
        $pmenu = $this->where('parent_id',0)->select();
        //如果有是顶级菜单有值
        if($pmenu){
            foreach ($pmenu as $k=>$v){
                $tree.='{"id":"'.$v['menu_id'].'","icon":"'.$v['menu_icon'].'","url":null,"text":"'.$v['menu_name'].'","checked":false,"attributes":null,';
                if(strstr($list['rules'],(string)$v['menu_id'])!==false){
                    $tree.='"state":{"selected":true},';
                }

                $tree.='"children":[';
                $smenu = $this->where('parent_id',$v['menu_id'])->select();
                if($smenu){
                    foreach ($smenu as $k1=>$v1){
                        $tree.='{"id":"'.$v1['menu_id'].'","icon":"'.$v1['menu_icon'].'","url":null,"text":"'.$v1['menu_name'].'","checked":false,"attributes":null,';
                        if(strpos($list['rules'],(string)$v1['menu_id'])!==false){
                            $tree.='"state":{"selected":true},';
                        }
                        $tree.='"children":[';

                        $tmenu=$this->where('parent_id',$v1['menu_id'])->select();

                        if($tmenu){
                            foreach ($tmenu as $k2=>$v2){
                                $tree.='{"id":"'.$v2['menu_id'].'","icon":"'.$v2['menu_icon'].'","url":null,"text":"'.$v2['menu_name'].'","checked":false,"attributes":null,';
                                if(strpos($list['rules'],(string)$v2['menu_id'])!==false){
                                    $tree.='"state":{"selected":true},';
                                }
                                $tree.='},';
                            }

                        }
                        $tree.=']},';
                    }
                }
                $tree.=']},';
            }
            $tree.=']}';
        }
        return $tree;
    }

}