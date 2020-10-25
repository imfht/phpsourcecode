<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Common\Model\CommonModel;
use Common\Util\Page;
use Common\Util\Tree;
use Think\Model;
/**
 * 菜单模型
 * @author jry <598821125@qq.com>
 */
class PromGoodsModel extends CommonModel{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('name','require','商品名称必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,32', '商品名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('category_id', 'require', '请选择栏目', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('goods_no', 'require', '商品编号必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('type_id', 'require', '商品类型必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('img', 'require', '商品图片必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('sell_price', 'require', '商品价格必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('market_price', 'require', '商品市场价格必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cost_price', 'require', '商品成本价必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('warning_line', '1,32', '商品警告数必须', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('store_nums', '1,32', '商品库存数必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH)
    );


    /**
     * 返回获取到的数据
     * @return mixed
     */
    public function getData()
    {
        //搜索
        $keyword = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|name'] = array($condition, $condition, '_multi'=>true); //搜索条件
        $data =$this->where($map)->limit($this->start,$this->size)->order('sort DESC, id DESC')->select();//根据条件获取数据
        return $this->getDatas($data,$map);//对数据进行处理
    }
}
