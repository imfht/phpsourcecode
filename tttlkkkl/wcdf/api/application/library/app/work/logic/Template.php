<?php
/**
 * 考勤模板
 * Date: 17-5-6
 * Time: 下午11:46
 * author :李华 yehong0000@163.com
 */

namespace app\work\logic;


class Template
{
    protected static $Obj;
    /**
     * 数据模型
     * @var \app\work\model\Work
     */
    protected        $Model;

    private function __construct()
    {
        $this->Model = \app\work\model\WorkRecord::getInstance();
    }


    /**
     * @return WorkRecord
     */
    static public function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self;
        }
        return self::$Obj;
    }
}