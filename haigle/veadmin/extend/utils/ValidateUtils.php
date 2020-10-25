<?php
namespace utils;


class ValidateUtils
{

    /**
     * 验证信息
     * return 3手机号,2邮箱,1登录名
     */
    public function usernameType($username)
    {
        // 邮箱验证
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
        preg_match($pattern, $username, $matches);
        if($matches) {
            return 2;
        }

        // 验证手机号码
        // 移动号码段:139、138、137、136、135、134、150、151、152、157、158、159、182、183、187、188、147
        // 联通号码段:130、131、132、136、185、186、145
        // 电信号码段:133、153、180、189
        // 小米移动号码段：171
        // 其他：177
        preg_match_all("/13[0123569]{1}\d{8}|14[57]\d{8}|15[01235689]\d{8}|17[17]\d{8}|18[01236789]\d{8}/", $username, $array);
        if (strlen($username) == "11" && $array) {
            return 3;
        }

        return 1;
    }

}