<?php
namespace app\sale\service;
/**
 * 推广接口
 */
class SaleService extends \app\base\service\BaseService {

    /**
     * 增加推广用户
     * @param $userId
     * @param int $parentId
     * @return bool
     */
    public function addUser($userId, $parentId = 0) {
        if (empty($userId)) {
            return $this->error('用户ID不能为空');
        }
        $data = [
            'user_id' => $userId,
            'parent_id' => $parentId,
            'join_time' => time(),
            'code' => $this->randCode($userId)
        ];
        $info = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        if (!empty($info)) {
            return $this->success($info['id']);
        }
        $id = target('sale/SaleUser')->add($data);
        if (!$id) {
            return $this->error('增加推广用户失败');
        }

        if (!target('sale/SaleStatis')->updateUser($parentId)) {
            return $this->error(target('sale/SaleStatis')->getError());
        }

        return $this->success($id);
    }

    /**
     * 推荐码生成
     * @param $userId
     * @return string
     */
    protected function randCode($userId) {
        $code = substr(base_convert(sha1(uniqid(mt_rand() . $userId)), 16, 36), 0, 5);
        $info = target('sale/SaleUser')->getWhereInfo([
            'code' => $code
        ]);
        if (empty($info)) {
            return $code;
        }
        return $this->randCode($userId);
    }

    /**
     * 加入推广商
     * @param $userId
     * @param int $parentId
     * @param int $applyCheck
     * @return bool
     */
    public function addAgent($userId, $parentId = 0, $applyCheck = 1) {

        $config = target('sale/saleConfig')->getConfig();
        $applyWhere = unserialize($config['apply_where']);


        if ($config['apply_type'] == 2) {
            $where = [];
            $where['order_user_id'] = $userId;
            $where['order_status'] = 1;
            if ($applyWhere['type']) {
                $where['pay_status'] = 1;
            } else {
                $where['order_complete_status'] = 1;
            }
            $count = target('order/Order')->countList($where);
            if ($count < $applyWhere['data']) {
                return $this->error('申请失败，您未达到消费次数！');
            }
        }

        if ($config['apply_type'] == 3) {
            $where = [];
            $where['order_user_id'] = $userId;
            $where['order_status'] = 1;
            if ($applyWhere['type']) {
                $where['pay_status'] = 1;
            } else {
                $where['order_complete_status'] = 1;
            }
            $list = target('order/Order')->loadList($where);
            $count = 0;
            foreach ($list as $vo) {
                $count += $vo['order_price'] + $vo['delivery_price'];
            }
            if ($count < $applyWhere['data']) {
                return $this->error('申请失败，您未达到消费次金额！');
            }


        }
        if ($config['apply_type'] == 4) {
            $shopInfo = target('shop/Shop')->getWhereInfo([
                'goods_no' => $applyWhere['data']
            ]);
            $where = [];
            $where['B.order_user_id'] = $userId;
            $where['A.goods_id'] = $shopInfo['shop_id'];
            $orderGoods = target('order/OrderGoods')->loadHasList($where);
            if (empty($orderGoods)) {
                return $this->error('申请失败，您还未购买指定商品！');
            }
        }

        if ($applyCheck) {
            return $this->submitApply($userId);
        }

        $userInfo = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        $id = $userInfo['id'];
        if (empty($userInfo)) {
            $id = $this->addUser($userId, $parentId);
            if (!$id) {
                return false;
            }
        }
        if ($userInfo['agent']) {
            return $this->error('您已经是推广商，请勿重复加入！');
        }
        $data = [
            'id' => $id,
            'agent' => 1,
            'agent_time' => time()
        ];
        if (!target('sale/SaleUser')->edit($data)) {
            return $this->error('推广商加入失败，请稍后再试！');
        }

        return $this->success();
    }

