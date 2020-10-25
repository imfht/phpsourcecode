<?php
/**
 * 前台基类
 *
 * 用于文件，图片上传
 *
 * @author		黑冰(001.black.ice@gmail.com)
 * @copyright	(c) 2012
 * @version		$Id$
 * @package		com.modules
 * @since		v0.1
 */

	// 后台控制类
	class MyPageController extends MyController{
        protected  $_isWap = FALSE;
		
		// 初始化

		public function init(){
            Yii::app()->setComponents(array(
                                            'user'    => array('stateKeyPrefix' => 'patabom.com',
                                                                'identityCookie'=>array('domain'=>'.patabom.com'),
                                                              ),
                                            'session' => array('cookieParams' => array('domain' => '.patabom.com'),
                                                              ),
                                           )
                                     );
			parent::init();
            // file cache
            $this->createStaticCache();
            //check is or phone pc
            if($this->checkWap())
                $this->_isWap = TRUE;
		}

        public function createStaticCache($prefix=''){
            $host_url = Yii::app()->request->hostInfo;
            if( preg_match('#//(.*?)$#i',$host_url,$result) && !empty($result[1]) ) $host_url = $result[1];
            $prefix.= ':'.$host_url.'/';
            //有post跳过
            if(Yii::app()->request->isPostRequest) return;
            //获取解析后的url
            $route = Yii::app()->getUrlManager()->parseUrl(Yii::app()->getRequest());
            //获取controller
            $controller = Yii::app()->getController();
            //获取actionID
            if( preg_match('#^'.$controller->id.'/([^/]+)#i',$route,$result) )
                $actionID = $result[1];
            else
                $actionID = $controller->defaultAction;
            $route = $controller->id.'/'.$actionID;
            $url = strtolower( trim( Yii::app()->createUrl($route,$_GET) ,'/') );
            //判断是否需要静态化
            if( $this->isRouteDynamic($host_url,$url) ){
                header('cache:no');
            }else{
                //清缓存
                if($_GET['flush']==='true'){
                    if( preg_match('#(.*?)/flush/true#i',$url,$result) ){
                        $url = $result[1];
                        $cacheKey = $prefix.$url;
                        Yii::app()->cache->delete($cacheKey);
                        MyFunction::funAlert('清楚缓存成功!!',Yii::app()->createUrl($url));
                        die();
                    }
                }
                $cacheKey = $prefix.$url;
                $value = Yii::app()->cache->get($cacheKey);
                //创建缓存
                if($value===false){//缓存失效则创建缓存
                    ob_start();
                    $controller->run($actionID);
                    $value = ob_get_contents();
                    ob_end_clean();
                    Yii::app()->cache->set($cacheKey,$value,Yii::app()->params['cache_timeout']);
                }
                //输出静态页
                header( 'cache:yes');
                header( 'cacheKey:'.$cacheKey);
                echo $value;
                Yii::app()->end();
            }
        }
        
        //判断是否是动态url
        public function isRouteDynamic($host_url,$url){
            $dynamicRule = Yii::app()->params['dynamicRule'];
            if( isset($dynamicRule['all']) ){
                foreach((array)$dynamicRule['all'] as $pattern){
                    if( preg_match(strtolower($pattern),$url) )
                        return true;
                }
            }
            if( isset($dynamicRule[$host_url]) ){
                foreach((array)$dynamicRule[$host_url] as $pattern){
                    if( preg_match(strtolower($pattern),$url) )
                        return true;
                }
            }
            return false;
        }

        //check pc or phone
        public function checkWap(){
            // 先检查是否为wap代理，准确度高
            if(stristr($_SERVER['HTTP_VIA'],"wap")){
                return true;
            }
            // 检查浏览器是否接受 WML.
            elseif(strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0){
                return true;
            }
            //检查USER_AGENT
            elseif(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])){
                return true;
            }
            else{
                return false;
            }
        }
	}
?>
