<?php

namespace Controller\Microsrv;

use SCH60\Kernel\KernelHelper;
use MicrosrvSDK\Base\Request;
use MicrosrvSDK\Integrate\FileRequestLogger;

trait DemoForGetMicrosrvRequestControllerTrait{
    
    /**
     * 
     * @var \MicrosrvSDK\Base\Request
     */
    protected $microsrvRequest;
    
    /**
     * 
     * @return \MicrosrvSDK\Base\Request
     */
    public function getMicrosrvRequest(){
        if(null !== $this->microsrvRequest){
            return $this->microsrvRequest;
        }
        
        $this->microsrvRequest = new Request(array(
            'gatewayUrl' => KernelHelper::config("MICROSRV_GATEWAYURL"),
            'appId' => KernelHelper::config("MICROSRV_APPID"),
            'appSecret' => KernelHelper::config("MICROSRV_APPSECRET"),
        ));
        
        $fileRequestLogger = new FileRequestLogger(array(
            'logDir' => KernelHelper::config("MICROSRV_FILE_LOG_DIR"),
        ));
        
        $this->microsrvRequest->setRequestLogger('filerequestloggerdemo', $fileRequestLogger);
        
        return $this->microsrvRequest;
    }
    
}