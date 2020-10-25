<?php

class utility{
    private $ret;
    private $isSEO;
    private $SEOHtml;
    private $apiClosed;
    public function __construct(){
        $this->ret = array();
        $this->setSuccessFalse();
        $this->isSEO = false;
        $this->apiClosed = false;
    }
    public function closeApi(){
        $this->apiClosed = true;
    }
    public function setSuccessTrue(){
        $this->ret['success'] = true;
    }
    public function setSuccessFalse(){
        $this->ret['success'] = false;
    }
    public function addError($error){
        $this->ret['errors'][] = $error;
    }
    public function setItem($key,$item){
        $this->ret[$key] = $item;
    }
    public function  SEOHtmlAppend($str){
        $this->SEOHtml = $this->SEOHtml . $str;
    }
    public function  openSEO($title){
        $this->isSEO = true;
        $this->SEOHtml = "";
        $this->SEOHtmlAppend('<!DOCTYPE html><html lang="zh"><head><meta charset="utf-8">');
        $this->SEOHtmlAppend('<title>'.$title .'</title>');
        $this->SEOHtmlAppend('</head><body>');
    }
    public function SEOLink($name,$url){
        $this->SEOHtmlAppend('<a href="'.SEO_LINK_PREFIX.$url.'">'.$name.'</a>');
    }
    public function  SEODiv(){
        $this->SEOHtmlAppend('<div>');
    }
    public function SEODivEnd(){
        $this->SEOHtmlAppend('</div>');
    }
    public function SEOSpan(){
        $this->SEOHtmlAppend('<span>');
    }
    public function  SEOSpanEnd(){
        $this->SEOHtmlAppend('</span>');
    }
    public function  SEOImg($alt,$url){
        $this->SEOHtmlAppend('<img src="'.$url.'" alt="'.$alt.'">');
    }
    public function  SEOH1($text){
        $this->SEOHtmlAppend('<h1>'.$text.'</h1>');
    }
    public function  finish(){
        if(!$this->isSEO ){
            if(!$this->apiClosed){
                echo json_encode($this->ret);
            }

        }
        else{
            $this->SEOHtmlAppend("</body></html>");
            echo $this->SEOHtml;
        }
    }

    public function  getObjectMap($model){
        $map = array();
        foreach($model as $key => $value){
            $map[$key] = $value;
        }
        return $map;
    }
    public function addFiles2Array(&$obj,$field_name,$files,$urlGenerator){
        $obj[$field_name] = array();
        foreach($files as $file){
            $obj[$field_name][] = array(
                'upload_file_name' => $file->upload_file_name,
                'url' => $urlGenerator($file->upload_file_name)
            );
        }
    }
    public function  getDBResultArrays(&$objs){
        $t = array();
        foreach($objs as $obj){
            $t[] = $obj->toArray();
        }
        return $t;
    }
    public function getDBColumns($obj,$except = array()){
        $arr = array();
        $map = $obj->columnMap();
        foreach($map as $key => $value){
            $b = false;
            foreach($except as $e){
                if($key == $e){
                    $b = true;
                    break;
                }
            }
            if($b == true){
                continue;
            }
            $arr[] = $key;
        }
        return $arr;
    }
    public function getPostData($params){
        $json = getPostJsonObject();
        if(!isset($json)){
            $this->addError(ERROR_JSON_INVILID);
            return false;
        }
        foreach($params as $param){
            if(!isset($json->$param)){
                $this->addError(ERROR_JSON_HALFBAKED);
                return false;
            }
        }
        return $json;
    }

    public function addDBRecord($obj,$params,$json){
        if($json === false){
            return false;
        }
        foreach($params as $param){
            $obj->$param = $json->$param;
        }
        if($obj->create()){
            return true;
        }
        else{
            $this->addError(ERROR_EXECUTE_FAIL);
            return false;
        }
    }
    public function editDBRecord($obj,$params,$json,$exceptId=true){
        if($json === false){
            return false;
        }
        $arr = array();
        foreach($params as $param){
            if($exceptId && $param == 'id'){
                continue;
            }
            $arr[$param] = $json->$param;
        }
        if($obj->save($arr)){
            return true;
        }
        else{
            $this->addError(ERROR_EXECUTE_FAIL);
            return false;
        }
    }

}
