<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\BrandApplys as validate;
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
 * 品牌申请业务处理
 */
class BrandApplys extends Base{
	protected $pk = 'applyId';
	/**
	 * 分页
	 */
	public function pageQuery(){
        $key = input('key');
        $where[] = ['b.dataFlag','=',1];
        if($key!='')$where[] = ['b.brandName','like','%'.$key.'%'];
        $page = Db::name('brand_applys')
            ->alias('b')
            ->join('__SHOPS__ s','b.shopId=s.shopId','left')
            ->where($where)
            ->field('b.applyId,b.brandName,b.brandImg,b.brandDesc,b.applyStatus,s.shopName')
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
	 * 获取指定对象
	 */
	public function getById($id){
		$result = $this->where(['applyId'=>$id])->find();
		//获取关联的分类
        $result['catIds'] = explode(',',$result['catIds']);
		return $result;
	}

    /**
     * 处理品牌申请
     */
	public function handleApply(){
        $applyId = input('post.id');

        $data = input('post.');
        $idsStr = explode(',',$data['catIds']);
        if($idsStr!=''){
            foreach ($idsStr as $v){
                if((int)$v>0)$ids[] = (int)$v;
            }
        }
        Db::startTrans();
        try{
            $validate = new validate();
            if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
            $result = $this->allowField(['brandName','brandImg','brandDesc','accreditImg','catIds','applyStatus','applyDesc'])->save($data,['applyId'=>$applyId]);
            if(false !== $result){
                WSTClearAllCache();
                $apply = Db::name('brand_applys')->where(['applyId'=>$applyId])->find();
                $brandId = 0;
                if($apply['applyStatus']==1){
                    if($apply['isNew']==1){
                        $brandData = [
                            "brandName"=>$data['brandName'],
                            "brandImg"=>$data['brandImg'],
                            "brandDesc"=>$data['brandDesc'],
                            'createTime'=>date('Y-m-d H:i:s'),
                            'dataFlag'=>1
                        ];
                        $brandId = Db::name('brands')->insertGetId($brandData);
                    }else{
                        $brandId = $apply['brandId'];
                    }
                }
                if($brandId>0){
                    $this->where(['applyId'=>$applyId])->update(['brandId'=>$brandId]);
                    if($apply['isNew']==1){
                        foreach ($ids as $key =>$v){
                            $d = array();
                            $d['catId'] = $v;
                            $d['brandId'] = $brandId;
                            Db::name('cat_brands')->insert($d);
                        }
                    }
                }
                Db::commit();
                return WSTReturn("操作成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn("操作失败");
        }
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
        return WSTReturn('删除失败',-1);
	}

	/*
	 * 删除品牌下的商家
	 */
    public function delShop(){
        $id = input('post.id/d');
        $data = [];
        $data['dataFlag'] = -1;
        $apply = $this->where(['applyId'=>$id])->find();
        Db::startTrans();
        try{
            $result = $this->where(['applyId'=>$id])->update($data);
            if(false !== $result){
                // 删除推荐品牌
                Db::name('recommends')->where(['dataSrc'=>2,'dataId'=>$apply['brandId']])->delete();
                WSTClearAllCache();
                Db::commit();
                return WSTReturn("删除成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
    }

    /**
     * 根据品牌名称查找品牌信息
     */
    public function getBrandByKey(){
        $key = input('key');
        $brand = Db::name('brands')->where([['brandName','=',$key],['dataFlag','=',1]])->find();
        if(empty($brand))return WSTReturn('找不到品牌',-1);
        $brand = Db::name('brands')->field('brandName')->where([['brandId','=',$brand['brandId']],['dataFlag','=',1]])->find();
        return WSTReturn('',1,['data'=>$brand]);
    }
}