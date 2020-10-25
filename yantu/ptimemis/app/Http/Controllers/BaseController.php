<?php

namespace App\Http\Controllers;

use DB;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    public function jsonResponse($error,$result="",$message=""){
        
        if($message == "paramError") $message = $result->first();

        return ["error"=>$error,"result"=>$result,"message"=>$message];
    }

    public function tableConfig($table){
        return require "../storage/app/config/$table.php";
    }
}