    /**
     * 推广升级
     * @param $userId
     * @return bool
     */
    public function levelUser($userId) {

        $userInfo = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        $levelInfo = target('sale/SaleUserLevel')->getWhereInfo([
            'level_id' => $userInfo['level_id']
        ]);
        if ($levelInfo['special']) {
            return true;
        }

        $config = target('sale/saleConfig')->getConfig();
        $saleLevel = $config['level_type'];

        $saleSaleStatis = target('sale/SaleStatis')->getWhereInfo([
            'user_id' => $userId
        ]);

        $num = 0;
        switch ($saleLevel) {
            case 1:
                $num = $saleSaleStatis['sale_order_money'];
                break;
            case 2:
                $num = $saleSaleStatis['sale_order_num'];
                break;
            case 3:
                $num = $saleSaleStatis['has_order_money'];
                break;
            case 4:
                $num = $saleSaleStatis['has_order_num'];
                break;
            case 5:
                $num = $saleSaleStatis['order_money'];
                break;
            case 6:
                $num = $saleSaleStatis['order_num'];
                break;
            case 7:
                $num = $saleSaleStatis['has_user_num'];
                break;
            case 8:
                $num = $saleSaleStatis['sale_user_num'];
                break;
        }

        $levelList = target('sale/SaleUserLevel')->loadList([
            'special' => 0,
        ], 0, 'level_where asc');

        $data = [];
        foreach ($levelList as $key => $vo) {
            if ($num >= $vo['level_where']) {
                $data = $vo;
            }
        }

        if ($levelInfo['level_id'] == $data['level_id'] || $levelInfo['level_where'] > $data['level_where']) {
            return true;
        }
        $status = target('sale/SaleUser')->edit([
            'id' => $userInfo['id'],
            'level_id' => $data['level_id']
        ]);
        if (!$status) {
            return $this->error(target('sale/SaleUser')->getError());
        }

        return true;
    }


    /**
     * 提交申请
     * @param $userId
     * @return bool
     */
    public function submitApply($userId) {
        if (empty($userId)) {
            return $this->error('用户ID不能为空');
        }
        $userInfo = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        if ($userInfo['agent']) {
            return $this->error('该用户已是推广商，无需申请！');
        }
        $info = target('sale/SaleUserApply')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        if (!empty($info)) {
            if ($info['status'] == 0) {
                return $this->error('您的申请已被拒绝！');
            }
            if ($info['status'] == 1) {
                return $this->error('您的申请正在审核中，请耐心等待！');
            }
            if ($info['status'] == 2) {
                return $this->error('您已申请成功成为推广商！');
            }
        }
        $data = [
            'user_id' => $userId,
            'apply_time' => time(),
        ];
        if (!target('sale/SaleUserApply')->add($data)) {
            return $this->error('申请提交失败！');
        }

        return $this->success('申请提交成功！');
    }

    /**
     * 查询推荐人
     * @param $userId
     * @return bool
     */
    public function parentUser($userId) {
        if (empty($userId)) {
            return $this->error('用户ID不能为空');
        }
        $userInfo = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId
        ]);

        return target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userInfo['parent_id']
        ]);
    }

    /**
     * 推广通知
     * @param $name
     * @param $userId
     * @param array $data
     * @return bool
     */
    public function noticeSale($name, $userId, $data = []) {
        $config = target('sale/saleConfig')->getConfig();

        $status = $config['notice_' . $name . '_status'];
        $class = unserialize($config['notice_' . $name . '_class']);
        $title = $config['notice_' . $name . '_title'];

        if (!$status) {
            return $this->error('通知类型未开启!');
        }
        if (empty($class) || empty($title)) {
            return $this->error('通知内容未设置完整!');
        }

        foreach ($class as $vo) {
            $content = $config['notice_' . $name . '_' . $vo . '_tpl'];
            foreach ($data as $key => $v) {
                $content = str_replace('[' . $key . ']', $v, $content);
            }

            if (LAYER_NAME == 'mobile') {
                $layer = 'mobile';
            } else {
                $layer = 'controller';
            }
            $url = url($layer . '/member/index/index', [], true);
            $status = target('tools/Tools', 'service')->sendMessage([
                'receive' => $userId,
                'class' => $vo,
                'title' => $title,
                'content' => $content,
                'user_status' => 1,
                'param' => [
                    'url' => $url
                ]
            ]);
            if (!$status) {
                return $this->error(target('tools/Tools', 'service')->getError());
            }
        }

        return $this->success();
    }


}

