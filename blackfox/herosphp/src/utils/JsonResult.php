<?php
/**
 * json result vo
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */
namespace herosphp\utils;

use herosphp\string\StringUtils;

class JsonResult {

    const CODE_SUCCESS = "000";
    const CODE_FAIL = "001";

    /**
     * 数据
     * @var object
     */
    private $data;

    /**
     * 列表数据条数
     * @var int
     */
    private $total;

    /**
     * 当前数据页码
     * @var int
     */
    private $page;

    /**
     * 每页显示数据条数
     * @var int
     */
    private $pagesize;

    /**
     * 附带数据
     * @var mixed
     */
    private $extra;
    /**
     * 错误代码
     * @var string
     */
    private $code = self::CODE_SUCCESS;

    // 是否发送 json 头信息
    private $useJsonHeader = true;

    /**
     * 消息
     * @var string
     */
    private $message;

    /**
     * JsonResult constructor.
     * @param $code
     * @param $message
     */
    public function __construct($code, $message){
        $this->setCode($code);
        $this->setMessage($message);
    }

    /**
     * 创建 JsonResult 实例, 并输出
     * @param $code
     * @param $message
     * @param array $data
     * @return JsonResult
     */
    public static function instance($code, $message) {
        return new JsonResult($code, $message);
    }

    /**
     * 返回一个成功的 result vo
     * @param string $message
     * @return JsonResult
     */
    public static function success($message='操作成功') {
        return new JsonResult(self::CODE_SUCCESS, $message);
    }

    /**
     * 返回一个失败的 result vo
     * @param string $message
     * @return JsonResult
     */
    public static function fail($message='系统开了小差') {
        return new JsonResult(self::CODE_FAIL, $message);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return the $message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param object $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getPagesize()
    {
        return $this->pagesize;
    }

    /**
     * @param int $pagesize
     */
    public function setPagesize($pagesize)
    {
        $this->pagesize = $pagesize;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return boolean
     */
    public function isUseJsonHeader()
    {
        return $this->useJsonHeader;
    }

    /**
     * @param boolean $useJsonHeader
     */
    public function setUseJsonHeader($useJsonHeader)
    {
        $this->useJsonHeader = $useJsonHeader;
    }

    /**
     * 转换字符串
     * @return string
     */
    public function __toString()
    {
        return StringUtils::jsonEncode(array(
            'code'=>$this->getCode(),
            'success'=>$this->isSuccess(),
            'message'=>$this->getMessage(),
            'data'=>$this->getData(),
            'count'=>$this->getCount(),
            'page'=>$this->getPage(),
            'pagesize'=>$this->getPagesize(),
            'extra'=>$this->getExtra()));
    }

    /**
     * 以json格式输出
     */
    public function output() {
        if ($this->useJsonHeader) {
            header('Content-type: application/json;charset=utf-8');
        }
        echo $this;
        die();
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->code == self::CODE_SUCCESS;
    }
}