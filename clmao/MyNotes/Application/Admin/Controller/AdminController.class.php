<?php

namespace Admin\Controller;

use Think\Controller;

class AdminController extends CommonController {
    /*
     * 后台框架
     */
	 public function index(){
	 }

    public function adminIndex() {
        if (is_mobile() == TRUE) {
            $this->redirect('Admin/Admin/help');
        }
        $this->conf = 'config.php';
        $this->categoryView = 'category.html';
        $this->contentView = 'content.html';
        $this->footerView = 'footer.html';
        $this->headerView = 'header.html';
        $this->indexView = 'index.html';
        $this->cssView = 'mobile.css';
        
        $this->display();
    }

    //添加内容视图
    public function addContent() {
        $this->title = '撰写文章';
        $this->category = M('category')->select();
        if (isset($_REQUEST['flag'])) {
            if (isset($_REQUEST['s'])) {
                $this->msg = "&nbsp;&nbsp;&nbsp;&nbsp;您刚才撰写的文章已经成功发布了！<br/>";
            }
            $this->display('Mobile/addContent');
            die;
        }
        $this->display();
    }

    /*
     * 添加内容的操作
     */

    public function addContentProcess() {
        $arr = array();
        $arr['title'] = I('post.title');
        $arr['c_id'] = I('post.c_id');
        $arr['author'] = I('post.author') ? I('post.author') : '未知';
        $arr['content'] = I('post.content','','');
        $arr['excerpt'] = csubstr(strip_tags($arr['content']), 0, 200);
        if (empty($_POST['time'])) {
            $arr['time'] = time();
        } else {
            $arr['time'] = strtotime(I('post.time'));
        }
        clmao_arrIsEmpty($arr);
        if (M('content')->add($arr)) {
            cookie(null, 'addcontent_');
            $siteMap= A('SiteMap');
            $siteMap->createHtml();
            $this->success('添加成功，可继续添加', U('Admin/Admin/addContent'), 3);
        }
    }

    /*
     * 删除内容的操作
     */

    public function delContentProcess() {
        $id = I('get.id') + 0;
        if (M('content')->delete($id)) {
            $siteMap= A('SiteMap');
            $siteMap->createHtml();
            $this->success('删除成功，稍后返回列表', U('Admin/Admin/listContent'), 3);
        }
    }

    //文章列表
    public function listContent() {
        $this->title = '所有文章';
        $data = D('content')->getPage(0, 1, 1);
        $this->assign('list', $data['list']); // 赋值数据集
        $this->assign('page', $data['page']); // 赋值分页输出
        $this->display();
    }

    /*
     * 保存草稿的操作
     */

    public function addDraftProcess() {
        $arr = $_POST;
        $arr['time'] = time();
        $arr['status'] = 2;
        unset($arr['id']);
        cookie('title', $arr['title'], array('expire' => 3600 * 24 * 30, 'prefix' => 'addcontent_'));
        cookie('c_id', $arr['c_id'], array('expire' => 3600 * 24 * 30, 'prefix' => 'addcontent_'));
        cookie('content', $arr['content'], array('expire' => 3600 * 24 * 30, 'prefix' => 'addcontent_'));
        cookie('author', $arr['author'], array('expire' => 3600 * 24 * 30, 'prefix' => 'addcontent_'));
        if (M('content')->add($arr)) {
            $this->success('草稿保存成功', U('Admin/Admin/addContent'), 2);
        }
    }

    /*
     * 草稿列表页
     */

    public function listDraft() {
        $this->title = '草稿箱';
        $data = D('content')->getPage(0, 2);
        $this->assign('list', $data['list']); // 赋值数据集
        $this->assign('page', $data['page']); // 赋值分页输出
        $this->display();
    }

    /*
     * 编辑内容的视图
     */

    public function editContent() {
        $this->title = '编辑文章';
        $id = I('get.id') + 0;
        $data = M('content')->find($id);
        $this->assign('content', $data);
        $this->category = M('category')->select();
        if (I('get.flag') == 'm') {
            $this->display('Mobile/editView');
            die;
        }
        $this->display();
    }

