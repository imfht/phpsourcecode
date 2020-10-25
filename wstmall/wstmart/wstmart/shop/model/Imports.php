<?php
namespace wstmart\shop\model;
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
 * 导入类
 */
class Imports{
    /**
     * 上传商品数据
     */
    public function importGoods($data){
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objReader = \PHPExcel_IOFactory::load(WSTRootPath().json_decode($data)->route.json_decode($data)->name);
        $objReader->setActiveSheetIndex(0);
        $sheet = $objReader->getActiveSheet();
        $rows = $sheet->getHighestRow();
        $cells = $sheet->getHighestColumn();
        //数据集合
        $readData = [];
        $shopId = (int)session('WST_USER.shopId');
        $importNum = 0;
        $goodsCatMap = []; //记录最后一级商品分类
        $goodsCatPathMap = [];//记录商品分类路径
        $shopCatMap = [];//记录店铺分类
        $goodsCat1Map = [];//记录最后一级商品分类对应的一级分类
        $tmpGoodsCatId = 0;
        $goodsCatBrandMap = [];//商品分类和品牌的对应关系

        // 规格商品导入错误集合 [['msg'=>'错误提示']];
        $specGoodsErrMsgArr = [];

        //生成订单
        Db::startTrans();
        try{
            //获取商家商城分类
            $applyCatIds = model('GoodsCats')->getShopApplyGoodsCats($shopId);
            //循环读取每个单元格的数据
            for ($row = 3; $row <= $rows; $row++){//行数是以第3行开始
                $tmpGoodsCatId = 0;
                $goods = [];
                $goods['shopId'] = $shopId;
                $goods['goodsName'] = trim($sheet->getCell("A".$row)->getValue());
                if($goods['goodsName']=='')break;//如果某一行第一列为空则停止导入
                $importGoodsSn = trim($sheet->getCell("B".$row)->getValue());
                $result = $this->checkUniqueNo('goodsSn',$importGoodsSn);
                if($result>0){
                    $specGoodsErrMsgArr[] = ['msg'=>"商品【".$goods['goodsName']."】导入失败，商品编号【".$importGoodsSn."】已存在"];
                    continue;
                }
                $goods['goodsSn'] = $importGoodsSn;
                $importProductNo = trim($sheet->getCell("C".$row)->getValue());
                $result = $this->checkUniqueNo('productNo',$importProductNo);
                if($result>0){
                    $specGoodsErrMsgArr[] = ['msg'=>"商品【".$goods['goodsName']."】导入失败，商品货号【".$importProductNo."】已存在"];
                    continue;
                }
                $goods['productNo'] = $importProductNo;
                $goods['marketPrice'] = trim($sheet->getCell("D".$row)->getValue());
                if(floatval($goods['marketPrice'])<0.01)$goods['marketPrice'] = 0.01;
                $goods['shopPrice'] = trim($sheet->getCell("E".$row)->getValue());
                if(floatval($goods['shopPrice'])<0.01)$goods['shopPrice'] = 0.01;
                $goods['goodsStock'] = trim($sheet->getCell("F".$row)->getValue());
                if(intval($goods['goodsStock'])<=0)$goods['goodsStock'] = 0;
                $goods['warnStock'] = trim($sheet->getCell("G".$row)->getValue());
                if(intval($goods['warnStock'])<=0)$goods['warnStock'] = 0;
                $goods['goodsImg'] = '';
                $goods['shopCatId1'] = 0;
                $goods['shopCatId2'] = 0;
                $goods['goodsUnit'] = trim($sheet->getCell("H".$row)->getValue());
                $goods['goodsSeoKeywords'] = trim($sheet->getCell("I".$row)->getValue());
                $goods['goodsTips'] = trim($sheet->getCell("J".$row)->getValue());
                $goods['isRecom'] = (trim($sheet->getCell("K".$row)->getValue())!='')?1:0;
                $goods['isBest'] = (trim($sheet->getCell("L".$row)->getValue())!='')?1:0;
                $goods['isNew'] = (trim($sheet->getCell("M".$row)->getValue())!='')?1:0;
                $goods['isHot'] = (trim($sheet->getCell("N".$row)->getValue())!='')?1:0;
                $goods['goodsCatId'] = 0;
                //查询商城分类
                $goodsCat = trim($sheet->getCell("O".$row)->getValue());
                if(!empty($goodsCat)){
                    //先判断集合是否存在，不存在的时候才查数据库
                    if(isset($goodsCatMap[$goodsCat])){
                        $goods['goodsCatId'] = $goodsCatMap[$goodsCat];
                        $goods['goodsCatIdPath'] = $goodsCatPathMap[$goodsCat];
                        $catIds = explode('_',$goods['goodsCatIdPath']);
                        $tmpGoodsCatId = $catIds[0];
                    }else{
                        $goodsCatId = Db::name('goods_cats')->where(['catName'=>$goodsCat,'dataFlag'=>1])->field('catId')->find();
                        if(!empty($goodsCatId['catId']) && in_array($goodsCatId['catId'],$applyCatIds)){
                            $goodsCats = model('GoodsCats')->getParentIs($goodsCatId['catId']);
                            $goods['goodsCatId'] = $goodsCatId['catId'];
                            $goods['goodsCatIdPath'] = implode('_',$goodsCats)."_";
                            //放入集合
                            $goodsCatMap[$goodsCat] = $goodsCatId['catId'];
                            $goodsCatPathMap[$goodsCat] = implode('_',$goodsCats)."_";
                            $goodsCat1Map[$goodsCat] = (count($goodsCats)==3)?$goodsCats[2]:0;
                            $tmpGoodsCatId = $goodsCat1Map[$goodsCat];
                        }
                    }
                }
                // 判断商城分类是否为最后一级分类
                $lastCatCount = Db::name('goods_cats')->where(['parentId'=>$goods['goodsCatId']])->count();
                if($lastCatCount!=0){
                    $specGoodsErrMsgArr[] = ['msg'=>"商品【".$goods['goodsName']."】导入失败，商城分类非最后一级分类"];
                    continue;
                }
                //查询店铺分类
                $shopGoodsCat = trim($sheet->getCell("P".$row)->getValue());
                if(!empty($shopGoodsCat)){
                    //先判断集合是否存在，不存在的时候才查数据库
                    if(isset($shopCatMap[$shopGoodsCat])){
                        $goods['shopCatId1'] = $shopCatMap[$shopGoodsCat]['s1'];
                        $goods['shopCatId2'] = $shopCatMap[$shopGoodsCat]['s2'];
                    }else{
                        $shopCat= Db::name("shop_cats")->alias('sc1')
                            ->join('__SHOP_CATS__ sc2','sc2.parentId=sc1.catId','left')
                            ->field('sc1.catId catId1,sc2.catId catId2,sc2.catName')
                            ->where(['sc1.shopId'=> $shopId,'sc1.dataFlag'=>1,'sc2.catName'=>$shopGoodsCat])
                            ->find();
                        if(!empty($shopCat)){
                            $goods['shopCatId1'] = $shopCat['catId1'];
                            $goods['shopCatId2'] = $shopCat['catId2'];
                            //放入集合
                            $shopCatMap[$shopGoodsCat] = [];
                            $shopCatMap[$shopGoodsCat]['s1'] = $goods['shopCatId1'];
                            $shopCatMap[$shopGoodsCat]['s2'] = $goods['shopCatId2'];
                        }
                    }
                }
                //查询品牌
                $brand = trim($sheet->getCell("Q".$row)->getValue());
                $bandRes = $this->checkBrandName($brand);
                if(!$bandRes){
                    $specGoodsErrMsgArr[] = ['msg'=>"【".$brand."】品牌未申请，已跳过"];
                    $brand = '';
                }
                if(!empty($brand)){

                    if(isset($goodsCatBrandMap[$brand])){
                        $goods['brandId'] = $goodsCatBrandMap[$brand];
                    }else{
                        $brands = Db::name('brands')->alias('a')->join('__CAT_BRANDS__ cb','a.brandId=cb.brandId','inner')
                            ->where(['catId'=>$tmpGoodsCatId,'brandName'=>$brand,'dataFlag'=>1])->field('a.brandId')->find();
                        if(!empty($brands)){
                            $goods['brandId'] = $brands['brandId'];
                            $goodsCatBrandMap[$brand] = $brands['brandId'];
                        }
                    }
                }
                $goods['goodsDesc'] = trim($sheet->getCell("R".$row)->getValue());
                $goods['isSale'] = 0;
                $goods['goodsStatus'] = (WSTConf("CONF.isGoodsVerify")==1)?0:1;
                $goods['dataFlag'] = 1;
                $goods['saleTime'] = date('Y-m-d H:i:s');
                $goods['createTime'] = date('Y-m-d H:i:s');


                // 商品数据写入
                $goodsId = Db::name('goods')->insertGetId($goods);
                if($goodsId===false)continue;
                // 商品评分表
                $gs = [];
                $gs['goodsId'] = $goodsId;
                $gs['shopId'] = $shopId;
                Db::name('goods_scores')->insert($gs);
                /**
                 * 导入商品规格
                 * 1.判断规格值是否存在
                 * 2.判断规格值与商品分类是否对应
                 * 3.判断组合的总数是否正确
                 */
                $sheetName = trim($sheet->getCell("S".$row)->getValue());
                if($sheetName!=''){// 存在规格页的key
                    $currSheet = $objReader->getSheetByName($sheetName);
                    if($currSheet!=null){// 规格页存在
                        // 固定的标题、其余为规格值
                        $titles = ['货号'=>'productNo',
                            '市场价'=>'marketPrice',
                            '店铺价'=>'specPrice',
                            '库存'=>'specStock',
                            '库存预警'=>'warnStock'];
                        $titKeys = array_keys($titles);
                        // 总行数
                        $_rows = $currSheet->getHighestRow();
                        // 总列数
                        $_cells = count($currSheet->toArray()[0]);
                        // 第一行为标题及规格名称

                        // 遍历第一行，筛选出规格名称

                        // 记录货号-预警库存
                        $titKeyArr = [];
                        // 记录规格对应的字母
                        $specKeyArr = [];
                        for ($_cell = 2; $_cell <= $_cells; $_cell++){// 第一列为推荐，从第二列开始遍历
                            $letter = $this->intToChr($_cell-1);
                            $cellVal = $currSheet->getCell($letter.'1')->getValue();
                            if(in_array($cellVal, $titKeys)){
                                // 为固定值
                                $titKeyArr[$titles[$cellVal]] = $letter; // ['productNo'=>'E']
                            }else{
                                // 为规格值
                                $specKeyArr[$cellVal] = $letter; // ['颜色'=>'B']
                            }
                        }

                        /**
                         * 2.判断规格值是否存在、是否与商品分类对应
                         */
                        $goodsCatPath = explode('_',$goods['goodsCatIdPath'])[0];
                        // 存在的规格总数，若存在总数与找到的规格总数不同，则终止规格操作
                        $findCount = count($specKeyArr);
                        $existsCount = 0;

                        // 不存在的规格名称，用于错误提示
                        $emptyCatName = [];
                        foreach($specKeyArr as $catName=>$v){
                            $catNameData = Db::name('spec_cats')->where(['catName'=>$catName])
                                ->where("goodsCatPath like '{$goodsCatPath}_%'")
                                ->field('catId')
                                ->find();
                            if(!empty($catNameData)){
                                // 保存规格的catId
                                $specKeyArr[$catName] = ['catId'=>$catNameData['catId'], 'letter'=>$v]; // ['颜色'=>['catId'=>1,'letter'=>'B']]
                                ++$existsCount;
                            }else{
                                $emptyCatName[] = $catName;
                            }
                        }
                        // 找到的总数 与 存在的总数相同
                        if($findCount===$existsCount){
                            /**
                             *  3.判断组合的总数是否正确
                             */
                            // 取出每一列规格对应的值
                            for ($_row = 2; $_row <= $_rows; $_row++){// 从第二列开始取值
                                foreach($specKeyArr as $k => $v){
                                    $letter = $v['letter'];
                                    if( !isset($specKeyArr[$k]['specValArr']) ) $specKeyArr[$k]['specValArr'] = [];
                                    $val = $currSheet->getCell($letter.$_row)->getValue();
                                    $specKeyArr[$k]['specValArr'][] = $val;
                                }
                            }
                            // 计算组合总条数
                            $specItemTotal = 1;
                            foreach($specKeyArr as $k => $v){
                                $specKeyArr[$k]['specValArr'] = array_unique($v['specValArr']); // ['颜色'=>['catId'=>1,'letter'=>'B','specValArr'=>['红色','蓝色','白色']]]

                                $specItemTotal = count($specKeyArr[$k]['specValArr']) * $specItemTotal;
                            }
                            // 总组合数 = 总行数-第一行(title)
                            if($specItemTotal===($_rows-1)){
                                // 执行数据写入
                                $specMap = [];
                                foreach ($specKeyArr as $k=>$v){
                                    foreach($v['specValArr'] as $itemName){
                                        $sitem = [];
                                        $sitem['shopId'] = $shopId;
                                        $sitem['catId'] = $v['catId'];
                                        $sitem['goodsId'] = $goodsId;
                                        $sitem['itemName'] = $itemName;
                                        $sitem['itemImg'] = '';
                                        $sitem['dataFlag'] = 1;
                                        $sitem['createTime'] = date('Y-m-d H:i:s');
                                        $itemId = Db::name('spec_items')->insertGetId($sitem);
                                        if($sitem['itemImg']!='')WSTUseResource(0, $itemId, $sitem['itemImg']);
                                        $specMap[$itemName] = $itemId; // ['红色'=>1,'蓝色'=>2,'白色'=>3]
                                    }
                                }

                                $defaultPrice = 0;//最低价
                                $totalStock = 0;//总库存
                                $gspecArray = [];
                                $isFindDefaultSpec = false;

                                // 取出每一列规格对应的值
                                for ($_row = 2; $_row <= $_rows; $_row++){// 从第二行开始取值

                                    // 写入固定的值
                                    // '货号'=>'productNo','市场价'=>'marketPrice', '店铺价'=>'specPrice', '库存'=>'specStock', '库存预警'=>'warnStock'

                                    $goodsSpecIds = [];
                                    foreach($specKeyArr as $k1=>$v1){// ['颜色'=>['catId'=>1,'letter'=>'B','specValArr'=>['红色','蓝色','白色']]]
                                        $specItemVal = $currSheet->getCell($v1['letter'].$_row)->getValue();// 红色
                                        $goodsSpecIds[] = $specMap[$specItemVal]; // ['红色'=>1,'蓝色'=>2,'白色'=>3]
                                    }
                                    $gspec = [];
                                    $gspec['specIds'] = implode(':',$goodsSpecIds);
                                    $gspec['shopId'] = $shopId;
                                    $gspec['goodsId'] = $goodsId;
                                    $gspec['productNo'] =  $currSheet->getCell($titKeyArr['productNo'].$_row)->getValue();
                                    $gspec['marketPrice'] = (float)$currSheet->getCell($titKeyArr['marketPrice'].$_row)->getValue();
                                    $gspec['specPrice'] = (float)$currSheet->getCell($titKeyArr['specPrice'].$_row)->getValue();
                                    $gspec['specStock'] = (int)$currSheet->getCell($titKeyArr['specStock'].$_row)->getValue();
                                    $gspec['warnStock'] = (int)$currSheet->getCell($titKeyArr['warnStock'].$_row)->getValue();


                                    //设置默认规格
                                    if($currSheet->getCell('A'.$_row)->getValue()=="是"){
                                        $isFindDefaultSpec = true;
                                        $defaultPrice = $gspec['specPrice'];
                                        $gspec['isDefault'] = 1;
                                    }else{
                                        $gspec['isDefault'] = 0;
                                    }

                                    // 如果未找到默认规格值，则取最后一行作为默认规格
                                    if( !$isFindDefaultSpec  && $_row==$_rows ){
                                        $defaultPrice = $gspec['specPrice'];
                                        $gspec['isDefault'] = 1;
                                    }
                                    $gspecArray[] = $gspec;
                                    //获取总库存
                                    $totalStock = $totalStock + $gspec['specStock'];
                                }
                                if(count($gspecArray)>0){
                                    Db::name('goods_specs')->insertAll($gspecArray);
                                    //更新默认价格和总库存
                                    Db::name('goods')->where('goodsId',$goodsId)->update(['isSpec'=>1,'shopPrice'=>$defaultPrice,'goodsStock'=>$totalStock]);
                                }
                            }else{
                                /**
                                 * 错误信息：组合总数不匹配
                                 */
                                $specGoodsErrMsgArr[] = ['msg'=>"商品【".$goods['goodsName']."】规格导入失败，规格值组合总条数不匹配,组合总条数应为【{$specItemTotal}】，实际组合条数为【".($_rows-1)."】"];
                            }
                        }else{
                            /**
                             * 错误信息：部分规格值不存在
                             */
                            $specGoodsErrMsgArr[] = ['msg'=>"商品【".$goods['goodsName']."】规格导入失败，商品分类对应的规格类型【". implode(',',$emptyCatName) ."】,不存在"];
                        }
                    }
                }
                $importNum++;
            }
            Db::commit();
            return json_encode(['status'=>1,'importNum'=>$importNum,'specErrMsg'=>$specGoodsErrMsgArr]);
        }catch (\Exception $e) {
            Db::rollback();
            return json_encode(WSTReturn('导入商品失败'.$e->getMessage(),-1));
        }
    }

    /**
     * 数字转字母 （类似于Excel列标）
     * @param Int $index 索引值
     * @param Int $start 字母起始值
     * @return String 返回字母
     */
    private function intToChr($index, $start = 65) {
        $str = '';
        if (floor($index / 26) > 0) {
            $str .= intToChr(floor($index / 26)-1);
        }
        return $str . chr($index % 26 + $start);
    }

    /*
 * 检查导入商品的商品编号和商品货号是否唯一
 */
    public function checkUniqueNo($filed,$value){
        $uniqueNo = WSTGoodsNo();
        $condition = [];
        if($value == '') $value = $uniqueNo;
        $condition[$filed] = $value;
        $condition['dataFlag'] = 1;
        return Db::name('goods')->where($condition)->count();
    }

    /*
    * 检查导入商品的品牌是否存在于商品品牌表
    */
    public function checkBrandName($brand=''){
        if($brand=='')return false;
        $res = Db::name('brand_applys')->where(['brandName'=>$brand,'applyStatus'=>1,'shopId'=>(int)session('WST_USER.shopId')])->find();
        if($res){
            return true;
        }
        return false;
    }
}