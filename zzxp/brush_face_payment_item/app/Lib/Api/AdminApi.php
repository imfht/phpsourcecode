<?php 
namespace App\Lib\Api;


class AdminApi extends BaseApi
{
    protected $path = '';
    public $system = 'adminapi';

    function __construct($setting = [])
    {
        parent::__construct($setting);
    }

    public function addGwBanner($data){

        $url = $this->apiUrl('gw-banner/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getGwBanner($data){

        $url = $this->apiUrl('gw-banner/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    //获取后台操作日志
    public function getSystemLog($data){
        $url = $this->apiUrl('system-log/get');
        //return $url;
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    //写入后台操作日志
    public function addSystemLog($data){
        $url = $this->apiUrl('system-log/add');
        //return $url;
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateGwBanner($data){

        $url = $this->apiUrl('gw-banner/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delGwBanner($data){

        $url = $this->apiUrl('gw-banner/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addGwNews($data){

        $url = $this->apiUrl('gw-news/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getGwNews($data){

        $url = $this->apiUrl('gw-news/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateGwNews($data){

        $url = $this->apiUrl('gw-news/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delGwNews($data){

        $url = $this->apiUrl('gw-news/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addSystemMenu($data){

        $url = $this->apiUrl('system-menu/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getSystemMenu($data){

        $url = $this->apiUrl('system-menu/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateSystemMenu($data){

        $url = $this->apiUrl('system-menu/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delSystemMenu($data){

        $url = $this->apiUrl('system-menu/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addSystemRole($data){

        $url = $this->apiUrl('system-role/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getSystemRole($data){

        $url = $this->apiUrl('system-role/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateSystemRole($data){

        $url = $this->apiUrl('system-role/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delSystemRole($data){

        $url = $this->apiUrl('system-role/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addSystemUser($data){

        $url = $this->apiUrl('system-user/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getSystemUser($data){
		//print_r($data);
        $url = $this->apiUrl('system-user/get');
        $res = $this->easyget($url, $data);
        //var_dump($res);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateSystemUser($data){

        $url = $this->apiUrl('system-user/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delSystemUser($data){

        $url = $this->apiUrl('system-user/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function addSystemMail($data){

        $url = $this->apiUrl('system-mail/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getSystemMail($data){
        //print_r($data);
        $url = $this->apiUrl('system-mail/get');
        $res = $this->easyget($url, $data);
        //var_dump($res);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateSystemMail($data){

        $url = $this->apiUrl('system-mail/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delSystemMail($data){

        $url = $this->apiUrl('system-mail/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function getProvincialPermissions($data){
        //print_r($data);
        $url = $this->apiUrl('provincial-permissions/get');
        $res = $this->easyget($url, $data);
        //var_dump($res);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function addProvincialPermissions($data){
        //print_r($data);
        $url = $this->apiUrl('provincial-permissions/add');
        $res = $this->easyget($url, $data);
        //var_dump($res);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateProvincialPermissions($data){
        //print_r($data);
        $url = $this->apiUrl('provincial-permissions/update');
        $res = $this->easyget($url, $data);
        //var_dump($res);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function getBrand($data){

        $url = $this->apiurl('brand/get');//echo $url;
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addBrand($data){

        $url = $this->apiurl('brand/add');//echo $url;
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function updateBrand($data){

        $url = $this->apiurl('brand/update');//echo $url;
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function getBrandArea($data){

        $url = $this->apiurl('brand-area/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addBrandArea($data){

        $url = $this->apiurl('brand-area/add');//echo $url;
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function updateBrandArea($data){

        $url = $this->apiurl('brand-area/update');//echo $url;
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    /*public function getBrand($data){

        $url = $this->apiurl('brand/get');//echo $url;
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }*/

}