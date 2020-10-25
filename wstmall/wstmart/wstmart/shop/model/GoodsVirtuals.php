<?php
namespace wstmart\shop\model;
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
    
}