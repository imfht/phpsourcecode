<?php
namespace wstmart\shop\model;
use wstmart\shop\validate\BrandApplys as validate;
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
 * 品牌申请业务处理类
 */
class BrandApplys extends Base{
	protected $pk = 'applyId';
    /**
     * 分页
     */
    public function pageQuery(){
        $key = input('key');
        $isNew = input('isNew');
        $where[] = ['b.dataFlag','=',1];
        $where[] = ['b.isNew','=',$isNew];
        $where[] = ['b.shopId','=',(int)session('WST_USER.shopId')];
        if($key!='')$where[] = ['b.brandName','like','%'.$key.'%'];
        $page = Db::name('brand_applys')->alias('b')->where($where)
            ->field('b.applyId,b.brandId,b.brandName,b.brandImg,b.brandDesc,b.applyStatus')
            ->order('b.applyId', 'desc')
            ->paginate(input('post.limit/d'))->toArray();
        if(count($page['data'])>0){
            foreach ($page['data'] as $key => $v){
                $page['data'][$key]['brandDesc'] = strip_tags(htmlspecialchars_decode($v['brandDesc']));
                $page['data'][$key]['applyStatusName'] = WSTBrandApplyStatus($v['applyStatus']);
            }
        }
        return $page;
    }

    /**
     * 获取品牌下的商家分页
     */
    public function shopPageQuery(){
        $key = input('key');
        $brandId = input('brandId');
        $where[] = ['b.dataFlag','=',1];
        $where[] = ['b.applyStatus','=',1];
        $where[] = ['b.isNew','=',0];
        $where[] = ['b.brandId','=',$brandId];
        if($key!='')$where[] = ['s.shopName','like','%'.$key.'%'];
        $page = Db::name('brand_applys')->alias('b')
            ->join('__SHOPS__ s','b.shopId = s.shopId','left')
            ->where($where)
            ->field('b.applyId,b.brandId,b.createTime,s.shopName')
            ->order('b.applyId', 'asc')
            ->paginate(input('post.limit/d'))->toArray();
        return $page;
    }

    /**
     * 获取指定对象
     */
    public function getById($id){
        $result = $this->where(['applyId'=>$id])->find();
        //获取关联的分类
        $result['catIds'] = explode(',',$result['catIds']);
        return $result;
    }

    /**
     * 新增
     */
    public function add(){
        $data = input('post.');
        WSTUnset($data,'dataFlag');
        $data['createTime'] = date('Y-m-d H:i:s');
        $data['shopId'] = (int)session('WST_USER.shopId');
        $isNew = $data['isNew'];
        Db::startTrans();
        try{
            $validate = new validate();
            if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
            $result = $this->allowField(true)->save($data);
            if(false !== $result){
                WSTClearAllCache();
                WSTUseResource(0, $this->applyId, $data['accreditImg']);
                if($isNew){
                    //启用上传图片
                    WSTUseResource(0, $this->applyId, $data['brandImg']);
                    //商品描述图片
                    WSTEditorImageRocord(0, $this->applyId, '',$data['brandDesc']);
                }
                Db::commit();
                return WSTReturn("新增成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('新增失败',-1);
    }

    /**
     * 编辑
     */
    public function edit(){
        $applyId = input('post.id/d');
        $data = input('post.');
        $isNew = $data['isNew'];
        Db::startTrans();
        try{
            WSTUseResource(0, $applyId, $data['accreditImg'], 'brand_applys', 'accreditImg');
            if($isNew){
                WSTUseResource(0, $applyId, $data['brandImg'], 'brand_applys', 'brandImg');
                // 品牌描述图片
                $desc = $this->where('applyId',$applyId)->value('brandDesc');
                WSTEditorImageRocord(0, $applyId, $desc, $data['brandDesc']);
            }

            $validate = new validate();
            if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
            $result = $this->allowField(['brandName','brandImg','brandDesc','accreditImg','catIds'])->save($data,['applyId'=>$applyId]);
            if(false !== $result){
                WSTClearAllCache();
                Db::commit();
                return WSTReturn("修改成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('修改失败',-1);
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('post.id/d');
        $data = [];
        $data['dataFlag'] = -1;
        $apply = $this->where(['applyId'=>$id])->find();
        Db::startTrans();
        try{
            $result = $this->where(['applyId'=>$id])->update($data);
            if($apply['isNew']){
                // 如果是新品牌，才将品牌图片、品牌描述图片设置成无效
                WSTUnuseResource('brand_applys','brandImg',$id);
                // 品牌描述图片
                $desc = $this->where('applyId',$id)->value('brandDesc');
                WSTEditorImageRocord(0, $id, $desc,'');
                if($apply['applyStatus']==1){
                    // 如果审核通过了
                    // 删除品牌表记录
                    Db::name('brands')->where(['brandId'=>$apply['brandId']])->update($data);
                    // 删除推荐品牌
                    Db::name('recommends')->where(['dataSrc'=>2,'dataId'=>$apply['brandId']])->delete();
                    // 删除品牌和分类的关系
                    Db::name('cat_brands')->where(['brandId'=>$apply['brandId']])->delete();
                }
            }
            WSTUnuseResource('brand_applys','accreditImg',$id);

            if(false !== $result){
                WSTClearAllCache();
                Db::commit();
                return WSTReturn("删除成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败'.$e->getMessage(),-1);
    }

    /**
     * 根据品牌名称查找品牌信息
     */
    public function getBrandByKey(){
        $key = input('key');
        $brands = Db::name('brands')->where([['brandName','like',"%$key%"],['dataFlag','=',1]])->select();
        if(empty($brands))return WSTReturn('找不到品牌',-1);
        foreach($brands as $key => $v){
            //获取关联的分类
            $brands[$key]['catIds'] = Db::name('cat_brands')->where(['brandId'=>$v['brandId']])->column('catId');
        }
        return WSTReturn('',1,['data'=>$brands]);
    }

    /**
     * 获取品牌信息
     */
    public function getBrandInfo(){
        $brandId = input('brandId');
        $brand = Db::name('brands')->where([['brandId','=',$brandId],['dataFlag','=',1]])->find();
        $brand['catIds'] = Db::name('cat_brands')->where(['brandId'=>$brandId])->column('catId');
        return WSTReturn('',1,['data'=>$brand]);
    }
}
