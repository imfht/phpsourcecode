<?php
namespace module\admin;
use \lib\Action,\lib\RBAC,\lib\page,\core\Config;
/**
 * 初始化文件
*
*/
class base extends Action{
    protected $_G = array();

    /**
     * 对数据进行处理
     *
    */
    protected function init(){
        define('TPL',__ROOT__.'/'.Config::get('TPL_TEMPLATE_PATH'));//模板路径
        if (isset($_GET)) $_GET = $this->textin($_GET);
        if (isset($_POST)) $_POST = $this->htmlin($_POST);
        if (isset($_REQUEST)) $_REQUEST = $this->htmlin($_REQUEST);
        if (isset($_COOKIE)) $_COOKIE = $this->_htmlspecialchars($_COOKIE);
    }

    public function __construct(){
        header("Content-type: text/html; charset=utf-8");
        $this->init();

        if(!isset($_SESSION)){
            session_start();
        }
        if($_SESSION['admin']!=1){
            $this->redirect(url('login/index'));
        }
        $this->_G['timestamp']=time();
        $this->assign('_G',$this->_G);

    }


    /**
     * 采用htmlspecialchars反转义特殊字符
     *
     * @param array|string $data 待反转义的数据
     * @return array|string 反转义之后的数据
     */
    protected function _htmlspecialchars(&$data) {
        return is_array($data) ? array_map(array($this, '_htmlspecialchars'), $data) : trim ( htmlspecialchars ( $data ) );
    }
    protected function textin($data){
        return is_array($data) ? array_map(array($this, 'textin'), $data) : cp_text($data);
    }
    protected function htmlin($data){
        return is_array($data) ? array_map(array($this, 'htmlin'), $data) : cp_html($data);
    }

    /**
     $url，基准网址，若为空，将会自动获取，不建议设置为空
     $total，信息总条数
     $listRows，每页显示行数
     $pagebarnum，分页栏每页显示的页数
     $mode，显示风格，参数可为整数1，2，3，4任意一个
     */
    protected function page($total=0,$listRows=10,$url="",$pageBarNum=10,$mode=1){
        $page=new Page();
        $page->pageSuffix=Config::get('URL_HTML_SUFFIX');
        $cur_page=$page->getCurPage();//当前页码
        $limit_start=($cur_page-1)*$listRows;
        $limit=$limit_start.','.$listRows;
        $pagestring = $page->show($url,$total,$listRows,$pageBarNum,$mode) ;
        return array($limit,$pagestring);
    }


}