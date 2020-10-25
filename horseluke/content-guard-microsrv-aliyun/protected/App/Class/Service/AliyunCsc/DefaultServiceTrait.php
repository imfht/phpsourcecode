<?php

namespace Service\AliyunCsc;

trait DefaultServiceTrait{
    
    protected $lastResponse;
    
    protected $lastError;
    
    public function setError($error){
        $this->lastError = $error;
        return false;
    }
    
    public function getLastError(){
        return $this->lastError;
    }
    
    public function getLastResponse(){
        return $this->lastResponse;
    }
    
}