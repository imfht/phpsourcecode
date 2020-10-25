<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

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
        
        $s_url = strtolower($url);
        
        if (!empty($allow_url_list)) {
            
            foreach ($allow_url_list as $v) {
                
                if (strpos($s_url, strtolower($v)) !== false) : return $pass_data; endif;
            }
        }
        
        $result = in_array($s_url, array_map("strtolower", $url_list)) ? true : false;
        
        !('admin/index/index' == $s_url && !$result) ?: clear_login_session();
        
        return $result ? $pass_data : [RESULT_ERROR, '未授权操作'];
    }
    
    /**
     * 获取过滤后的菜单树
     */
    public function getMenuTree($menu_list = [], $url_list = [])
    {
        
        foreach ($menu_list as $key => $menu_info) {
            
            list($status, $message) = $this->authCheck(strtolower(MODULE_NAME . SYS_DS_PROS . $menu_info['url']), $url_list);
            
            [$message];
            
            if ((!IS_ROOT && RESULT_ERROR == $status) || !empty($menu_info['is_hide'])) : unset($menu_list[$key]); endif;
        }
        
        return $this->getListTree($menu_list);
    }
    
    /**
     * 获取列表树结构
     */
    public function getListTree($list = [])
    {
        
        if (is_object($list)) :  $list = $list->toArray(); endif;
        
        return list_to_tree(array_values($list), 'id', 'pid', 'child');
    }
    
    /**
     * 过滤页面内容
     */
    public function filter($content = '', $url_list = [])
    {
        
        $results = [];
        
        preg_match_all('/<ob_link>.*?<\/ob_link>/', $content, $results);
        
        foreach ($results[0] as $a)
        {
            
            $href_results = []; 
            
            preg_match_all('/href=\"([^(\}>)]+)\"/', $a, $href_results);
            
            empty($href_results[0]) && empty($href_results[1]) && preg_match_all('/url=\"([^(\}>)]+)\"/', $a, $href_results);
            
            $url_array_tmp = explode(SYS_DS_PROS, $href_results[1][0]); 
            
            foreach ($url_array_tmp as $k => $u)
            {
                if (strpos($u, EXT) != false) : unset($url_array_tmp[$k]);  break; endif;
            }
            
            $url_array = array_values($url_array_tmp);
            
            $url_array_html = strpos($url_array[1], EXT) === false ? explode('.', $url_array[2]) : explode('.', $url_array[1]);
            
            $check_url = MODULE_NAME . SYS_DS_PROS . $url_array[1] . SYS_DS_PROS . $url_array_html[0];
            
            $result = $this->authCheck($check_url, $url_list);
            
            $result[0] != RESULT_SUCCESS && $content = sr($content, $a);
        }
        
        return $content;
    }
    
    /**
     * 获取首页数据
     */
    public function getIndexData()
    {
        
        $query = new \think\db\Query();
        
        $system_info_mysql = $query->query("select version() as v;");
        
        // 系统信息
        $data['ob_version']     = SYS_VERSION;
        $data['think_version']  = THINK_VERSION;
        $data['os']             = PHP_OS;
        $data['software']       = $_SERVER['SERVER_SOFTWARE'];
        $data['mysql_version']  = $system_info_mysql[0]['v'];
        $data['upload_max']     = ini_get('upload_max_filesize');
        $data['php_version']    = PHP_VERSION;
        
        // 产品信息
        $data['product_name']   = 'OneBase开源免费基础架构';
        $data['author']         = 'Bigotry';
        $data['website']        = 'www.onebase.org';
        $data['qun']            = '<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=3807aa892b97015a8e016778909dee8f23bbd54a4305d827482eda88fcc55b5e"><img border="0" src="//pub.idqqimg.com/wpa/images/group.png" alt="OneBase ①" title="OneBase ①"></a>';
        $data['document']       = '<a target="_blank" href="http://document.onebase.org">http://document.onebase.org</a>';
        
        return $data;
    }
    
}
