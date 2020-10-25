<?php

/**
 * 银行管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class PayBankModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'bank_id',
        'validate' => [
            'name' => [
                'empty' => ['', '请输入银行名称!', 'must', 'all'],
                'unique' => ['', '已存在相同的银行!', 'value', 'all'],
            ],
            'label' => [
                'empty' => ['', '请输入银行标识!', 'must', 'all'],
                'unique' => ['', '已存在相同的标识!', 'value', 'all'],
            ],
        ],
    ];

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = parent::loadList($where, $limit, 'bank_id asc');
        foreach ($list as $key => $vo) {
            $list[$key]['logo'] = $vo['logo'] ? $vo['logo'] : (ROOT_URL . '/public/member/images/blank/'.$vo['label'].'.png');
        }
        return $list;
    }


    public $cardType = [
        'CC' => '信用卡',
        'DC' => '储蓄卡',
        'SCC' => '准贷记卡',
        'PC' => '预付费卡',
    ];

    public function getType($label = '') {
        if(empty($label)) {
            return $this->cardType;
        }
        $name = $this->cardType[$label];
        return $name ? $name : '银行卡';
    }

    public function bankInfo($cardNum) {
        $result = \dux\lib\Http::curlGet("https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo={$cardNum}&cardBinCheck=true");
        if(empty($result)) {
            $this->error = '银行信息获取失败!';
        } 
        $result = json_decode($result);
        if (!$result->validated) {
            $this->error = '卡号输入错误或暂不支持!';
            return false;
        } else {
            $info = $this->getWhereInfo([
                'label' => $result->bank
            ]);
            if(empty($info)) {
                $this->error = '暂不支持该行银行卡!';
                return false;
            }

            $bankInfo = [
                'label' => $info['label'],
                'name' => $info['name'],
                'image' => "https://apimg.alipay.com/combo.png?d=cashier&t={$info['label']}",
                'type' => $result->cardType,
                'type_name' => $this->cardType[$result->cardType],
                'color' => $info['color']
            ];
        }
        return $bankInfo;
    }



}