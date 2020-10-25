<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\cms\controller;

use app\cms\model\News as NewsModel;
use app\common\model\Common as CommonModel;
use app\cms\model\Category as CategoryModel;
use app\common\widget\Widget;
use think\Db;
use think\facade\Cache;
use app\admin\controller\Base;
use app\user\model\User as UserModel;
use think\helper\Time;
use think\facade\Env;

class AdminNews extends Base
{
    public function initialize()
    {
        parent::initialize();
        $this->view->config('view_path',Env::get('app_path').'admin/view/');
    }
    /**
     * cms模块首页(仪表盘)
     * @throws
     */
    public function index()
    {
        $news_model      = new NewsModel();
        $user_model      = new UserModel;
        $comments_model  = new CommonModel();
        $comments_model  = $comments_model->setTable(config('database.prefix') . 'comments')->setPk('id');
        $guestbook_model = new CommonModel();
        $guestbook_model = $guestbook_model->setTable(config('database.prefix') . 'guestbook')->setPk('id');
        //热门文章排行
        $news_list = $news_model->where('lang', $this->lang)->order('hits desc')->limit(0, 10)->select();
        $this->assign('news_list', $news_list);
        //总文章数
        $news_count = $news_model->count();
        $this->assign('news_count', $news_count);
        //总会员数
        $user_count = $user_model->count();
        $this->assign('user_count', $user_count);
        //总留言数
        $sugs_count = $guestbook_model->count();
        $this->assign('sugs_count', $sugs_count);
        //总评论数
        $coms_count = $comments_model->count();
        $this->assign('coms_count', $coms_count);

        //日期时间戳
        list($start_t, $end_t) = Time::today();
        list($start_y, $end_y) = Time::yesterday();

        //今日发表文章数
        $tonews_count = $news_model->whereTime('create_time', 'between', [$start_t, $end_t])->count();
        $this->assign('tonews_count', $tonews_count);

        //昨日文章数
        $ztnews_count = $news_model->whereTime('create_time', 'between', [$start_y, $end_y])->count();
        $this->assign('ztnews_count', $ztnews_count);
        //今日提升比
        $difday = ($ztnews_count > 0) ? ($tonews_count - $ztnews_count) / $ztnews_count * 100 : 0;
        $this->assign('difday', $difday);

        //今日增加会员
        $touser_count = $user_model->whereTime('create_time', 'between', [$start_t, $end_t])->count();
        $this->assign('touser_count', $touser_count);
        //昨日会员数
        $ztuser_count = $user_model->whereTime('create_time', 'between', [$start_y, $end_y])->count();
        $this->assign('ztuser_count', $ztuser_count);
        //今日提升比
        $difday_m = ($ztuser_count > 0) ? ($touser_count - $ztuser_count) / $ztuser_count * 100 : 0;
        $this->assign('difday_m', $difday_m);

        //今日留言
        $tosugs_count = $guestbook_model->whereTime('create_time', 'between', [$start_t, $end_t])->count();
        $this->assign('tosugs_count', $tosugs_count);
        //昨日留言
        $ztsugs_count = $guestbook_model->whereTime('create_time', 'between', [$start_y, $end_y])->count();
        $this->assign('ztsugs_count', $ztsugs_count);
        //今日提升比
        $difday_s = ($ztsugs_count > 0) ? ($tosugs_count - $ztsugs_count) / $ztsugs_count * 100 : 0;
        $this->assign('difday_s', $difday_s);

        //今日评论
        $tocoms_count = $comments_model->whereTime('create_time', 'between', [$start_t, $end_t])->count();
        $this->assign('tocoms_count', $tocoms_count);
        //昨日评论
        $ztcoms_count = $comments_model->whereTime('create_time', 'between', [$start_y, $end_y])->count();
        $this->assign('ztcoms_count', $ztcoms_count);
        //今日提升比
        $difday_c = ($ztcoms_count > 0) ? ($tocoms_count - $ztcoms_count) / $ztcoms_count * 100 : 0;
        $this->assign('difday_c', $difday_c);
        return $this->view->config('view_path', Env::get('app_path').request()->module().'/view/admin/')->fetch();
    }
    /**
     * 文章列表
     * @throws
     */
    public function newsIndex()
    {
        $keytype = input('keytype', 'title');
        $key     = input('key', '');
        $lang    = input('lang', '');
        $status  = input('status', '');
        $cid     = input('cid', '');
        $diyflag = input('diyflag', '');
        //查询：时间格式过滤 获取格式 2015-11-12 - 2015-11-18
        $sldate = input('reservation', '');
        $arr    = explode(" - ", $sldate);
        $map    = [];
        if (count($arr) == 2) {
            $arrdateone = strtotime($arr[0]);
            $arrdatetwo = strtotime($arr[1] . ' 23:59:59');
            $map[]      = ['a.create_time', 'between', [$arrdateone, $arrdatetwo]];
        }
        //map架构查询条件数组
        $map[] = ['is_back', '=', 0];
        if (!empty($key)) {
            if ($keytype == 'title') {
                $map[] = ['a.title', 'like', "%" . $key . "%"];
            } elseif ($keytype == 'username') {
                $map[] = ['b.username', 'like', "%" . $key . "%"];
            } else {
                $map[] = [$keytype, '=', $key];
            }
        }
        if ($status != '') {
            $map[] = ['a.status', '=', $status];
        }
        if (!empty($lang)) {
            $map[] = ['a.lang', '=', $lang];
        }
        if (!config('yfcmf.lang_switch_on')) {
            $map[] = ['a.lang', '=', $this->lang];
        }
        if ($cid) {
            $ids   = get_category_byid($cid, 1, 2);
            $map[] = ['a.cid', 'in', implode(",", $ids)];
        }
        if ($diyflag) {
            $map[] = ['', 'exp', Db::raw("FIND_IN_SET('$diyflag',flags)")];
        }
        $news_model = new NewsModel();
        $news       = $news_model->alias("a")->field('a.*,b.username,c.name')
            ->join(config('database.prefix') . 'user b', 'a.author =b.id')
            ->join(config('database.prefix') . 'category c', 'a.cid =c.id')
            ->where($map)->order('a.create_time desc')->paginate(config('paginate.list_rows'), false, ['query' => get_query()]);
        $page       = $news->render();
        $page       = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $page);
        $data       = $news->items();
        //文章属性数据
        $common_model = new CommonModel();
        $diyflag_list = Cache::get('flags');
        if (!$diyflag_list) {
            $diyflag_list = $common_model->setTable(config('database.prefix') . 'flags')->setPk('id')->column('name', 'value');
            Cache::set('flags', $diyflag_list);
        }
        //栏目数据
        $category_text = category_text($this->lang);
        //表格字段
        $fields = [
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '作者', 'field' => 'username'],
            ['title' => '文章标题', 'field' => 'title'],
            ['title' => '所属分类', 'field' => 'name'],
            ['title' => '状态', 'field' => 'status', 'type' => 'switch', 'url' => url('newsState')],
            ['title' => '发布时间', 'field' => 'create_time', 'type' => 'date']
        ];
        $pk     = 'id';
        //右侧操作按钮
        $right_action = [
            'edit'   => ['href' => url('newsEdit'), 'is_pop' => 1],
            'delete' => url('newsDel')
        ];
        $order        = url('newsOrder');
        $delall       = url('newsAlldel');
        $search       = [
            ['select', 'keytype', '', ['title' => '按标题', 'author' => '按发布人ID', 'username' => '按发布人名'], $keytype, '', '', ['is_formgroup' => false], 'class' => ''],
            ['select', 'cid', '', $category_text, $cid, '', '', ['is_formgroup' => false, 'default' => '按分类'], 'class' => 'ajax_change'],
            ['select', 'lang', '', ['zh-cn' => '中文', 'en-us' => '英语'], $lang, '', '', ['is_formgroup' => false, 'default' => '按语言'], 'ajax_change'],
            ['select', 'diyflag', '', $diyflag_list, $diyflag, '', '', ['is_formgroup' => false, 'default' => '按属性'], 'ajax_change'],
            ['select', 'status', '', ['1' => '已启用', '0' => '未启用'], $status, '', '', ['is_formgroup' => false, 'default' => '按状态'], 'ajax_change'],
            ['daterange', 'reservation', '', $sldate, '', ['is_formgroup' => false, 'placeholder' => '点击选择日期范围'], '', 'height:30px;margin:auto 2px;'],
            ['text', 'key', '', $key, '', '', 'text', ['placeholder' => '输入需查询的关键字', 'is_formgroup' => false], 'search-query'],
            ['button', '搜索', ['class' => 'btn btn-purple btn-sm search-query ajax-search-form', 'type' => 'submit', 'icon_l' => 'ace-icon fa fa-search icon-on-right bigger-110']]
        ];
        if (!config('yfcmf.lang_switch_on')) {
            unset($search[2]);
        }
        $form         = [
            'href'  => url('newsIndex'),
            'class' => 'form-search',
            'id'    => 'list-filter'
        ];
        //实例化表单类
        $widget = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, $page, $order, $delall, 1);
        } else {
            return $widget
                ->addToparea(['add' => ['href' => url('newsAdd'), 'is_pop' => 1]], [], $search, $form)
                ->addtable($fields, $pk, $data, $right_action, $page, $order, $delall)
                ->setButton()
                ->fetch();
        }
    }

    /**
     * 添加显示
     * @throws
     */
    public function newsAdd()
    {
        $cid          = input('cid', 0, 'intval');
        $category_text    = category_text($this->lang);
        $common_model = new CommonModel();
        $diyflag_list = Cache::get('flags');
        if (!$diyflag_list) {
            $diyflag_list = $common_model->setTable(config('database.prefix') . 'flags')->setPk('id')->column('name', 'value');
            Cache::set('flags', $diyflag_list);
        }
        $source = Cache::get('source');
        if (!$source) {
            $source = $common_model->setTable(config('database.prefix') . 'source')->setPk('id')->column('name');
            Cache::set('source', $source);
        }
        $help = '<label class="input_last">常用：';
        foreach ($source as $value) {
            $help .= '<a class="btn btn-minier btn-yellow" href="javascript:;" onclick="return souadd("' . $value . '");">' . $value . '</a>&nbsp;';
        }
        $help .= '</label>';
        //实例化表单类
        $widget = new Widget();
        return $widget
            ->addSelect('cid', '文章所属分类', $category_text, $cid)
            ->addText('title', '文章标题', '', '', 'required', 'text', ['placeholder' => '必填：文章标题'])
            ->addText('stitle', '文章短标题', '', '', '', 'text', ['placeholder' => '简短标题，建议6~12字数'])
            ->addCheckbox('flag[]', '文章属性', $diyflag_list)
            ->addText('tags', '标签', '', '多个以,隔开', '', 'text', ['placeholder' => '标签'])
            ->addText('url', '跳转地址', '', '正确格式：http(s):// 开头', '', 'text', ['placeholder' => '跳转地址'])
            ->addText('keyword', '文章关键字', '', '', '', 'text', ['placeholder' => '输入文章关键字，以英文,逗号隔开'])
            ->addText('source', '文章来源', 'YFCMF', $help, '', 'text', ['id' => 'news_source'])
            ->addImage('img', '封面图片上传', '', '上传前先用PS处理成等比例图片后上传，最后都统一比例')
            ->addImages('imgs', '多图')
            ->addSwitch('status', '是否启用', 0)
            ->addText('sort', '排序', 50, '从小到大', '', 'number')
            ->addDate('show_time', '显示日期', date('Y-m-d'))
            ->addTextarea('scontent', '文章简介', '', '已限制在100个字以内', '', ['maxlength' => 100, 'autosize' => 1])
            ->addUeditor('content', '文章主内容')
            ->setTrigger('flag[]', "j", 'url', false)
            ->setUrl(url('newsSave'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 增加
     */
    public function newsSave()
    {
        //获取文章属性
        $flags = input('post.flag/a');
        $flag  = [];
        if (!empty($flags)) {
            foreach ($flags as $v) {
                $flag[] = $v;
            }
        }
        $flagdata = implode(',', $flag);
        $cid      = input('cid', 0, 'intval');
        $sl_data  = [
            'title'       => input('title'),
            'stitle'      => input('stitle', ''),
            'cid'         => $cid,
            'author'      => session('hid'),
            'flags'       => $flagdata,
            'url'         => input('url', ''),
            'keyword'     => input('keyword', ''),
            'tags'        => input('tags', ''),
            'source'      => input('source', ''),
            'imgs'        => input('imgs', ''),//多图路径
            'img'         => input('img', ''),//封面图片路径
            'status'      => input('status', 0),
            'scontent'    => input('scontent', ''),
            'content'     => htmlspecialchars_decode(input('content')),
            'uid'         => session('admin_auth.uid'),
            'create_time' => time(),
            'show_time'   => input('show_time', time(), 'intval'),
            'sort'        => input('sort', 50, 'intval'),
        ];
        //根据栏目id,获取语言
        $model = new CategoryModel();
        $lang            = $model->where('id', $cid)->value('lang');
        $sl_data['lang'] = $lang ? : 'zh-cn';
        $rst             = NewsModel::create($sl_data);
        if ($rst) {
            $this->success('文章添加成功,返回列表页', 'newsIndex', ['is_frame' => 1]);
        } else {
            $this->error('文章添加失败,返回列表页', 'newsIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 编辑显示
     * @throws
     */
    public function newsEdit()
    {
        $id = input('id');
        if (empty($id)) {
            $this->error('参数错误', 'newsIndex');
        }
        $news_list    = NewsModel::get($id);
        $common_model = new CommonModel();
        $diyflag      = Cache::get('flags');
        if (!$diyflag) {
            $diyflag = $common_model->setTable(config('database.prefix') . 'flags')->setPk('id')->column('name', 'value');
            Cache::set('flags', $diyflag);
        }
        $source = Cache::get('source');
        if (!$source) {
            $source = $common_model->setTable(config('database.prefix') . 'source')->setPk('id')->column('name');
            Cache::set('source', $source);
        }
        $help = '<label class="input_last">常用：';
        foreach ($source as $value) {
            $help .= '<a class="btn btn-minier btn-yellow" href="javascript:;" onclick="return souadd(" ' . $value . ' ");">' . $value . '</a>&nbsp;';
        }
        $help .= '</label>';
        $category_text = category_text($this->lang);
        //实例化表单类
        $widget = new Widget();
        return $widget
            ->addText('id', '', $id, '', '', 'hidden')
            ->addSelect('cid', '文章所属分类', $category_text, $news_list['cid'])
            ->addText('title', '文章标题', $news_list['title'], '', 'required', 'text', ['placeholder' => '必填：文章标题'])
            ->addText('stitle', '文章短标题', $news_list['stitle'], '', '', 'text', ['placeholder' => '简短标题，建议6~12字数'])
            ->addCheckbox('flag[]', '文章属性', $diyflag, $news_list['flags'])
            ->addText('tags', '标签', $news_list['tags'], '多个以,隔开', '', 'text', ['placeholder' => '标签'])
            ->addText('url', '跳转地址', $news_list['url'], '正确格式：http(s):// 开头', '', 'text', ['placeholder' => '跳转地址'])
            ->addText('keyword', '文章关键字', $news_list['keyword'], '', '', 'text', ['placeholder' => '输入文章关键字，以英文,逗号隔开'])
            ->addText('source', '文章来源', $news_list['source'], $help, '', 'text', ['id' => 'news_source'])
            ->addImage('img', '封面图片上传', $news_list['img'], '上传前先用PS处理成等比例图片后上传，最后都统一比例')
            ->addImages('imgs', '多图', $news_list['imgs'])
            ->addSwitch('status', '是否启用', $news_list['status'])
            ->addText('sort', '排序', $news_list['sort'], '从小到大', '', 'number')
            ->addDate('show_time', '显示日期', date('Y-m-d', $news_list['show_time']))
            ->addTextarea('scontent', '文章简介', $news_list['scontent'], '已限制在100个字以内', '', ['maxlength' => 100, 'autosize' => 1])
            ->addUeditor('content', '文章主内容', $news_list['content'])
            ->setTrigger('flag[]', "j", 'url', false)
            ->setUrl(url('newsUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 文章更新
     */
    public function newsUpdate()
    {
        //获取文章属性
        $flags = input('post.flag/a');
        $flag  = [];
        if (!empty($flags)) {
            foreach ($flags as $v) {
                $flag[] = $v;
            }
        }
        $flagdata = implode(',', $flag);
        $showtime = input('show_time', '');
        $showtime = ($showtime == '') ? time() : strtotime($showtime);
        $cid      = input('cid', 0, 'intval');
        $sl_data  = [
            'id'          => input('id'),
            'title'       => input('title'),
            'stitle'      => input('stitle', ''),
            'cid'         => $cid,
            'flags'       => $flagdata,
            'url'         => input('url', ''),
            'keyword'     => input('keyword', ''),
            'tags'        => input('tags', ''),
            'img'         => input('img', ''),
            'imgs'        => input('imgs', ''),
            'source'      => input('source', ''),
            'status'      => input('status', 0),
            'scontent'    => input('scontent', ''),
            'content'     => htmlspecialchars_decode(input('content')),
            'sort'        => input('sort', 50, 'intval'),
            'update_time' => time(),
            'show_time'   => $showtime
        ];
        //根据栏目id,获取语言
        $model = new CategoryModel();
        $lang            = $model->where('id', $cid)->value('lang');
        $sl_data['lang'] = $lang ? : 'zh-cn';
        $rst             = NewsModel::update($sl_data);
        if ($rst !== false) {
            $this->success('文章修改成功', 'newsIndex', ['is_frame' => 1]);
        } else {
            $this->error('文章修改失败', 'newsIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 文章排序
     * @throws
     */
    public function newsOrder()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', 'newsIndex');
        } else {
            $list = [];
            foreach (input('post.') as $id => $sort) {
                $list[] = ['id' => $id, 'sort' => $sort];
            }
            $news_model = new NewsModel();
            $news_model->saveAll($list);
            $this->success('排序更新成功', 'newsIndex');
        }
    }

    /**
     * 删除至回收站(单个)
     */
    public function newsDel()
    {
        $news_model = new NewsModel();
        $rst        = $news_model->where('id', input('id'))->setField('is_back', 1);
        if ($rst !== false) {
            $this->success('文章已转入回收站', 'newsIndex');
        } else {
            $this->error("删除文章失败！", 'newsIndex');
        }
    }

    /**
     * 删除至回收站(全选)
     */
    public function newsAlldel()
    {
        $ids = input('ids/a');
        if (empty($ids)) {
            $this->error("请选择删除文章", 'newsIndex');
        }
        if (is_array($ids)) {
            $where = 'id in(' . implode(',', $ids) . ')';
        } else {
            $where = 'id=' . $ids;
        }
        $news_model = new NewsModel();
        $rst        = $news_model->where($where)->setField('is_back', 1);
        if ($rst !== false) {
            $this->success("成功把文章移至回收站！", 'newsIndex');
        } else {
            $this->error("删除文章失败！", 'newsIndex');
        }
    }

    /**
     * 文章审核/取消审核
     */
    public function newsState()
    {
        $id = input('id', 0, 'intval');
        if (!$id) {
            $this->error('文章不存在', 'newsIndex');
        }
        $news_model = new NewsModel();
        $status = $news_model->where('id', $id)->value('status');
        $status = $status ? 0 : 1;
        $news_model->where('id', $id)->setField('status', $status);
        $this->success($status ? '启用' : '禁用', null, ['result' => $status]);
    }

    /**
     * 回收站列表
     * @throws
     */
    public function backIndex()
    {
        $keytype = input('keytype', 'title');
        $key     = input('key', '');
        $lang    = input('lang', '');
        $status  = input('status', '');
        $cid     = input('cid', '');
        $diyflag = input('diyflag', '');
        //查询：时间格式过滤 获取格式 2015-11-12 - 2015-11-18
        $sldate = input('reservation', '');
        $arr    = explode(" - ", $sldate);
        $map    = [];
        if (count($arr) == 2) {
            $arrdateone = strtotime($arr[0]);
            $arrdatetwo = strtotime($arr[1] . ' 23:55:55');
            $map[]      = ['a.create_time', 'between', [$arrdateone, $arrdatetwo]];
        }
        //map架构查询条件数组
        $map[] = ['is_back', '=', 1];
        if (!empty($key)) {
            if ($keytype == 'title') {
                $map[] = ['a.title', 'like', "%" . $key . "%"];
            } elseif ($keytype == 'username') {
                $map[] = ['b.username', 'like', "%" . $key . "%"];
            } else {
                $map[] = [$keytype, '=', $key];
            }
        }
        if ($status != '') {
            $map[] = ['a.status', '=', $status];
        }
        if (!empty($lang)) {
            $map[] = ['a.lang', '=', $lang];
        }
        if (!config('yfcmf.lang_switch_on')) {
            $map[] = ['a.lang', '=', $this->lang];
        }
        if ($cid) {
            $ids   = get_category_byid($cid, 1, 2);
            $map[] = ['a.cid', 'in', implode(",", $ids)];
        }
        if ($diyflag) {
            $map[] = ['', 'exp', Db::raw("FIND_IN_SET('$diyflag',flags)")];
        }
        $news_model = new NewsModel();
        $news       = $news_model->alias("a")->field('a.*,b.username,c.name')
            ->join(config('database.prefix') . 'user b', 'a.author =b.id')
            ->join(config('database.prefix') . 'category c', 'a.cid =c.id')
            ->where($map)->order('a.create_time desc')->paginate(config('paginate.list_rows'), false, ['query' => get_query()]);
        $page       = $news->render();
        $page       = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $page);
        $data       = $news->items();
        foreach ($data as &$v) {
            $v['back_url'] = url('backState', ['id' => $v['id']]);
        }
        //文章属性数据
        $common_model = new CommonModel();
        $diyflag_list = Cache::get('flags');
        if (!$diyflag_list) {
            $diyflag_list = $common_model->setTable(config('database.prefix') . 'flags')->setPk('id')->column('name', 'value');
            Cache::set('flags', $diyflag_list);
        }
        //栏目数据
        $category_text = category_text($this->lang);
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '作者', 'field' => 'username'],
            ['title' => '文章标题', 'field' => 'title'],
            ['title' => '所属分类', 'field' => 'name'],
            ['title' => '状态', 'field' => 'status', 'type' => 'switch', 'url' => url('newsState')],
            ['title' => '发布时间', 'field' => 'create_time', 'type' => 'date']
        ];
        $pk     = 'id';
        //右侧操作按钮
        $right_action = [
            'back'   => ['field' => 'back_url', 'title' => '还原', 'extra_attr' => 'data-info="你确定要还原文章到文章列表吗？"', 'class' => 'red confirm-rst-url-btn', 'icon' => 'fa fa-check'],
            'delete' => url('backDel')
        ];
        $delall       = url('backAlldel');
        $search       = [
            ['select', 'keytype', '', ['title' => '按标题', 'author' => '按发布人ID', 'username' => '按发布人名'], $keytype, '', '', ['is_formgroup' => false], 'class' => ''],
            ['select', 'cid', '', $category_text, $cid, '', '', ['is_formgroup' => false, 'default' => '按分类'], 'class' => 'ajax_change'],
            ['select', 'lang', '', ['zh-cn' => '中文', 'en-us' => '英语'], $lang, '', '', ['is_formgroup' => false, 'default' => '按语言'], 'ajax_change'],
            ['select', 'diyflag', '', $diyflag_list, $diyflag, '', '', ['is_formgroup' => false, 'default' => '按属性'], 'ajax_change'],
            ['select', 'status', '', ['1' => '已启用', '0' => '未启用'], $status, '', '', ['is_formgroup' => false, 'default' => '按状态'], 'ajax_change'],
            ['daterange', 'reservation', '', $sldate, '', ['is_formgroup' => false, 'placeholder' => '点击选择日期范围'], '', 'height:30px;margin:auto 2px;'],
            ['text', 'key', '', $key, '', '', 'text', ['placeholder' => '输入需查询的关键字', 'is_formgroup' => false], 'search-query'],
            ['button', '搜索', ['class' => 'btn btn-purple btn-sm search-query ajax-search-form', 'type' => 'submit', 'icon_l' => 'ace-icon fa fa-search icon-on-right bigger-110']]
        ];
        $form         = [
            'href'  => url('backIndex'),
            'class' => 'form-search',
            'id'    => 'list-filter'
        ];
        if (!config('yfcmf.lang_switch_on')) {
            unset($search[2]);
        }
        //实例化表单类
        $widget = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, $page, '', $delall, 1);
        } else {
            return $widget
                ->addToparea([], [], $search, $form)
                ->addtable($fields, $pk, $data, $right_action, $page, '', $delall)
                ->setButton()
                ->fetch();
        }
    }

    /**
     * 还原文章
     */
    public function backState()
    {
        $news_model = new NewsModel();
        $rst        = $news_model->where('id', input('id'))->setField('is_back', 0);
        if ($rst !== false) {
            $this->success('文章还原成功！', 'backIndex');
        } else {
            $this->error("文章还原失败！", 'backIndex');
        }
    }

    /**
     * 彻底删除(单个)
     * @throws
     */
    public function backDel()
    {
        $id         = input('id');
        $news_model = new NewsModel();
        if (empty($id)) {
            $this->error('参数错误', 'backIndex');
        } else {
            $rst = $news_model->where('id', $id)->delete();
            if ($rst !== false) {
                $this->success('文章彻底删除成功！', 'backIndex');
            } else {
                $this->error("文章彻底删除失败！", 'backIndex');
            }
        }
    }

    /**
     * 彻底删除(全选)
     * @throws
     */
    public function backAlldel()
    {
        $ids = input('ids/a');
        if (empty($ids)) {
            $this->error("请选择删除文章", 'backIndex');
        }
        if (is_array($ids)) {
            $where = 'id in(' . implode(',', $ids) . ')';
        } else {
            $where = 'id=' . $ids;
        }
        $news_model = new NewsModel();
        $rst        = $news_model->where($where)->delete();
        if ($rst !== false) {
            $this->success("文章彻底删除成功！", 'backIndex');
        } else {
            $this->error("文章彻底删除失败！", 'backIndex');
        }
    }
}
