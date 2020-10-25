<?php
namespace wstmart\home\model;
use wstmart\common\model\GoodsVirtuals as CGoodsVirtuals;
use think\Db;
use think\Loader;
use Env;
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
 * 虚拟商品卡券模型
 */
class GoodsVirtuals extends CGoodsVirtuals{
	/**
	 * 导入
	 */
	public function importCards($data){
		require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
		$objReader = \PHPExcel_IOFactory::load(WSTRootPath().json_decode($data)->route.json_decode($data)->name);
		$objReader->setActiveSheetIndex(0);
		$sheet = $objReader->getActiveSheet();
		$rows = $sheet->getHighestRow();
		$cells = $sheet->getHighestColumn();
		//数据集合
        $readData = [];
        $shopId = (int)session('WST_USER.shopId');
        $goodsId = (int)input('goodsId');
        $importNum = 0;
        //生成订单
		Db::startTrans();
		try{
			//读取现有的卡券
			$goodscards = Db::name('goods_virtuals')->where(['dataFlag'=>1,'goodsId'=>$goodsId])->field('cardNo')->select();
			$goodscardsMap = [];
			if(count($goodscards)>0){
				foreach($goodscards as $v){
					$goodscardsMap[] = $v['cardNo'];
				}
			}
	        //循环读取每个单元格的数据
	        for ($row = 2; $row <= $rows; $row++){//行数是以第2行开始
	        	$cards = [];
	            $cards['shopId'] = $shopId;
	            $cards['goodsId'] = $goodsId;
	            $cardNo = trim($sheet->getCell("A".$row)->getValue());
	            if($cardNo=='')break;//如果某一行第一列为空则停止导入
	            $cards['cardNo'] = $cardNo;
	            $cards['cardPwd'] = trim($sheet->getCell("B".$row)->getValue());
	            $cards['createTime'] = date('Y-m-d H:i:s');
	            if(in_array($cardNo,$goodscardsMap))continue;
	            $goodscardsMap[] = $cardNo;
	            $readData[] = $cards;
	            $importNum++;
	        }
            if(count($readData)>0){
            	model('GoodsVirtuals')->insertAll($readData);
                $this->updateGoodsStock($goodsId);
            }
            Db::commit();
            return json_encode(['status'=>1,'importNum'=>$importNum]);
		}catch (\Exception $e) {
            Db::rollback();
            return json_encode(WSTReturn('导入商品卡券失败',-1));
        }
    }

    /**
     * 删除
     */
    public function del(){
        $shopId = (int)session('WST_USER.shopId');
        $ids = input('ids');
        $id = input('id');
        if($ids=='')return WSTReturn('请选择要删除的卡券号');
        try{
            $this->where([['id','in',$ids],['shopId','=',$shopId],['goodsId','=',$id]])->update(['dataFlag'=>-1]);
            $this->updateGoodsStock($id);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('操作失败');
        }
        return WSTReturn('操作成功',1);
    }

    /**
     * 编辑
     */
    public function edit(){
        $shopId = (int)session('WST_USER.shopId');
        $id = (int)input('id');
        //判断卡券是否有效
        $rs = $this->where(['id'=>$id,'shopId'=>$shopId,'dataFlag'=>1,'isUse'=>0])->find();
        if(empty($rs))return WSTReturn('非法的卡券');
        $cardNo = input('cardNo');
        $cardPwd = input('cardPwd');
        if($cardNo=='' || $cardPwd=='')return WSTReturn('请输入完整卡券信息');
        $conts = $this->where([['id','<>',$id],['shopId','=',$shopId],['cardNo','=',$cardNo],['dataFlag','=',1]])->Count();
        if($conts>0)return WSTReturn('该卡券号已存在，请重新输入');
        $rs = $this->update(['cardNo'=>$cardNo,'cardPwd'=>$cardPwd],[['id','=',$id],['shopId','=',$shopId]]);
        if($rs !== false){
            return WSTReturn('操作成功',1);
        }
        return WSTReturn('操作失败');
    }

    /**
     * 获取虚拟商品库存列表
     */
    public function stockByPage(){
        $key = input('cardNo');
        $id = (int)input('id');
        $isUse = (int)input('isUse',-1);
        $shopId = (int)session('WST_USER.shopId');
        $where = ['shopId'=>$shopId,'goodsId'=>$id,'dataFlag'=>1];
        if($key !='')$where[] = ['cardNo','like','%'.$key.'%'];
        if(in_array($isUse,[0,1]))$where['isUse'] = $isUse;
        $page = $this->field('orderNo,orderId,cardNo,id,cardPwd,isUse')
        ->where($where)->order('id desc')
        ->paginate(20)->toArray();
        return  $page;
    }

    /**
     * 生成卡券号
     */
    public function getCardNo($shopId){
        $cardNo = date('Ymd').sprintf("%08d", rand(0,99999999));
        $conts = $this->where(['shopId'=>$shopId,'dataFlag'=>1,'cardNo'=>$cardNo])->Count();
        if($conts==0){
            return $cardNo;
        }else{
            return $this->getCardNo($shopId);
        }
    }

    /**
     * 生成卡券
     */
    public function add(){
        $shopId = (int)session('WST_USER.shopId');
        $goodsId = (int)input('goodsId');
        //判断商品是否有效
        $goods = model('goods')->where(['goodsId'=>$goodsId,'shopId'=>$shopId,'goodsType'=>1])->find();
        if(empty($goods))return WSTReturn('非法的卡券商品');
        $cardNo = input('cardNo');
        $cardPwd = input('cardPwd');
        if($cardNo=='' || $cardPwd=='')return WSTReturn('请输入完整卡券信息');
        $conts = $this->where(['shopId'=>$shopId,'dataFlag'=>1,'cardNo'=>$cardNo])->Count();
        if($conts>0)return WSTReturn('该卡券号已存在，请重新输入');
        $data = [];
        $data['cardNo'] = $cardNo;
        $data['cardPwd'] = $cardPwd;
        $data['dataFlag'] = 1;
        $data['shopId'] = $shopId;
        $data['goodsId'] = $goodsId;
        $data['createTime'] = date('Y-m-d H:i:s');
        Db::startTrans();
        try{
            $this->save($data);
            $this->updateGoodsStock($goodsId);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败');
        }
        return WSTReturn('新增成功',1);
    }

    /**
     * 更新商品数量
     */
    public function updateGoodsStock($id){
        $shopId = (int)session('WST_USER.shopId');
        $counts = $this->where(['dataFlag'=>1,'goodsId'=>$id,'shopId'=>$shopId,'isUse'=>0])->Count();
        Db::name('goods')->where('goodsId',$id)->setField('goodsStock',$counts);
    }
}