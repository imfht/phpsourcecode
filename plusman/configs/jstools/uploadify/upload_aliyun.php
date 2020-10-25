<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class upload extends MY_Controller {
        function __construct(){
            parent::__construct();
        }


        function index(){

        }

        function upload_aliyun(){
            if(isset($_FILES['Filedata'])){
                $file = $_FILES['Filedata'];
                require_once(APPPATH.'third_party/uploader/uploader.class.php');
                $uploader = new uploader();
                $up_res = $uploader->upload_aliyun($file['name'],$file['tmp_name']);
                if($up_res['error'] == 0){
                    $json = array(
                        'error' => 0,
                        'fileurl' => $up_res['url'],
                    );
                }else{
                    $json = array(
                        'error' => $up_res['error'],
                        'message' => $up_res['message'],
                    );
                }
            }
            echo json_encode($json);
        }

    }
?>