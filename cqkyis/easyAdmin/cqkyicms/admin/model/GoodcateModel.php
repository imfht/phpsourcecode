<?php

namespace app\admin\model;

use app\admin\validate\GoodcateValidate;
use think\Model;

class GoodcateModel extends Model
{
    //
    protected $name="good_cate";


    public function listAll(){
        return $this->order('orderby asc')->select();
    }


    public function add($data){
        try {
            $validate  = new GoodcateValidate();
            if (!$validate->check($data)) {
                return easymsg(2,'',$validate->getError());
            }
            $this->save($data);
            return easymsg(1,url('goodcate/index'),'添加产品分类成功');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }

    }

    public function findById($id){
        return $this->field('cate_id,cate_name')->where('cate_id',$id)->find();
    }


    public function edit($data){
        try {

            $this->save($data, ['cate_id' => $data['cate_id']]);
            return easymsg(1,url('goodcate/index'),'修改成功！');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }

    public function findByIdAll($id){
        return $this->where('cate_id',$id)->find();
    }

    /**
     * 根据上级ID查询
     */
    public function findByParentId($id){
        return $this->where('parent_id',$id)->find();
    }


    public function del($id){
        try {
            $parent=$this->where('parent_id',$id)->find();
            if($parent){
                return easymsg(-2,'','该菜单下还有子分类，请先删除子分类再进行相关操作！');
            }
            $this->where('cate_id',$id)->delete();
            return easymsg(1,url('goodcate/index'),'删除成功！');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }


    public function bulidTree(){
        $tree='{"id":"0","icon":null,"url":null,"text":"全部分类","state":{"opened":true},"checked":true,"attributes":null,"children":[';
        $pmenu = $this->where('parent_id',0)->select();
        //如果有是顶级菜单有值
        if($pmenu){
            foreach ($pmenu as $k=>$v){
                $tree.='{"id":"'.$v['cate_id'].'","icon":null,"url":null,"text":"'.$v['cate_name'].'","checked":false,"attributes":null,"children":[';
                $smenu = $this->where('parent_id',$v['cate_id'])->select();
                if($smenu){
                    foreach ($smenu as $k1=>$v1){
                        $tree.='{"id":"'.$v1['cate_id'].'","icon":null,"url":null,"text":"'.$v1['cate_name'].'","checked":false,"attributes":null,"children":[';
                        $tmenu= $this->where('parent_id',$v1['cate_id'])->select();


                        if($tmenu){
                            foreach ($tmenu as $k2=>$v2){
                                $tree.='{"id":"'.$v2['cate_id'].'","icon":null,"url":null,"text":"'.$v2['cate_name'].'","checked":false,"attributes":null},';
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
