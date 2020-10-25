<?php
/**
 * Created by PhpStorm.
 * User: xiangdong
 * Date: 15/11/23
 * Time: 下午2:25
 */

namespace App\Controller;

use App\Model\Interfaces;
use Swoole;

class Api extends Swoole\Controller
{
    function getInterface(){
        if(!in_array($_SERVER["REMOTE_ADDR"],$this->swoole->config["common"]["sao_service"])){
            exit("you are not my service !");
        }
        $interface_name=getRequest("name");
        $interfaces= new Interfaces(\Swoole::$php,"status_center");
        $params=array(
            "name"=>$interface_name
        );
        $interface=$interfaces->exists($params);
        if($interface){
            $data = $interfaces->get($interface_name,"name")->get();
            return $data["id"];
        }else{
            $data=[
                'name'=>$interface_name,
                'create_time'=>time()
            ];
            $id=$interfaces->put($data);
            return $id;
        }

    }

}