<?php
namespace wstmart\admin\model;
use think\Db;

/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 店铺风格业务处理
 */
class ShopStyles extends Base{
    /**
     * 获取分类
     */
    public function getCats(){
        return $this->distinct(true)->field('styleSys')->select();
    }
    /**
     * 获取风格列表
     */
    public function listQuery(){
        $styleSys = input('styleSys');
        $styleCat = input('styleCat',-1);
        $where = [];
        $where[] = ['styleSys','=',$styleSys];
        if($styleCat > -1){
            $where[] = ['styleCat','=',$styleCat];
        }
        $rs = $this->where($where)->paginate(14,false,['page'=>input('p/d')])->toArray();

        if(count($rs['data'])>0) {
            foreach ($rs['data'] as $k => $v) {
                if (strpos($v['stylePath'], '/') == false) {
                    $rs['data'][$k]['styleImgPath'] = 'default';
                } else {
                    $stylePath = explode("/", $v["stylePath"]);
                    unset($stylePath[count($stylePath) - 1]);
                    $stylePath = implode("/", $stylePath);
                    $rs['data'][$k]['styleImgPath'] = 'default' . DS . $stylePath;
                }
            }
        }
        $cats = WSTDatas('SHOPSTYLES_CAT');
        return ['sys'=>$styleSys,'list'=>$rs,'cats'=>$cats,'cat'=>$styleCat];
    }
    /**
     * 初始化风格列表
     */
    public function initStyles(){
        $styles = $this->field('styleSys,stylePath,id,isShow')->select();
        $sys = [];
        foreach ($styles as $key => $v) {
            $sys[$v['styleSys']][$v['stylePath']] = 1;
            if($v['stylePath'] != "shop_home" && $v['stylePath'] != "self_shop"){
                $stylePath = explode("/",$v["stylePath"]);
                unset($stylePath[count($stylePath)-1]);
                $stylePath = 'default' . DS . implode("/",$stylePath);
                if($v['styleSys'] != 'weapp' && $v['styleSys'] != 'app'){
                    //删除不存在的风格记录
                    if(!is_dir(WSTRootPath(). DS .'wstmart'. DS .$v['styleSys']. DS .'view'. DS.$stylePath)){
                        $this->where('id',$v['id'])->delete();
                    }
                }

            }
        }
        Db::startTrans();
        try{
            //添加不存在的风格目录
            $prefix = config('database.prefix');
            foreach ($sys as $key => $v) {
                $dirs = array_map('basename',glob(WSTRootPath(). DS .'wstmart'.DS.$key.DS.'view/default/theme/shop'.DS.'*', GLOB_ONLYDIR));
                foreach ($dirs as $dkey => $dv) {
                    $path = "theme/shop/".$dv."/shop_home";
                    if(!isset($v[$path])){
                        $sqlPath = WSTRootPath(). DS .'wstmart'. DS .$key. DS .'view/default/theme/shop'. DS .$dv. DS.'sql'.DS.'install.sql';// sql路径
                        $hasFile = file_exists($sqlPath);
                        if(!$hasFile)continue;
                        $sql = file_get_contents($sqlPath);
                        $this->excute($sql,$prefix);
                    }
                }
            }
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
        }
    }

    /**
     * 编辑
     */
    public function changeStyleShow(){
        $id = (int)input('post.id');
        $isShow = (int)input('post.isShow');
        $rs = $this->where("id","=",$id)->update(["isShow"=>$isShow]);
        if($rs !== false){
            return WSTReturn('操作成功',1);
        }else{
            return WSTReturn("操作失败");
        }
    }

    /**
     * 执行sql
     */
    private function excute($sql,$db_prefix=''){
        if(!isset($sql) || empty($sql)) return;
        $sql = str_replace("\r", " ", str_replace('`wst_', '`'.$db_prefix, $sql));// 替换表前缀
        $ret = array();
        foreach(explode(";\n", trim($sql)) as $query) {
            Db::execute(trim($query));
        }
    }

    /*
     * 设置店铺风格的分类
     */
    public function changeStyleCat(){
        $id = (int)input('post.id');
        $styleCat = (int)input('post.styleCat');
        $rs = $this->where("id","=",$id)->update(["styleCat"=>$styleCat]);
        if($rs !== false){
            return WSTReturn('操作成功',1);
        }else{
            return WSTReturn("操作失败");
        }
    }
}
