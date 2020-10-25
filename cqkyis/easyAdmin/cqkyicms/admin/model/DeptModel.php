<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/10 11:49
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\model;


use app\admin\validate\DeptValidate;
use think\Model;

class DeptModel extends Model
{

    protected  $name="system_dept";

    /**
     * 列表
     */

  public function listAll(){
      return $this->order('orderby asc')->select();
  }

    /*
     * 添加
     */
    public function add($data){
        try {
            $validate = new DeptValidate();
            if (!$validate->check($data)) {
                return easymsg(2,'',$validate->getError());
            }
            $this->save($data);
            return easymsg(1,url('dept/index'),'添加部门成功');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }


    /**
     * 根据ID查询
     */

    public function findById($id){
        return $this->field('dept_id,dept_name')->where('dept_id',$id)->find();
    }

    /**
     * 生成树String形式
     * 后面更新用数组型
     */

    public function bulidTree(){
        $tree='{"id":"0","icon":null,"url":null,"text":"全部部门","state":{"opened":true},"checked":true,"attributes":null,"children":[';
        $pmenu = $this->where('parent_id',0)->select();
        //如果有是顶级菜单有值
        if($pmenu){
            foreach ($pmenu as $k=>$v){
                $tree.='{"id":"'.$v['dept_id'].'","icon":null,"url":null,"text":"'.$v['dept_name'].'","checked":false,"attributes":null,"children":[';
                $smenu = $this->where('parent_id',$v['dept_id'])->select();
                if($smenu){
                    foreach ($smenu as $k1=>$v1){
                        $tree.='{"id":"'.$v1['dept_id'].'","icon":null,"url":null,"text":"'.$v1['dept_name'].'","checked":false,"attributes":null,"children":[';
                        $tmenu= $this->where('parent_id',$v1['dept_id'])->select();


                        if($tmenu){
                            foreach ($tmenu as $k2=>$v2){
                                $tree.='{"id":"'.$v2['dept_id'].'","icon":null,"url":null,"text":"'.$v2['dept_name'].'","checked":false,"attributes":null},';
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