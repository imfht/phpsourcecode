<?php
/**
 * 授权基类
 * Date: 16-10-10
 * Time: 下午9:42
 * author :李华 yehong0000@163.com
 */

namespace system\auth;


class Base {
    static private $CID = 1;
    static private $wxCorpID = '';
    static private $company;

    static public function getCompanyInfo($cid) {
        if (!self::$company) {
            self::$CID = $cid ?: self::$CID;
            $where['id'] = self::$CID;
            self::$company = db('company')->where($where)->find();
        }
        return self::$company;
    }

    /**
     * 根据微信标识获取企业信息
     *
     * @param $wxCorpID
     * @return array|false|\PDOStatement|string|\think\Model
     */
    static public function getCompanyInfoByWxCorpId($wxCorpID) {
        if (!self::$company) {
            self::$wxCorpID = $wxCorpID ?: self::$wxCorpID;
            $where['corpid'] = self::$wxCorpID;
            self::$company = db('company')->where($where)->find();
        }
        return self::$company;
    }
}