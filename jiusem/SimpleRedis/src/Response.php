<?php namespace Crazymus\SimpleRedis;

/*
 * This file is part of the SimpleRedis package.
 *
 * (c) Jingzhou Guo <crazymus@foxmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Response
{
    /**
     * Redis响应内容的第一行，可以认为是状态行
     * 类似于Http协议的响应头
     *
     * @var string
     */
    protected $header;

    /**
     * Redis响应内容的第二行，可以认为是正文行
     * 类似于Http协议的响应内容
     *
     * @var string
     */
    protected $body;

    /**
     * 用来标识查询成功与否
     *
     * @var bool
     */
    protected $status;

    /**
     * 初步解析的响应结果
     *
     * @var string
     */
    protected $data;

    /**
     * Redis返回的原始响应内容
     *
     * @var string
     */
    protected $rawResponseData;

    public function __construct($rawResponseData)
    {
        $this->rawResponseData = $rawResponseData;
        $array = explode(PHP_EOL, $this->rawResponseData);
        if (empty($array)) {
            throw new \RuntimeException('解析查询结果失败', ErrorCode::DEFAULT_CODE);
        }

        $this->header = trim($array[0] ?? '');
        unset($array[0]);
        $this->body   = implode('', $array);
        var_dump($this->body);

        $this->parse();
    }

    /**
     * 解析Redis的响应头和响应正文
     */
    protected function parse()
    {
        $status = substr($this->header, 0, 1);
        if ($status == '+') {
            $data = substr($this->header, 1);
            $this->status = true;
            $this->data = $data;
        } elseif ($status == '-') {
            $data = substr($this->header, 1);
            $this->status = false;
            $this->data = $data;
        } elseif ($status == '$') {
            $len = (int) substr($this->header, 1);
            $this->status = $len > 0 ? true : false;
            $this->data = $this->body;
        } else {
            $this->status = false;
            $this->data = null;
        }
    }

    /**
     * 获取查询状态
     * true表示查询成功 | false表示查询失败
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 获取查询结果
     * getStatus方法结果为true，该方法才是有意义的
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取Redis返回的原始响应内容
     *
     * @return string
     */
    public function getRawData()
    {
        return $this->rawResponseData;
    }
}
