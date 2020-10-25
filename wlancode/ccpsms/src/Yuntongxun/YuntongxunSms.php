<?php

namespace Yuntongxun;


use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class YuntongxunSms
{
    use YuntongxunApi;

    /**
     * 时间字符串，格式为 yyyyMMddHHmmss
     * @var string
     */
    public $time;

    public function __construct()
    {
        $this->time   = Carbon::now()->format('YmdHis');
        $this->config = Config::get('yuntongxunsms');
    }

    /**
     * @var int $templateId
     * @param array $param
     * @param array $mobiles
     * @return boolen
     *
     * @throws \Exception
     *
     */
    public function templateSMS(int $templateId, array $param, array $mobiles)
    {
        if (empty($mobiles)) {
            throw new \Exception('手机号码不能为空', 5001);
        }

        $_mobileStr = implode(',', $mobiles);
        $data = [
            'appId' => $this->config['appId'],
            'datas' => $param,
            'templateId' => $templateId,
            'to' => $_mobileStr
        ];

        return $this->responsePost('SMS/TemplateSMS', $data);
    }


}