<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/28
 * Time: 12:06
 */

namespace Yan\Core\Compo;

class Result implements ResultInterface
{
    protected $code;
    protected $message;
    protected $data;

    public function __construct(int $code = 0, string $message = '', array $data = [])
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    function getCode(): int
    {
        return $this->code;
    }

    function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return ['code' => $this->code, 'message' => $this->message, 'data' => $this->data];
    }
}