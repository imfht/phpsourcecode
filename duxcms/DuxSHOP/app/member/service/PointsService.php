<?php
namespace app\member\service;
/**
 * 积分处理
 */
class PointsService extends \app\base\service\BaseService {

    /**
     * 操作账户
     * @param $data
     * @return bool
     */
    public function account($data) {
        $data = [
            'user_id' => intval($data['user_id']),
            'money' => price_format($data['money']),
            'type' => isset($data['type']) ? intval($data['type']) : 1,
            'deduct' => isset($data['deduct']) ? intval($data['deduct']) : 1,
            'title' => html_clear($data['title']),
            'remark' => html_clear($data['remark']),
            'force' => false
        ];
        if(empty($data['user_id'])) {
            return $this->error('无法识别用户!');
        }
        if(bccomp($data['money'], 0, 2) === -1) {
            return $this->error('处理积分不正确!');
        }
        $model = target('member/PointsAccount');
        $userInfo = $model->getWhereInfo([
            'A.user_id' => $data['user_id']
        ]);
        if(empty($userInfo)) {
            $accountData = [
                'user_id' => $data['user_id']
            ];
            $accountId = target('member/PointsAccount')->add($accountData);
            if(!$accountId) {
                return $this->error('账户创建失败！');
            }
            $userInfo = [
                'account_id' => $accountId,
                'user_id' => $data['user_id'],
                'money' => 0,
                'charge' => 0,
                'spend' => 0
            ];
        }
        //实际操作
        if($data['deduct']) {
            if(!$data['type']){
                if(bccomp($userInfo['money'], $data['money'], 2) === -1 && !$data['force']) {
                    return $this->error('账户积分不足,无法进行扣除!');
                }
            }
            if($data['type']){
                $status = target('member/PointsCharge')->add([
                    'user_id' => $data['user_id'],
                    'money' => $data['money'],
                    'start_time' => strtotime(date('Y-m-d')),
                    'stop_time' => strtotime(date('Y-m-d' , strtotime('+1 year'))),
                    'remark' => $data['title'],
                    'status' => 1
                ]);
                if(!$status){
                    return $this->error('账户积分操作失败,请稍候再试!');
                }

            }else{
                $moneyList = target('member/PointsCharge')->loadList([
                    'user_id' => $data['user_id'],
                    'status' => 1,
                    '_sql' => 'stop_time >=' . time()
                ], 0, 'start_time asc');

                $payMoney = $data['money'];
                $useData = [];
                $returnMoney = 0;
                foreach ($moneyList as $vo) {
                    if(bccomp($vo['money'], $payMoney, 2) === -1) {
                        $payMoney = price_calculate($payMoney, '-', $vo['money']);
                        $useData[] = $vo['id'];
                    }else {
                        $useData[] = $vo['id'];
                        $returnMoney = price_calculate($vo['money'], '-', $payMoney);
                        break;
                    }
                }
                if(empty($useData)) {
                    return $this->error('账户积分不足!');
                }
                foreach ($useData as $key => $vo) {
                    $status = target('member/PointsCharge')->where(['id' => $vo])->data(['status' => 0])->update();
                    if(!$status){
                        return $this->error('账户积分操作失败,请稍候再试!');
                    }
                }
                if(bccomp(0, $returnMoney, 2) === -1) {
                    $status = target('member/PointsCharge')->add([
                        'user_id' => $data['user_id'],
                        'money' => $returnMoney,
                        'start_time' => strtotime(date('Y-m-d')),
                        'stop_time' => strtotime(date('Y-m-d' , strtotime('+1 year'))),
                        'remark' => '使用结余',
                        'status' => 1
                    ]);
                    if(!$status){
                        return $this->error('账户积分操作失败,请稍候再试!');
                    }
                }
            }
        }

        if($data['type']){
            $status = $model->where(['user_id' => $data['user_id']])->setInc('charge', $data['money']);
        }else{
            $status = $model->where(['user_id' => $data['user_id']])->setInc('spend', $data['money']);
        }

        if(!$status){
            return $this->error('账户积分操作失败,请稍候再试!');
        }
        //写入记录
        $logData = array();
        $logData['user_id'] = $userInfo['user_id'];
        $logData['log_no'] = log_no($userInfo['user_id']);
        $logData['time'] = time();
        $logData['money'] = $data['money'];
        $logData['title'] = $data['title'];
        $logData['remark'] = $data['remark'];
        $logData['type'] = $data['type'];
        $logId = target('member/PointsLog')->add($logData);
        if(!$logId) {
            return $this->error('积分记录失败,请稍候再试!');
        }
        return $this->success($logId);
    }

    /**
     * 增加资金
     * @param $userId
     * @param $money
     * @param $payNo
     * @param $payName
     * @param string $remark
     * @param int $deduct
     * @return bool
     */
    public function incAccount($userId, $money, $payName, $payNo = '', $title = '', $remark = '', $deduct = 1) {
        return $this->account([
            'user_id' =>$userId,
            'money' => $money,
            'pay_no' => $payNo,
            'pay_name' => $payName,
            'type' => 1,
            'title' => $title,
            'deduct' => $deduct,
            'remark' => $remark
        ]);
    }

    /**
     * 减少资金
     * @param $userId
     * @param $money
     * @param $payNo
     * @param $payName
     * @param string $title
     * @param string $remark
     * @param int $deduct
     * @return bool
     */
    public function decAccount($userId, $money, $payName, $payNo = '', $title = '', $remark = '', $deduct = 1) {
        return $this->account([
            'user_id' =>$userId,
            'money' => $money,
            'pay_no' => $payNo,
            'pay_name' => $payName,
            'type' => 0,
            'title' => $title,
            'deduct' => $deduct,
            'remark' => $remark
        ]);
    }

    /**
     * 检测账户积分
     * @param $userId
     * @param $balance
     * @return bool
     */
    public function checkAccount($userId, $balance) {
        $model = target('member/PointsAccount');
        $userInfo = $model->getWhereInfo([
            'A.user_id' => $userId
        ]);
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
        $info = target('member/PointsAccount')->getWhereInfo(['A.user_id' => $userId]);
        return $info['money'];
    }

    /**
     * 汇率
     * @return int
     */
    public function erAccount() {
        return 1;
    }

}
