<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public $categorys;
    public $menu;

    public function _initialize(){
        $site = D('Site')->getSetting();
        $default_style = $site['default_style'] ? $site['default_style'] : 'default';
        $this->theme($default_style);
        define('JS_PATH', '/Public/theme/'.$default_style.'/js/');
        define('CSS_PATH', '/Public/theme/'.$default_style.'/css/');
        define('IMG_PATH', '/Public/theme/'.$default_style.'/images/');
        define('STATIC_PATH', '/Public/theme/'.$default_style.'/');
        

        //获取栏目分类
        $this->categorys = F('category_content');
        $this->menu = list_to_tree($this->categorys,'catid','parentid','_child');
        $this->db = D('Content');

        $this->assign('menu', $this->menu);
        $this->assign('CATEGORYS', $this->categorys);
    }

	public function index(){
        $SEO = seo();
        $this->assign('SEO', $SEO);
		$this->display('index');
	}

    public function search(){
        $word = I('get.wd');
        $SEO = seo();
        //搜索产品
        $this->db->set_model(3);
        $map['title'] = array('like', "%$word%");

        $page = I('get.p');
        $pagesize = 16;

        $page_list = $this->db->where($map)->page($page, $pagesize)->select();
        if($page_list){
            foreach ($page_list as $key => $value) {
                $page_list[$key]['title'] = str_ireplace($word, "<em>$word</em>", $value['title']);
            }
        }

        $count = $this->db->where($map)->count();
        $Page       = new \Think\Page($count, $pagesize);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $this->assign('page_list', $page_list);
        $this->assign('pages', $Page->show());
        $this->assign('SEO', $SEO);
        $this->display('search');
    }

    public function lists(){
        $catid = I('get.catid');
        $catdir = I('get.catdir');

        $page = I('get.p');
        // $pagesize = 16;
        //循环， 判断是否存在catdir
        if(empty($catid) && !empty($catdir)){
            foreach($this->categorys as $v){
                if($v['catdir'] == $catdir){
                    $catid = $v['catid'];
                    break;
                }
            }
        }
        if(empty($catid) || !$this->categorys[$catid]){
            $this->error('category_not_exists');
        }
        $map['catid'] = array('in', $this->categorys[$catid]['arrchildid']);
        //取出分类设置
        $CAT = $this->categorys[$catid];

        $this->db->set_model($CAT['modelid']);
        $setting = string2array($CAT['setting']);

        $SEO = seo($catid, $setting['meta_title'],$setting['meta_description'],$setting['meta_keywords']);

        $template_list = $setting['list_template'] ? $setting['list_template'] : 'list';

        if($CAT['type'] == 0){
            //内部栏目
            //顶级topcatid
            $arrparentid = explode(',', $CAT['arrparentid']);
            $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
            // $page_list = $this->db->where($map)->page($page, $pagesize)->select();
            // $count = $this->db->where($map)->count();
            // $Page       = new \Think\Page($count, $pagesize);// 实例化分页类 传入总记录数和每页显示的记录数(25)
//            $Page->setConfig('header',$count.' items');
//            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');

            $this->assign('top_parentid', $top_parentid);
            // $this->assign('page_list', $page_list);
            // $this->assign('pages', $Page->show());
            $this->assign('page', $page);
            $template = $template_list;
        }else{
            //单页面
            $detail = D('Page')->getDetailByCatid($catid);
            $this->assign('Detail', $detail);
            
            $template = 'page';
        }
        $this->assign('catid', $catid);
        $this->assign('SEO', $SEO);
        $this->display($template);
    }

    public function show(){
        $catid = I('get.catid');
        $catdir = I('get.catdir');
        $id = I('get.id');

        //循环， 判断是否存在catdir
        if(empty($catid) && !empty($catdir)){
            foreach($this->categorys as $v){
                if($v['catdir'] == $catdir){
                    $catid = $v['catid'];
                    break;
                }
            }
        }
        $category = $this->categorys[$catid];
        if(empty($catid) || !$this->categorys[$catid]){
            $this->error('category_not_exists');
        }


        //顶级topcatid
        $arrparentid = explode(',', $category['arrparentid']);
        $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;

        $modelid = $category['modelid'];
        $this->db->set_model($modelid);
        $detail = $this->db->getDetail($id);
        $detail['pictureurls'] = string2array($detail['pictureurls']);
        
        //取出栏目推荐
        $pos_category = D('Position')->getList(10, 5, ['catid' => $catid]);
        //取出下一篇文章
        $map_pre['catid'] = $catid;
        $map_pre['id'] = array('lt', $id);
        $map_pre['status'] = 99;
        $previous_page = $this->db->where($map_pre)->find();
        //下一页
        $map_next['catid'] = $catid;
        $map_next['id'] = array('gt', $id);
        $map_next['status'] = 99;
        $next_page = $this->db->where($map_next)->find();

        $template = $template ? $template : $category['setting']['show_template'];
        if(!$template) $template = 'show';

        //seo部分
        if(empty($detail['seotitle'])){
            $SEO = seo($catid, $detail['title'], $detail['description'], $detail['keywords']);
        }else{
            $SEO = seo($catid, $detail['seotitle'], $detail['description'], $detail['keywords']);
        }

        $this->assign('SEO', $SEO);
        $this->assign('top_parentid', $top_parentid);
        $this->assign('catid', $catid);
        $this->assign('previous_page', $previous_page);
        $this->assign('next_page', $next_page);
        $this->assign('pos_category', $pos_category);
        
        $this->assign('Detail', $detail);
        $this->display($template);
    }

    public function show_inquiry(){
        $this->display();
    }
	
    //发送邮件
    public function message() {
        if($_POST){
            //后台验证部分
            $title = isset($_POST['title']) && trim($_POST['title']) ? trim($_POST['title']) : JsMessage('please_enter_title');
            $name = isset($_POST['name']) && trim($_POST['name']) ? trim($_POST['name']) : JsMessage('please_enter_your_name', HTTP_REFERER);
            $companyName = isset($_POST['companyName']) && trim($_POST['companyName']) ? trim($_POST['companyName']) : '';
            $email = isset($_POST['email']) && trim($_POST['email']) ? trim($_POST['email']) : JsMessage('please_enter_your_email', HTTP_REFERER);
            $tel = isset($_POST['tel']) && trim($_POST['tel']) ? trim($_POST['tel']) : showmessage('please_enter_your_tel', HTTP_REFERER);
            $message = isset($_POST['message']) && trim($_POST['message']) ? trim($_POST['message']) : JsMessage('please_enter_message', HTTP_REFERER);
            
            $referer_url = $_SERVER['HTTP_REFERER'];
            $client_ip = get_client_ip(0, true);

            //改为api发送邮件
            $params['name'] = $name;
            $params['tel'] = $tel;
            $params['message'] = $message;
            $params['company'] = $companyName;
            $params['email'] = $email;
            $params['product'] = $title;
            $params['referer_url'] = $referer_url;
            $params['client_ip'] = $client_ip;

            $data['emailType'] = 'Inquiry';
            $data['toEmail'] = 'Manager@MetalsExporter.com,87419336@qq.com';
            $data['siteid'] = 1;
            $data['params'] = $params;

            $api_url = 'http://api.pdosgk.com/email/v1/send?access_token=RA5STXmuh4-kjsC8W1Isb_W2Cqk4RkzTBpqSN1VG';
            $result = getCurlData($api_url, '', 'post', $data);
            $this->ajaxReturn(json_decode($result, true));
            
        }
    }
}