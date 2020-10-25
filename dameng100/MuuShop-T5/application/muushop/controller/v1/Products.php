<?php
namespace app\muushop\controller\v1;

use think\Controller;
use think\Db;

class Products extends Controller {

	/**
     * 商品列表
     * @param $category_id
     * @param $search
     * @param $sortType
     * @param $sortPrice
     * @return array
     * @throws \think\exception\DbException
     */
	public function lists()
	{
		echo 'test';
	}

	/**
     * 获取商品详情
     * @param $goods_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function detail($goods_id)
    {
        // 商品详情
        $detail = GoodsModel::detail($goods_id);
        if (!$detail || $detail['goods_status']['value'] != 10) {
            return $this->renderError('很抱歉，商品信息不存在或已下架');
        }
        
        return $this->renderSuccess(compact('detail', 'cart_total_num', 'specData'));
    }


}