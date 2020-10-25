<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-12
 * Time: 下午8:44
 */
namespace Helper;

use Illuminate\Support\MessageBag;

class ValidateHelper
{
    /**
     * @param \Illuminate\Support\MessageBag $messages
     * @param null $keys
     * @param string $separator
     * @return array
     */
    public static function changeValidatorMessageToArray(MessageBag $messages, $keys = null, $separator = ',')
    {
        $returnMessages = [];
        $messagesArray = $messages->toArray();

        if( ! is_array($keys) ){
            $keys = array_keys($messagesArray);
        }

        foreach( $keys as $currentKey ) {
            $returnMessages[$currentKey]  = implode( $separator , $messagesArray[$currentKey]);
        }

        return $returnMessages;
    }

    /**
     * 提取Validator实例中的错误信息，将其转换为字符串形式以方便发送给前端.
     *
     * @param \Illuminate\Support\MessageBag $messages 错误信息的实例
     * @param null $keys 指定所提取的字段信息，如不指定则返回全部
     * @param string $separator 信息的分隔符
     * @return string
     */
    public static function changeValidatorMessageToString(\Illuminate\Support\MessageBag $messages, $keys = null, $separator = ',')
    {
        $resp = '';
        $messagesArray = $messages->toArray();

        if( ! is_array($keys) ){
            $keys = array_keys($messagesArray);
        }

        foreach($keys as $currentKey){
            $resp .= implode($separator, $messagesArray[$currentKey] ). ' '. $separator. ' ';
        }

        return rtrim($resp, $separator.' ');
    }
}