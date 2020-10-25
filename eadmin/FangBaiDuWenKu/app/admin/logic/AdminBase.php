<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

use app\common\logic\LogicBase;

/**
 * Admin基础逻辑
 */
class AdminBase extends LogicBase
{

    /**
     * 权限检测
     */
    public function authCheck($url = '', $url_list = [])
    {

        $pass_data = [RESULT_SUCCESS, '权限检查通过'];
        
        $allow_url = config('allow_url');
        
        $allow_url_list  = parse_config_attr($allow_url);
       
        if (IS_ROOT) : return $pass_data; endif;
        
        if (!empty($allow_url_list)) {
            
            foreach ($allow_url_list as $v) {
            	
            
                if (strpos(strtolower($url), strtolower($v)) !== false) : return $pass_data; endif;
            }
        }
       
        
      
        $result = in_array(strtolower($url), $url_list) ? true : false;
       
        return $result ? $pass_data : [RESULT_ERROR, '未授权操作'];
    }
    
    /**
     * 获取过滤后的菜单树
     */
    public function getMenuTree($menu_list = [], $url_list = [])
    {
    	$menu=array();
        foreach ($menu_list as $key => $menu_info) {
        	
            list($status, $message) = $this->authCheck(MODULE_NAME . SYS_DSS . $menu_info['url'], $url_list);
            
            [$message];
            
            if ((!IS_ROOT && RESULT_ERROR == $status) || !empty($menu_info['is_hide']))
            {
            	 unset($menu_list[$key]);
            }else{
            	$menu_list[$key]['href']=url($menu_info['url']);
            	$menu_list[$key]['title']=	$menu_info['name'];
            	//$adminliststr[$key]['icon']=	$menu_info['icon'];
            	
            	$menu_list[$key]['spread']=false;
            	//$adminliststr[$key]['pid']=$menu_info['pid'];
            	//$adminliststr[$key]['id']=$menu_info['id'];
            	//$adminliststr[$key]['is_hide']=$menu_info['is_hide'];
            	
            	if(empty($menu)){
            		$menu_list[$key]['spread']=true;
            	}
            	$menu[]=$menu_list[$key];
            }
        
            
        
        }
     
        return $this->getListTree($menu_list);
    }
    
    /**
     * 获取列表树结构
     */
    public function getListTree($list = [])
    {
       
        return list_to_tree(array_values($list), 'id', 'pid', 'children');
    }
    
    /**
     * 过滤页面内容
     */
    public function filter($content = '', $url_list = [])
    {
        
        $expression_ob_link = '%<ob_link.*?>(.*?)</ob_link>%si';

        $results = [];
        
        preg_match_all($expression_ob_link, $content, $results);
        
        $expression_href='/href=\"([^(\}>)]+)\"/';
        
        
        
        
        
        $expression_url='/data-url=\"([^(\}>)]+)\"/';
       
        foreach ($results[1] as $a)
        {
        	
            $href_results = []; 
            
           
          
            	preg_match_all($expression_url, $a, $href_results);
          
          
            empty($href_results[0]) && empty($href_results[1]) && preg_match_all($expression_url, $a, $href_results);
           
            $ls=explode($_SERVER['SCRIPT_NAME'], $href_results[1][0]);
            $url_array=explode(SYS_DSS, $ls[1]);
          
            $url_array = explode('.', $url_array[2]);
  
            $check_url = MODULE_NAME . SYS_DSS . strtolower(CONTROLLER_NAME) . SYS_DSS . $url_array[0];
            
            $result = $this->authCheck($check_url, $url_list);
          
            $result[0] != RESULT_SUCCESS && $content = str_replace($a, '', $content); 
           
            
        }
        
        return $content;
    }
    public function getactiveurl(){
    	 
    	$curl = curl_init();
    	 
    	 
    	 
    	curl_setopt($curl, CURLOPT_URL, "http://appapi.imzaker.com/api.php/common/checkweburl");
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl, CURLOPT_POST, true);
    
    	$token=sha1('EasySNS' . date("Ymd") . 'l2V|gfZp{8`;jzR~6Y1_');
    	 
    	curl_setopt($curl, CURLOPT_POSTFIELDS, [
    	'access_token'=>$token,
    	'url'=>$_SERVER["SERVER_NAME"],
    	]);
    
    	 
    	 
    	$result = curl_exec($curl);
    	 
    	curl_close($curl);
    	return $result;
    	 
    }
    /**
     * 获取首页数据
     */
    public function getIndexData()
    {
        
        $query = new \think\db\Query();
        
        $system_info_mysql = $query->query("select version() as v;");
        
        $dodata=$this->getactiveurl();
        
        // 系统信息
        $data['ob_version']     = SYS_VERSION;
        $data['think_version']  = THINK_VERSION;
        $data['os']             = PHP_OS;
        $data['software']       = $_SERVER['SERVER_SOFTWARE'];
        $data['mysql_version']  = $system_info_mysql[0]['v'];
        $data['upload_max']     = ini_get('upload_max_filesize');
        $data['php_version']    = PHP_VERSION;
        
        // 产品信息
        $data['product_name']   = '仿百度文库--By ESPHP框架';
        $data['author']         = 'Zaker';
        $data['website']        = 'www.imzaker.com';
        $data['qun']            = '475030585';
        $data['document']       = '制作中...';
       
        
     
        	$data['hit']            =0;
    
        	$cacdata = json_decode($dodata,true);
        	if($cacdata['code']>0){
        		$data['do']   =array( "status" => 0,"downstatus" =>0,"ver" => "1.1");
        	}else{
        		$data['do']   =$cacdata['data'];
        		
        		if($data['do']['status']!=1){
        			$m=  file_get_contents('./template/'.config('site_tpl').'/PC/Public/footer.html');
        			if(strpos($m,'imzaker.com')!== false){
        		
        			}else{
        				file_put_contents('./template/'.config('site_tpl').'/PC/Public/footer.html','<div id="ft"><div class="footer"><p>Copyright © 2017 imzaker.com 版权所有  {$Think.CONFIG.web_site_icp}</p>{$Think.CONFIG.web_site_footer|html_entity_decode}</div></div>');
        			}
        			 
        		}
        		
        		
        	}

        return $data;
    }
    
}
