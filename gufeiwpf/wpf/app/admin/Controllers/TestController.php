<?php
namespace Wpf\App\Admin\Controllers;
use Wpf\App\Admin\Models\AdminMenu;
class TestController extends \Wpf\App\Admin\Common\Controllers\CommonController{
    public function imgAction(){
        
    }
    
    public function editorAction(){
        
        if($this->request->isPost()){
            
            
            var_dump($_POST);
            exit;
            
        }else{
            
        
        
            $this->headercss
                ->addCss("theme/assets/global/plugins/bootstrap-summernote/summernote.css");
            
            $this->footerjs            
                ->addJs("theme/assets/global/plugins/bootstrap-summernote/summernote.min.js")
                ->addJs("theme/assets/global/plugins/bootstrap-summernote/lang/summernote-zh-CN.js");
            
        }
    }
    
    public function flushAction(){
              
        
        $fileFrontCache = new \Phalcon\Cache\Frontend\Data($this->config->cache_lifttime->toArray());
        $redisFrontCache = new \Phalcon\Cache\Frontend\Data($this->config->cache_lifttime->toArray());
        
        $cache = new \Phalcon\Cache\Backend\Redis($redisFrontCache, $this->config->redis->toArray());
        $cache->flush();
        
        $cache = new \Phalcon\Cache\Backend\File($fileFrontCache,$this->config->filecache->toArray());
        $cache->flush();
        
        
        $this->modelsCache->flush();
        
        
        $this->cache->flush();
        
        $this->setDbConfigData(true);
        
        
        
        
        if(function_exists("opcache_reset")){
            opcache_reset();
        }
        
        $this->success('更新成功');
    }
    
    public function mapAction(){
        
    }
    
    public function bootboxAction(){
        $this->footerjs->addJs("js/WPF_bootbox.js");
    }
    
    public function excelAction(){
        
        $do = $this->request->get("do",null,0);
        
        if($do){
            //vendor('PHPExcel.PHPExcel');
            //$objPHPExcel = new \PHPExcel();
            
            $loader = new \Phalcon\Loader();        
            $loader->registerDirs(
                array(
                    LIBS_PATH.'/PHPExcel/',
                )
            ,true);
            $loader->register();
            $objPHPExcel = new \PHPExcel();
            
            //$configmodel = M('config');
            $configmodel = new \Wpf\Common\Models\Config();
            $list = $configmodel->find()->toArray();
            
            $n = 1;
            foreach($list as $key=>$value){
                $num = 1000000;
                $k = 1;
                foreach($value as $v){
                    //$objPHPExcel->getActiveSheet()->setCellValue(chr($num+$k) . $key, $v);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($num+$k) . $n, $v);
                    $k++;
                }
                $n++;
                
            }
            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');
            
            
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            
            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="01simple.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            
            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0
            
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
    }
    
    public function emailAction(){
        
        if($this->request->isPost()){
            
            //以下是发邮件完整流程，自动触发的邮件通知，请自行封装
            
            //vendor('PHPMailer.PHPMailerAutoload');
            
            require LIBS_PATH.'/PHPMailer/PHPMailerAutoload.php';
            
            $loader = new \Phalcon\Loader();        
            //$loader->registerDirs(
//                array(
//                    LIBS_PATH.'/PHPExcel/',
//                )
//            ,true);
            
            //$loader->registerClasses(
//                array(
//                    "PHPMailer"         => LIBS_PATH.'/PHPMailer/PHPMailerAutoload.php',
//                )
//            ,true);
//            
//            $loader->register();
            
            $mail = new \PHPMailer;
            
            //$mail->SMTPDebug = 3;
            $mail->isSMTP(); 
            
            $mail->Host = $this->request->getPost("EMAIL_Host",null,$this->config->EMAIL_HOST);
            $mail->SMTPAuth = ($this->request->getPost("SMTPAuth",null,$this->config->EMAIL_SMTPAUTH)) ? true : false;
            $mail->Username = $this->request->getPost("Username",null,$this->config->EMAIL_USERNAME);
            $mail->Password = $this->request->getPost("Password",null,$this->config->EMAIL_PASSWORD);
            
            //var_dump($mail->Username);
            //var_dump($mail->Password);
            $mail->SMTPSecure = $this->request->getPost("SMTPSecure",null,$this->config->EMAIL_SMTPSECURE);
            
            $mail->Port = $this->request->getPost("Port",null,$this->config->EMAIL_PORT);
            
            $mail->From = $this->request->getPost("From",null,$this->config->EMAIL_FROM);
            $mail->FromName = $this->request->getPost("FromName",null,$this->config->EMAIL_FROMNAME);
            $mail->addAddress($this->request->getPost("addAddress",null,"service@asiacation.com"),$this->request->getPost("addAddressName",null,"service@asiacation.com"));
            
            //$mail->addAddress("jiaheng.wu@uthing.cn");
            
            $mail->isHTML($this->request->getPost("isHTML",null,$this->config->EMAIL_ISHTML)); 
            
            $mail->Subject = $this->request->getPost("Subject",null,"TEST");
            $mail->Body = $this->request->getPost("Body",null,"This is the HTML message body <b>in bold!</b>");
            if($mail->send()) {
                $this->success('发送成功');
            } else {
                //var_dump($mail->ErrorInfo);
                //exit;
                $this->error($mail->ErrorInfo);
            }
            
        }
        
    }
}