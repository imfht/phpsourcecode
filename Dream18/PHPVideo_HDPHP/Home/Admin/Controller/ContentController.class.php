<?php
/**
 * 内容管理
 * Class ContentController
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class ContentController extends AuthController
{
    private $cate, $cid, $aid, $db;

    /**
     * [$authAction 权限验证的动作]
     * @var array
     */
    private $authAction = array('add', 'edit', 'del');

    /**
     * [__init 构造函数]
     * @return [type] [description]
     */
    public function __init()
    {
        parent::__init();
        $this->cate = S('cate');
        $this->db = K('Content');
        $this->cid = Q('cid', 0, 'intval');
        $this->aid = Q('aid', 0, 'intval');

        //验证栏目cid
        if ($this->cid && !isset($this->cate[$this->cid]))
        {
            $this->error('栏目不存在');
        }
        //验证权限
        /*if (!$this->checkAccess()) {
            $this->error('没有操作权限');
        }*/
    }

    /**
     * [index 内容栏目选择页]
     * @return [type] [description]
     */
    public function index()
    {
        $this->display();
    }

    /**
     * [show 内容列表]
     * @return [type] [description]
     */
    public function show()
    {
        // 视频状态
        $video_status = Q('get.content_status', 1, 'intval');
        // 分页类
        $page = new Page($this->db->count(), 15);
        $this->page = $page->show();
        $data = $this->db->where(array('content_status'=> $video_status))->limit($page->limit())->order('arc_sort ASC')->all();
        $this->assign('field', $data);
        $this->display();
    }

    /**
     * [add 添加视频]
     */
    public function add()
    {
        if(IS_POST)
        {
            if($this->db->CreateAdd())
            {
                $this->success('视频内容添加成功！');
            }
            $this->error($this->db->error);
        }
        $cate = $this->cate;
        $flag = M('flag')->all();
        $this->assign('flag', $flag);
        $this->assign('cate', $cate);
        $this->display();
    }

    /**
     * [edit 修改视频]
     * @return [type] [description]
     */
    public function edit()
    {
        if(IS_POST)
        {
            if($this->db->CreateEdit())
            {
                $this->success('视频修改成功！');
            }
            $this->error($this->db->error);
        }
        $cate = $this->cate;
        $result = $this->db->where(array('aid'=> $this->aid))->find();
        $this->assign('cate', $cate);
        $this->assign('field', $result);
        $this->display();
    }

    /**
     * 删除内容视频
     */
    public function del()
    {
        if(M('content')->where(array('aid'=> $this->aid))->delete())
        {
            $this->success('视频内容删除成功！');
        }
        $this->error('视频内容删除失败！');
    }

    /*-----------------------------------------------属性定义----------------------------------------------------*/


    /**
     * 获得category的ztree json 数据
     */
    public function getCateZtree()
    {
        if($this->cate)
        {
            $data = self::setZtree($this->cate);
        }
        $data = isset($data) ? $data : array();
        array_unshift($data,array('name'=>'刷新频道','icon'=>__CONTROLLER_VIEW__.'/ztree/img/diy/1_open.png','click'=>'getZtree();'));
        echo json_encode($data);die;
    }

    /**
     * 异步获得目录树，内容左侧目录列表
     * [getCategoryZtree description]
     * @return [type] [description]
     */
    static private function setZtree($field,$pid=0)
    {
        $cate = array();
        if ($field)
        {
            foreach ($field as $n => $cat)
            {

                $data = array();
                //过滤掉外部链接栏目
                if ($cat['cattype'] != 3)
                {
                    //单视频栏目
                    if ($cat['cattype'] == 4)
                    {
                        $link = U('single', array('cid' => $cat['cid']));
                        $url = $link;
                    }
                    else if ($cat['cattype'] == 1)
                    {
                        $url = U('show', array('cid' => $cat['cid']));
                    }
                    else
                    {
                        $url = 'javascript:';
                    }
                    $data['url'] = $url;
                    $data['target'] = 'z_content';
                    $data['open'] = true;
                    $data['icon']= __CONTROLLER_VIEW__ . '/ztree/img/diy/2.png';
                    if ($cat['cattype'] == 2)
                    {
                        $data['name'] = $cat['catname'] . '（封面）';
                    }
                    else
                    {
                        $data['name'] = $cat['catname'];
                    }
                    $cate[] = $data;
                }
            }
        }
        return $cate;
    }


    /**
     * 验证操作权限
     * @return [type] [description]
     */
    /*public function checkAccess()
    {
        //管理员不验证
        if (IS_SUPER_ADMIN || IS_WEBMASTER)
        {
            return true;
        }
        if (in_array(ACTION, $this->authAction)) {
            $db = M('category_access');
            $access = $db->find($this->cid);
            //栏目没有权限
            if (empty($access)) {
                return true;
            }
            //获得角色权限
            $map['cid'] = $this->cid;
            $map['rid'] = $_SESSION['user']['rid'];
            $RoleAccess = $db->where($map)->find();
            if (empty($RoleAccess)) {
                return false;
            }
            return $RoleAccess[ACTION];
        }
    }*/

    /**
     * 排序
     */
    /*public function order()
    {
        $arc_order = Q('arc_order');
        if (!empty($arc_order) && is_array($arc_order))
        {
            $ContentModel = ContentModel::getInstance($this->mid);
            foreach ($arc_order as $aid => $order)
            {
                $ContentModel->save(array('aid' => $aid, 'arc_sort' => $order));
            }
        }
        $this->success('排序成功');
    }*/


    /**
     * 审核视频
     */
    public function audit()
    {
        $aid = Q('aid', 0, 'intval');
        $status = Q('status', 0, 'intval');
        $data = array('aid' => $aid, 'content_status' => $status);
        if($this->db->where(array('aid'=> $aid))->save($data))
        {
            $this->success('操作成功！', 'show');
        }
        $this->error('参数错误', 'show');
    }

    /**
     * 移动视频
     */
    public function move()
    {
        if (IS_POST) {
            $ContentModel = ContentModel::getInstance($this->mid);
            //移动方式  1 从指定ID  2 从指定栏目
            $from_type = Q("post.from_type", 0, "intval");
            //目标栏目cid
            $to_cid = Q("post.to_cid", 0, 'intval');
            if ($to_cid) {
                switch ($from_type) {
                    case 1 :
                        //移动aid
                        $aid = Q("post.aid", 0, "trim");
                        $aid = explode("|", $aid);
                        if ($aid && is_array($aid)) {
                            foreach ($aid as $id) {
                                if (is_numeric($id)) {
                                    $ContentModel->save(array("aid" => $id, "cid" => $to_cid));
                                }
                            }
                        }
                        break;
                    case 2 :
                        //来源栏目cid
                        $from_cid = Q("post.from_cid", 0);
                        if ($from_cid) {
                            foreach ($from_cid as $d) {
                                if (is_numeric($d)) {
                                    $table = $this->model[$this->cate[$d]['mid']]['table_name'];
                                    M($table)->where("cid=$d")->save(array("cid" => $to_cid));
                                }
                            }
                        }
                        break;
                }
                $this->success('移动成功！');
            } else {
                $this->error('请选择目录栏目');
            }

        } else {
            $category = array();
            foreach ($this->cate as $n => $v) {
                //排除非本模型或外部链接类型栏目或单视频栏目
                if ($v['mid'] != $this->mid || $v['cattype'] == 3 || $v['cattype'] == 4) {
                    continue;
                }
                if ($this->cid == $v['cid']) {
                    $v['selected'] = "selected";
                }
                //封面栏目
                if ($v['cattype'] == 2) {
                    $v['disabled'] = 'disabled';
                }
                $category[$n] = $v;
            }
            $this->assign('category', $category);
            $this->display();
        }
    }



    /**
     * [uploadFile 上传文件]
     * @return [type] [description]
     */
    public function uploadFile()
    {
        $cache = S('field' . $_GET['mid']);
        $set = $cache[$_GET['name']]['set'];
        switch ($_GET['type']) {
            case 'thumb':
                //缩略图字段
                $set['type'] = 'thumb';
                $set['name'] = 'thumb';
                $set['allow_size'] = 2000;
                $set['num'] = 1;
                $set['filetype'] = '*.gif; *.jpg; *.png';
                break;
            case 'image':
                $set['type'] = 'image';
                $set['name'] = $_GET['name'];
                $set['allow_size'] *= 1000;
                $set['num'] = 1;
                $set['filetype'] = '*.gif; *.jpg; *.png';
                break;
            case 'images':
                $set['type'] = 'images';
                $set['name'] = $_GET['name'];
                $set['allow_size'] *= 1000;
                $set['filetype'] = '*.gif; *.jpg; *.png';
                break;
            case 'files':
                $set['type'] = 'files';
                $set['name'] = $_GET['name'];
                $set['allow_size'] *= 1000;
                $filetype = explode(',', $set['filetype']);
                $set['filetype'] = '';
                foreach ($filetype as $t) {
                    $set['filetype'] .= '*.' . $t . ';';
                }
                $set['filetype'] = substr($set['filetype'], 0, -1);
                break;
        }
        $this->assign('set', $set);
        $this->display();
    }

    /**
     * 站内文件
     */
    public function webFile()
    {
        $type = Q('type');
        switch ($type) {
            case 'file':
                $map['status'] = array('EQ', 1);
                break;
            case 'thumb':
            case 'image':
            case 'images':
                $map['status'] = array('EQ', 1);
                $map['image'] = array('EQ', 1);
                break;
            case 'files':
                $map = '';
                break;
        }
        $db = M('upload');
        $map['uid'] = $_SESSION['user']['uid'];
        $count = $db->where($map)->count();
        $page = new Page($count, 18);
        $data = $db->where($map)->limit($page->limit())->all();
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }



    /**
     * 站内文件
     */
    public function noUse()
    {
        $type = Q('type');
        switch ($type) {
            case 'file':
                $map['status'] = array('EQ', 0);
                break;
            case 'thumb':
            case 'image':
            case 'images':
                $map['status'] = array('EQ', 0);
                $map['image'] = array('EQ', 1);
                break;
            case 'files':
                $map = '';
                break;
        }
        $db = M('upload');
        $map['uid'] = $_SESSION['user']['uid'];
        $count = $db->where($map)->count();
        $page = new Page($count, 18);
        $data = $db->where($map)->limit($page->limit())->all();
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 选择模板
     */
    public function selectTemplate()
    {
        if (!$dir = Q('dir')) {
            $dir = 'Template/' . C('WEB_STYLE');
        }
        $file = Dir::tree($dir, 'html');
        $this->assign('id', Q('id'));
        $this->assign('file', $file);
        $this->display();
    }

    /**
     * 上传文件
     */
    public function upload()
    {
        $upload = new Upload('Upload/Content/' . date('y/m'));
        $file = $upload->upload();
        if (empty($file)) {
            $this->ajax('上传失败');
        } else {
            $data = $file[0];
            if($data['image'] && C('WATER_ON')){
                $imageObj = new Image();
                $imageObj->water($data['path'],$data['path']);
            }
            $data['uid'] = $_SESSION['user']['uid'];
            $data['mid'] = $this->mid;
            M('upload')->add($data);
            $this->ajax($data);
        }
    }



    /**
     * 批量删除
     */
    public function batchDel()
    {
        $aid = Q('aid', '', '');
        if (empty($aid)) {
            $this->error('请选择视频');
        }
        $ContentModel = new Content();
        foreach ($aid as $id) {
            if (!$ContentModel->del($id)) {
                $this->error($ContentModel->error);
            }
        }
        $this->success('删除成功');
    }
}