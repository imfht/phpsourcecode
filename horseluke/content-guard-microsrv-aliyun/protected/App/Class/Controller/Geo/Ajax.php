<?php

namespace Controller\Geo;

use SCH60\Kernel\BaseController;

class Ajax extends BaseController{
    
    public function actionSearchCity(){
        
        $citys = $this->request->input($_POST, 'citys');
        
        if(strlen($citys) > 3000){
            return $this->response->json(false, 1, "查询数据太长");
        }
        
        $citys = explode(',', $citys);
        
        $cityCount = count($citys);
        if($cityCount > 250){
            return $this->response->json(false, 1, "一次不能超过250个城市");
        }
        
        $cityList = require D_APP_DIR. '/Config/Geodb/AmapChinaCity.php';
        
        $result = array(
            'citys' => array(),
            'miss' => array(),
        );
        
        foreach($citys as $city){
            
            $miss = true;
            
            foreach($cityList as $cityDefine){
                if($city == $cityDefine['city']){
                    $miss = false;
                    $result['citys'][] = $cityDefine;
                    break;
                }
                
                if($city == $cityDefine['name']){
                    $miss = false;
                    $result['citys'][] = $cityDefine;
                    break;
                }
                
            }
            
            if($miss){
                $result['miss'][] = $city;
            }
            
        }
        
        return $this->response->json($result);
        
    }
    
    
    public function actionSearchCountry(){
        $countrys = $this->request->input($_POST, 'countrys');
        
        if(strlen($countrys) > 3000){
            return $this->response->json(false, 1, "查询数据太长");
        }
        
        $countrys = explode(',', $countrys);
        
        $count = count($countrys);
        if($count > 250){
            return $this->response->json(false, 1, "一次不能超过250个国家");
        }
        
        $countryList = require D_APP_DIR. '/Config/Geodb/WorldCountry.php';
        
        $result = array(
            'countrys' => array(),
            'miss' => array(),
        );
        
        foreach($countrys as $country){
        
            $miss = true;
        
            foreach($countryList as $defineRow){
                if($country == $defineRow['country_zh_cn']){
                    $miss = false;
                    $result['countrys'][] = $defineRow;
                    break;
                }
        
            }
        
            if($miss){
                $result['miss'][] = $country;
            }
        
        }
        
        return $this->response->json($result);
        
    }
    
}