<?php
/**
 *
 * @author Hades
 */
interface IWxLogin {
    public function getNewToken();
    public function isExprise($token);
    public function isLogined($token);
    public function getUserInfo($token);
    public function setUserInfo($token,$userinfo);
}
