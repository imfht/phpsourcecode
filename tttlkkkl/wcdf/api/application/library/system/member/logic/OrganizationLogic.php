<?php

/**
 * Class OrganizationLogic
 * 企业组织架构
 *
 * @datetime: 2017/5/5 16:51
 * @author: lihs
 * @copyright: ec
 */

namespace system\member\logic;


use common\Nsq;

class OrganizationLogic {
    protected static $Obj;
    protected $DbModel;

    private function __construct() {

    }

    /**
     * @return OrganizationLogic
     */
    static public function getInstance() {
        if (!self::$Obj) {
            self::$Obj = new self();
        }
        return self::$Obj;
    }

    /**
     * 更新组织架构
     *
     * @param $data
     */
    public function put($data) {
        $msg = [
            'target' => 'member/organization/pull',
            'data'   => mt_rand(0, 1000)
        ];
        return Nsq::getInstance()->pub('nsq_common', $msg);
    }
}