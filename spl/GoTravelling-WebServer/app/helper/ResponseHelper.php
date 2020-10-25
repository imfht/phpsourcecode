<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-13
 * Time: 下午7:16
 */

namespace Helper;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class ResponseHelper
{
    /**
     * 根据请求头部 'HTTP_ACCEPT':'application/json' 来判断是浏览器请求还是移动端
     *
     * @param Request $request
     * @param array $responses
     * @return mixed
     */
    public static function chooseResponse(Request $request, array $responses)
    {
        if( $request->wantsJson() ){
            return $responses['json'];
        } else {
            return $responses['base'];
        }
    }

    /**
     * 返回针对成功操作的响应，自适应移动端和浏览器
     *
     * @param Request $request
     * @param array $data
     * @param $templateName
     * @param int $code
     * @return mixed
     */
    public static function responseViewOrJson(Request $request, array $data, $templateName, $code = 200)
    {
        return static::chooseResponse($request, [
           'json' => response()->json($data, $code),
            'base' => response()->view($templateName, $data, $code)
        ]);
    }

    /**
     * 返回针对数据验证失败的响应，自适应移动端和浏览器
     *
     * @param Request $request
     * @param MessageBag $messageBag
     * @param int $code
     * @return mixed
     */
    public static function responseErrorMessagesOnViewOrJson(Request $request, MessageBag $messageBag, $code = 400)
    {
        $errorData['data'] = $request->all();
        $errorData['error'] = ValidateHelper::changeValidatorMessageToArray($messageBag);

        return static::chooseResponse($request, [
            'json' => response()->json($errorData, $code),
            'base' => \Redirect::back()->withInput($errorData['data'])->withErrors($errorData['error'])
        ]);
    }

    public static function responseErrorMessageOnJson(array $data, MessageBag $messageBag, $code = 400)
    {
        $errorData['data'] = $data;
        $errorData['error'] = ValidateHelper::changeValidatorMessageToArray($messageBag);
        return response()->json($errorData, $code);
    }
}