<?php

namespace Addons\UrlToBaidu;
use Common\Controller\Addon;

/**
 * 提交链接给百度插件
 * @author B-fox
 */

    class UrlToBaiduAddon extends Addon{

        public $info = array(
            'name'=>'UrlToBaidu',
            'title'=>'提交链接给百度',
            'description'=>'将链接推送给百度，以保证新链接可以及时被百度收录。 请确保网站网站已经设置且正确，并经过百度站长平台认证。',
            'status'=>1,
            'author'=>'翟小斐',
            'version'=>'1.0'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的documentSaveComplete钩子方法
        public function documentSaveComplete($param){
            $web_url = C('WEB_SITE_URL');
            if(empty($web_url)){
                session('url_to_baidu_msg','|提交到百度失败，请设置网站网站');
                return ;
            }
            if(empty($param['data_id'])){
                if(!empty($param['article_id'])){
                    $url = U('Home/article/detail',array('id'=>$param['article_id']),true,true);
                    $url  = str_replace('admin.php', 'index.php', $url);
                    $urls = array(
                        $url
                    );
                    $config = $this->getConfig();
                    $api = 'http://data.zz.baidu.com/urls?site='.trim($web_url).'&token='.trim($config['baidu_token']);
                    if(empty($config['baidu_token'])){
                        session('url_to_baidu_msg','|提交到百度失败，token为空');
                        return ;
                    }
                    $ch = curl_init();
                    $options =  array(
                        CURLOPT_URL => $api,
                        CURLOPT_POST => true,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POSTFIELDS => implode("\n", $urls),
                        CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
                    );
                    curl_setopt_array($ch, $options);
                    $result = curl_exec($ch);
                    $json = json_decode($result);
   
                    if(isset($json->error)){
                       session('url_to_baidu_msg','|error:".$json->error."|"."message:".$json->message."');  
                    }
                    
                }
            }
            
        }

    }