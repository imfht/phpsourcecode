<?php
namespace app\member\service;
/**
 * 财务处理
 */
class FinanceService extends \app\base\service\BaseService {

    /**
     * 操作账户
     * @param $data
     * @return bool
     */
    public function account($data) {
        $data = [
            'user_id' => intval($data['user_id']),
            'money' => price_format($data['money']),
            'pay_no' => $data['pay_no'] ? html_clear($data['pay_no']) : log_no(),
            'pay_name' => html_clear($data['pay_name']),
            'type' => isset($data['type']) ? intval($data['type']) : 1,
            'deduct' => isset($data['deduct']) ? intval($data['deduct']) : 1,
            'title' => $data['title'] ? html_clear($data['title']) : '在线支付',
            'remark' => html_clear($data['remark']),
        ];
        if(empty($data['user_id'])) {
            return $this->error('无法识别用户!');
        }
        if(bccomp($data['money'], 0, 2) === -1) {
            return $this->error('处理金额不正确!');
        }
        if(empty($data['pay_no']) || empty($data['pay_name'])) {
            return $this->error('支付号或支付名不正确!');
        }
        $model = target('member/PayAccount');
        $userInfo = $model->getWhereInfo([
            'A.user_id' => $data['user_id']
        ]);
        if(empty($userInfo)) {
            return $this->error('该账户不存在!');
        }
        //实际操作
        if($data['deduct']) {
            if(!$data['type']){
                if(bccomp($userInfo['money'], $data['money'], 2) === -1) {
                    return $this->error('账户余额不足,无法进行扣除!');
                }
            }
            if($data['type']){
                $status = $model->where(['user_id' => $data['user_id']])->setInc('money', $data['money']);
            }else{
                $status = $model->where(['user_id' => $data['user_id']])->setDec('money', $data['money']);
            }
            if(!$status){
                return $this->error('账户资金操作失败,请稍候再试!');
            }
        }

        if($data['type']){
            $status = $model->where(['user_id' => $data['user_id']])->setInc('charge', $data['money']);
        }else{
            $status = $model->where(['user_id' => $data['user_id']])->setInc('spend', $data['money']);
        }

        if(!$status){
            return $this->error('账户资金操作失败,请稍候再试!');
        }
        //写入记录
        $logData = array();
        $logData['user_id'] = $userInfo['user_id'];
        $logData['log_no'] = log_no($userInfo['user_id']);
        $logData['time'] = time();
        $logData['money'] = $data['money'];
        $logData['title'] = $data['title'];
        $logData['remark'] = $data['remark'];
        $logData['pay_no'] = $data['pay_no'];
        $logData['pay_name'] = $data['pay_name'];
        $logData['type'] = $data['type'];
        $logId = target('member/PayLog')->add($logData);
        if(!$logId) {
            return $this->error('资金记录失败,请稍候再试!');
        }

        $status = target('member/PayStats')->stats($userInfo['user_id'], $data['money'],  $data['type'], 'pay');
        if (!$status) {
            $this->error(target('member/PayStats', 'service')->getError());
        }
        return $this->success($logId);
    }

    /**
     * 检测账户余额
     * @param $userId
     * @param $balance
     * @return bool
     */
    public function checkAccount($userId, $balance) {
        $model = target('member/PayAccount');
        $userInfo = $model->getWhereInfo([
            'A.user_id' => $userId
        ]);
        if(empty($userInfo)) {
            return $this->error('该账户不存在!');
        }
        if($userInfo['money'] < $balance){
            return $this->error('账户余额不足!');
        }
        return $this->success();
    }

    /**
     * 账户余额
     * @param $userId
     * @return mixed
     */
    public function amountAccount($userId) {
        $info = target('member/PayAccount')->getWhereInfo(['A.user_id' => $userId]);
        return $info['money'] ? $info['money'] : 0;
    }

    /**
     * 汇率
     * @return int
     */
    public function erAccount() {
        return 1;
    }
}
