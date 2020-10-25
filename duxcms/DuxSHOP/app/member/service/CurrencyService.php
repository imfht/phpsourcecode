<?php
namespace app\member\service;
/**
 * 货币接口
 */
class CurrencyService extends \app\base\service\BaseService {


    /**
     * 获取账户余额
     * @param $type
     * @return mixed
     */
    public function getAmount($type) {
        if($type == 'credit') {
            $info = target('member/PointsAccount')->getWhereInfo(['A.user_id' => USER_ID]);
            return $info['money'];
        }
        return 0;
    }

    /**
     * 货币支付
     * @param $key
     * @param $userId
     * @param $amount
     * @param $payNo
     * @param $payName
     * @param $title
     * @param $remark
     * @param int $type
     * @return bool
     */
    public function payAmount($key, $userId, $amount, $payNo, $payName, $title, $remark, $type = 0) {
        if($key == 'credit') {
            $status = target('member/Points', 'service')->account([
                'user_id' => $userId,
                'money' => $amount,
                'type' => $type,
                'deduct' => 1,
                'title' => $title ? $title : '商品兑换',
                'remark' => $remark ? $remark : '商品兑换',
            ]);
            if(!$status) {
                return $this->error(target('member/Points', 'service')->getError());
            }
        }
        return true;
    }


    /**
     * 货币类型
     * @return array
     */
    public function typeList() {
        $list = hook('service', 'Currency', 'Type');
        $data = array();
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        return $data;
    }




}