    /*
     * 编辑内容的处理
     */

    public function editContentProcess() {
        $data = $_POST;
        if (empty($_POST['time'])) {
            $data['time'] = time();
        } else {
            $data['time'] = strtotime(I('post.time'));
        }
        clmao_arrIsEmpty($data);
        $data['excerpt'] = csubstr(strip_tags($data['content']), 0, 200);
        if (M('content')->save($data)) {
          $this->success('保存成功', U('Admin/Admin/editContent', array('id' => $data['id'])));
        } else {
            clmao_die('失败');
        }
    }

    //系统信息
    public function main() {
        $this->title = '系统信息';
        $info = array(
            '作者' => '撒哈拉的小猫 [ <a href="http://blog.clmao.com" target="_blank">查看他的博客</a> ]',
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            'ThinkPHP版本' => THINK_VERSION . ' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . '秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '服务器域名/IP' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
        );
        $this->assign('info', $info);
        $this->display();
    }

    //移至回收站，发布
    public function statusContent() {
        $data['id'] = I('get.id', 0, 'intval');
        $data['status'] = I('get.status', 1, 'intval');
        if (M('content')->save($data)) {
            $siteMap= A('SiteMap');
            $siteMap->createHtml();
            if (I('get.flag') == 'm')
                $this->redirect('/Admin/Mobile/edit/flag/callback');
            $this->success('操作成功', '', 1);
        }
    }

    //回收站列表
    public function listCallback() {
        $this->title = '回收站';
        $data = D('content')->getPage(0, 0);
        $this->assign('list', $data['list']); // 赋值数据集
        $this->assign('page', $data['page']); // 赋值分页输出
        $this->display();
    }

    

    //安全退出
    public function sale() {
        D('User')->sale_exit();
    }

    //分类列表
    public function listCategory() {
        $this->title = '所有分类';
        $data = D('Common/Page')->getPage('category');
        $this->assign('list', $data['list']); // 赋值数据集
        $this->assign('page', $data['page']); // 赋值分页输出
        $this->display();
    }

    //添加分类视图
    public function addCategory() {
        $this->title = '添加分类';
        $this->display();
    }

    //添加分类处理
    public function addCategoryProcess() {
        $data['title'] = trim($_POST['title']);
        $is_diff = M('category')->field('id')->where(array('title' => $data['title']))->find();
        if (!empty($is_diff)) {
            $this->error('该分类名称已经存在', U('Admin/Admin/addCategory'), 2);
        }
        $data['is_nav'] = trim($_POST['is_nav']);
        if (M('category')->add($data)) {
            $this->success('添加成功，可继续添加', U('Admin/Admin/addCategory'), 2);
        }
    }

    //删除分类
    public function delCategoryProcess() {
        $id = I('get.id') + 0;
        if (M('category')->delete($id)) {
            $this->success('删除成功，稍后返回列表', U('Admin/Admin/listCategory'), 3);
        }
    }

    //编辑分类视图
    public function editCategory() {
        $this->title = '编辑分类';
        $id = I('get.id') + 0;
        $data = M('category')->find($id);
        $this->assign('category', $data);
        $this->display();
    }

    //编辑分类的处理
    public function editCategoryProcess() {
        $data = $_POST;
        if (M('category')->save($data)) {
            $this->success('保存成功', U('Admin/Admin/editCategory', array('id' => $data['id'])));
        }
    }

    /*
     * 关于
     */

    public function help() {
        $this->title = '关于';
        $this->display();
    }

    /*
     * 跳转回电脑版
     */

    public function pc() {
        cookie('is_mobile', null);
        $this->redirect('/Admin/Admin/adminIndex', 0);
    }

    /*
     * 跳转回移动版
     */

    public function mobile() {
        cookie('is_mobile', 'true');
        $this->redirect('/Admin/Admin/help', 0);
    }

}
