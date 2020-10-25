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

use app\common\model\Common as CommonModel;
use app\common\widget\Widget;
use app\admin\controller\Base;

class AdminPlug extends Base
{
    protected $link_model     = null;
    protected $linktype_model = null;
    protected $source_model   = null;
    protected $ad_model       = null;
    protected $adtype_model   = null;

    public function initialize()
    {
        parent::initialize();
        $this->link_model     = new CommonModel();
        $this->link_model     = $this->link_model->setTable(config('database.prefix') . 'link')->setPk('id');
        $this->linktype_model = new CommonModel();
        $this->linktype_model = $this->linktype_model->setTable(config('database.prefix') . 'linktype')->setPk('id');
        $this->ad_model       = new CommonModel();
        $this->ad_model       = $this->ad_model->setTable(config('database.prefix') . 'ad')->setPk('id');
        $this->adtype_model   = new CommonModel();
        $this->adtype_model   = $this->adtype_model->setTable(config('database.prefix') . 'adtype')->setPk('id');
        $this->source_model   = new CommonModel();
        $this->source_model   = $this->source_model->setTable(config('database.prefix') . 'source')->setPk('id');
    }

    /**
     * 友情链接列表
     * @author rainfer <81818832@qq.com>
     */
    public function linkIndex()
    {
        //条件
        $type = input('type', '');
        $val  = input('val', '');
        $lang = input('lang', '');
        $map  = [];
        $type && $map[] = ['typeid', '=', $type];
        $val && $map[] = ['name|url', 'like', "%" . $val . "%"];
        $lang && $map[] = ['lang', '=', $lang];
        if (!config('yfcmf.lang_switch_on')) {
            $map['lang'] = ['lang', '=', $this->lang];
        }
        $link_type = $this->linktype_model->column('name', 'id');
        $link      = $this->link_model->alias("a")->field('a.*,b.name as typename')->join(config('database.prefix') . 'linktype b', 'a.typeid =b.id')->where($map)->order('create_time desc')->paginate(config('paginate.list_rows'), false, ['query' => request()->param()]);
        $page      = $link->render();
        $page      = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $page);
        $data      = $link->items();
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
            ['title' => '链接名称', 'field' => 'name'],
            ['title' => '链接URL', 'field' => 'url'],
            ['title' => '联系QQ', 'field' => 'qq'],
            ['title' => '所属栏目', 'field' => 'typename'],
            ['title' => '语言', 'field' => 'lang'],
            ['title' => '添加时间', 'field' => 'create_time', 'type' => 'date'],
            ['title' => '状态', 'field' => 'status', 'type' => 'switch', 'url' => url('linkState')]
        ];
        //主键
        $pk = 'id';
        //右侧操作按钮
        $right_action = [
            'edit'   => ['href' => url('linkEdit'), 'is_pop' => 1],
            'delete' => url('linkDel')
        ];
        $search       = [
            ['select', 'type', '', $link_type, $type, '', '', ['is_formgroup' => false, 'default' => '按广告位'], 'ajax_change'],
            ['select', 'plug_link_l', '', ['zh-cn' => '中文', 'en-us' => '英语'], $lang, '', '', ['is_formgroup' => false, 'default' => '按语言'], 'ajax_change'],
            ['text', 'val', '', $val, '', '', 'text', ['placeholder' => '输入链接名称或URL', 'is_formgroup' => false], 'search-query'],
            ['button', '搜索', ['class' => 'btn btn-purple btn-sm search-query ajax-search-form', 'type' => 'submit', 'icon_l' => 'ace-icon fa fa-search icon-on-right bigger-110']]
        ];
        $form         = [
            'href'  => url('linkIndex'),
            'class' => 'form-search',
            'id'    => 'list-filter'
        ];
        if (!config('yfcmf.lang_switch_on')) {
            unset($fields[6]);
            unset($search[1]);
        }
        //实例化表单类
        $order  = url('linkOrder');
        $delall = url('linkAlldel');
        $widget = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, $page, $order, $delall, 1);
        } else {
            return $widget
                ->addToparea(['add' => ['href' => url('linkAdd'), 'is_pop' => 1]], [], $search, $form)
                ->addtable($fields, $pk, $data, $right_action, $page, $order, $delall)
                ->setButton()
                ->fetch();
        }
    }


    /**
     * 友情链接添加操作
     * @author rainfer <81818832@qq.com>
     */
    public function linkAdd()
    {
        $link_type = $this->linktype_model->column('name', 'id');
        //实例化表单类
        $widget = new Widget();
        $widget = $widget
            ->addSelect('typeid', '所属栏目', $link_type, '', '', 'required')
            ->addText('name', '链接名称', '', '必须是以字母开头，数字、符号组合', 'required', 'text', ['placeholder' => '输入链接名称'])
            ->addText('url', '链接地址', '', '必须是以http(s)://开头', 'required', 'text', ['placeholder' => '输入链接URL'])
            ->addSelect('target', '打开方式', ['_blank' => '新标签页打开', '_self' => '本窗口打开'], '_blank', '', 'required')
            ->addSwitch('status', '是否启用', 0)
            ->addText('qq', '联系方式', '', 'QQ或其它联系方式')
            ->addText('sort', '排序', 50, '从小到大排序', 'required', 'number');
        if (config('yfcmf.lang_switch_on')) {
            $widget = $widget->addSelect('lang', '语言', ['zh-cn' => '中文', 'en-us' => '英语'], 'zh-cn', '', 'required');
        }
        return $widget->setUrl(url('linkSave'))
            ->setAjax()
            ->fetch();
    }

    /**
     * 友情链接添加操作
     * @author rainfer <81818832@qq.com>
     */
    public function linkSave()
    {
        $sl_data = [
            'name'        => input('name'),
            'lang'        => input('lang', 'zh-cn'),
            'url'         => input('url'),
            'target'      => input('target'),
            'typeid'      => input('typeid'),
            'qq'          => input('qq'),
            'sort'        => input('sort'),
            'create_time' => time(),
            'status'      => input('status', 0),
        ];
        $rst     = $this->link_model->insert($sl_data);
        if ($rst !== false) {
            $this->success('友情链接添加成功', 'linkIndex', ['is_frame' => 1]);
        } else {
            $this->error('友情链接添加失败', 'linkIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 友情 链接修改操作
     * @author rainfer <81818832@qq.com>
     */
    public function linkEdit()
    {
        $link_type = $this->linktype_model->column('name', 'id');
        $id        = input('id');
        $link      = $this->link_model->where('id', $id)->find();
        $widget    = new Widget();
        $widget = $widget
            ->addText('id', '', $link['id'], '', '', 'hidden')
            ->addSelect('typeid', '所属栏目', $link_type, $link['typeid'], '', 'required')
            ->addText('name', '链接名称', $link['name'], '必须是以字母开头，数字、符号组合', 'required', 'text', ['placeholder' => '输入链接名称'])
            ->addText('url', '链接地址', $link['url'], '必须是以http(s)://开头', 'required', 'text', ['placeholder' => '输入链接URL'])
            ->addSelect('target', '打开方式', ['_blank' => '新标签页打开', '_self' => '本窗口打开'], $link['target'], '', 'required')
            ->addText('qq', '联系方式', $link['qq'], 'QQ或其它联系方式')
            ->addText('sort', '排序', $link['sort'], '从小到大排序', 'required', 'number');
        if (config('yfcmf.lang_switch_on')) {
            $widget = $widget->addSelect('lang', '语言', ['zh-cn' => '中文', 'en-us' => '英语'], $link['lang'], '', 'required');
        }
        return $widget->setUrl(url('linkUpdate'))
            ->setAjax()
            ->fetch();
    }

    /**
     * 友情 链接修改操作
     * @author rainfer <81818832@qq.com>
     */
    public function linkUpdate()
    {
        $sl_data = [
            'id'     => input('id'),
            'lang'   => input('lang', 'zh-cn'),
            'name'   => input('name'),
            'url'    => input('url'),
            'target' => input('target'),
            'typeid' => input('typeid'),
            'qq'     => input('plug_link_qq'),
            'sort'   => input('sort'),

        ];
        $rst     = $this->link_model->isUpdate(true)->save($sl_data);
        if ($rst !== false) {
            $this->success('友情链接修改成功', 'linkIndex', ['is_frame' => 1]);
        } else {
            $this->error('友情链接修改失败', 'linkIndex');
        }
    }

    /**
     * 友情链接删除操作
     * @author rainfer <81818832@qq.com>
     */
    public function linkDel()
    {
        $rst = $this->link_model->where('id', input('id'))->delete();
        if ($rst !== false) {
            $this->success('友情链接删除成功', 'linkIndex');
        } else {
            $this->error('友情链接删除失败', 'linkIndex');
        }
    }

    /**
     * 友情链接多选删除操作
     * @author rainfer <81818832@qq.com>
     */
    public function linkAlldel()
    {
        $ids = input('ids/a');
        if (empty($ids)) {
            $this->error("请选择待删除的友链", 'linkIndex');
        }
        if (is_array($ids)) {
            $where = 'id in(' . implode(',', $ids) . ')';
        } else {
            $where = 'id=' . $ids;
        }
        $rst = $this->link_model->where($where)->delete();
        if ($rst !== false) {
            $this->success("友情链接删除成功", 'linkIndex');
        } else {
            $this->error("友情链接删除失败", 'linkIndex');
        }
    }

    /**
     * 友情链接状态操作
     * @author rainfer <81818832@qq.com>
     */
    public function linkState()
    {
        $id = input('id', 0, 'intval');
        if (!$id) {
            $this->error('友情链接不存在', 'linkIndex');
        }
        $status = $this->link_model->where('id', $id)->value('status');
        $status = $status ? 0 : 1;
        $this->link_model->where('id', $id)->setField('status', $status);
        $this->success($status ? '启用' : '禁用', null, ['result' => $status]);
    }

    /**
     * 友链排序
     * @author rainfer <81818832@qq.com>
     */
    public function linkOrder()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', 'linkIndex');
        } else {
            foreach (input('post.') as $id => $sort) {
                $this->link_model->where('id', $id)->setField('sort', $sort);
            }
            $this->success('排序更新成功', 'linkIndex');
        }
    }

    /**
     * 友情链接类型列表
     * @author rainfer <81818832@qq.com>
     */
    public function linktypeIndex()
    {
        $link_type = $this->linktype_model->order('sort')->select();
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '栏目名称', 'field' => 'name'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input']
        ];
        //主键
        $pk = 'id';
        //右侧操作按钮
        $right_action = [
            'edit'   => ['href' => url('linktypeEdit'), 'is_pop' => 1],
            'delete' => url('linktypeDel')
        ];
        $order        = url('linktypeOrder');
        //实例化表单类
        $widget = new Widget();
        return $widget
            ->addToparea(['add' => ['href' => url('linktypeAdd'), 'is_pop' => 1]])
            ->addtable($fields, $pk, $link_type, $right_action, '', $order)
            ->setButton()
            ->fetch();
    }

    /**
     * 友情链接类型添加
     * @author rainfer <81818832@qq.com>
     */
    public function linktypeAdd()
    {
        $widget = new Widget();
        return $widget
            ->addText('name', '栏目名称', '', '', 'required', 'text', ['placeholder' => '输入栏目名称'])
            ->addText('sort', '排序', '50', '* 从小到大排序', 'required', 'number')
            ->setUrl(url('linktypeSave'))
            ->setAjax()
            ->fetch();
    }

    /**
     * 友情链接类型添加
     * @author rainfer <81818832@qq.com>
     */
    public function linktypeSave()
    {
        $rst = $this->linktype_model->insert(input('post.'));
        if ($rst) {
            $this->success('栏目添加成功', 'linktypeIndex', ['is_frame' => 1]);
        } else {
            $this->error('栏目添加失败', 'linktypeIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 友情链接类型修改
     * @author rainfer <81818832@qq.com>
     */
    public function linktypeEdit()
    {
        $id       = input('id', 0, 'intval');
        $linktype = $this->linktype_model->find($id);
        $widget   = new Widget();
        return $widget
            ->addText('id', '', $id, '', '', 'hidden')
            ->addText('name', '栏目名称', $linktype['name'], '', 'required', 'text', ['placeholder' => '输入栏目名称'])
            ->addText('sort', '排序', $linktype['sort'], '* 从小到大排序', 'required', 'number')
            ->setUrl(url('linktypeUpdate'))
            ->setAjax()
            ->fetch();
    }

    /**
    * 友情链接类型修改
    * @author rainfer <81818832@qq.com>
    */
    public function linktypeUpdate()
    {
        $sl_data = [
            'id'   => input('id', 0, 'intval'),
            'name' => input('name'),
            'sort' => input('sort'),
        ];
        $rst     = $this->linktype_model->isUpdate(true)->save($sl_data);
        if ($rst !== false) {
            $this->success('友情链接栏目修改成功', 'linktypeIndex', ['is_frame' => 1]);
        } else {
            $this->error('友情链接栏目修改成功', 'linktypeIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 友情链接类型删除
     * @author rainfer <81818832@qq.com>
     */
    public function linktypeDel()
    {
        $id  = input('id');
        $rst = $this->link_model->where('typeid', $id)->find();
        if ($rst) {
            $this->error('友链栏位存在友链,请先删除友链', 'linkIndex');
        }
        $rst = $this->linktype_model->where('id', input('id'))->delete();
        if ($rst !== false) {
            $this->success('友链类型删除成功', 'linktypeIndex');
        } else {
            $this->error('友链类型删除失败', 'linktypeIndex');
        }
    }

    /**
     * 友情链接类型排序
     * @author rainfer <81818832@qq.com>
     */
    public function linktypeOrder()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', 'linktypeIndex');
        } else {
            $post = input('post.');
            foreach ($post as $id => $sort) {
                $this->linktype_model->where('id', $id)->setField('sort', $sort);
            }
            $this->success('排序更新成功', 'linktypeIndex');
        }
    }

    /**
     * 广告管理
     * @author rainfer <81818832@qq.com>
     */
    public function adIndex()
    {
        $key  = input('key', '');
        $lang = input('lang', '');
        $map  = [];
        $key && $map[] = ['name', 'like', "%" . $key . "%"];
        $lang && $map[] = ['lang', '=', $lang];
        if (!config('yfcmf.lang_switch_on')) {
            $map['lang'] = ['lang', '=', $this->lang];
        }
        $ad_list = $this->ad_model->alias("a")->field('a.*,b.name as typename')->join(config('database.prefix') . 'adtype b', 'a.adtypeid =b.id')->where($map)->order('a.sort')->paginate(config('paginate.list_rows'), false, ['query' => request()->param()]);
        $page    = $ad_list->render();
        $page    = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $page);
        $data    = $ad_list->items();
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '广告名称', 'field' => 'name'],
            ['title' => '所属位置', 'field' => 'typename'],
            ['title' => '语言', 'field' => 'lang'],
            ['title' => '添加时间', 'field' => 'create_time', 'type' => 'date'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
            ['title' => '状态', 'field' => 'status', 'type' => 'switch', 'url' => url('adState')]
        ];
        //主键
        $pk = 'id';
        //右侧操作按钮
        $right_action = [
            'edit'   => ['href' => url('adEdit'), 'is_pop' => 1],
            'delete' => url('adDel')
        ];
        $order        = url('adOrder');
        $search       = [
            ['select', 'lang', '', ['zh-cn' => '中文', 'en-us' => '英语'], $lang, '', '', ['is_formgroup' => false, 'default' => '按语言'], 'ajax_change'],
            ['text', 'key', '', $key, '', '', 'text', ['placeholder' => '输入链接名称或URL', 'is_formgroup' => false], 'search-query'],
            ['button', '搜索', ['class' => 'btn btn-purple btn-sm search-query ajax-search-form', 'type' => 'submit', 'icon_l' => 'ace-icon fa fa-search icon-on-right bigger-110']]
        ];
        $form         = [
            'href'  => url('adIndex'),
            'class' => 'form-search'
        ];
        if (!config('yfcmf.lang_switch_on')) {
            unset($fields[3]);
            unset($search[0]);
        }
        //实例化表单类
        $widget = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, $page, $order, '', 1);
        } else {
            return $widget
                ->addToparea(['add' => ['href' => url('adAdd'), 'is_pop' => 1]], [], $search, $form)
                ->addtable($fields, $pk, $data, $right_action, $page, $order)
                ->setButton()
                ->fetch();
        }
    }

    /**
     * 添加广告操作
     * @author rainfer <81818832@qq.com>
     */
    public function adAdd()
    {
        $adtype_list = $this->adtype_model->order('sort')->column('name', 'id');//获取所有广告位
        $widget      = new Widget();
        $widget = $widget
            ->addSelect('adtypeid', '广告位', $adtype_list);
        if (config('yfcmf.lang_switch_on')) {
            $widget = $widget->addSelect('lang', '语言', ['' => '选择语言', 'zh-cn' => '中文', 'en-us' => '英语']);
        }
        return $widget->addText('name', '广告名称', '', '', 'required', 'text', ['placeholder' => '输入广告名称'])
            ->addRadio('type', '广告模式', ['1' => '图片模式', '2' => 'JS代码'], 1)
            ->addImage('img', '广告图片')
            ->addTextarea('js', 'JS代码', '', '', '', ['placeholder' => '输入JS代码'])
            ->addText('url', '链接URL', '', '必须是以http(s)://开头', '', 'text', ['placeholder' => '输入链接URL'])
            ->addSwitch('status', '是否启用', 0, '默认不启用')
            ->addText('sort', '排序', '50', '* 从小到大排序', 'required', 'number')
            ->addTextarea('content', '内容', '', '广告文字内容')
            ->setUrl(url('adSave'))
            ->setTrigger('type', "1", 'img', false)
            ->setTrigger('type', "2", 'js', false)
            ->setAjax()
            ->fetch();
    }

    /**
     * 添加广告操作
     * @author rainfer <81818832@qq.com>
     */
    public function adSave()
    {
        //构建数组
        $sl_data = [
            'adtypeid'    => input('adtypeid'),
            'name'        => input('name'),
            'lang'        => input('lang', 'zh-cn'),
            'img'         => input('img', ''),
            'url'         => input('url', ''),
            'type'        => input('type'),
            'js'          => input('js'),
            'status'      => input('status', 0),
            'sort'        => input('sort', 50, 'intval'),
            'content'     => input('content', ''),
            'create_time' => time(),
        ];
        $rst     = $this->ad_model->insert($sl_data);
        if ($rst) {
            $this->success('广告添加成功', 'adIndex', ['is_frame' => 1]);
        } else {
            $this->error('广告添加失败', 'adIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 广告位修改操作
     * @author rainfer <81818832@qq.com>
     */
    public function adEdit()
    {
        $id          = input('id', 0, 'intval');
        $ad          = $this->ad_model->find($id);
        $adtype_list = $this->adtype_model->order('sort')->column('name', 'id');//获取所有广告位
        $widget      = new Widget();
        $widget = $widget
            ->addText('id', '', $id, '', '', 'hidden')
            ->addSelect('adtypeid', '广告位', $adtype_list, $ad['adtypeid']);
        if (config('yfcmf.lang_switch_on')) {
            $widget = $widget->addSelect('lang', '语言', ['' => '选择语言', 'zh-cn' => '中文', 'en-us' => '英语'], $ad['lang']);
        }
        return $widget->addText('name', '广告名称', $ad['name'], '', 'required', 'text', ['placeholder' => '输入广告名称'])
            ->addradio('type', '广告模式', ['1' => '图片模式', '2' => 'JS代码'], $ad['type'])
            ->addImage('img', '广告图片', $ad['img'])
            ->addText('js', 'JS代码', $ad['js'], '', '', 'text', ['placeholder' => '输入JS代码'])
            ->addText('url', '链接URL', $ad['url'], '必须是以http(s)://开头', '', 'text', ['placeholder' => '输入链接URL'])
            ->addSwitch('status', '是否启用', $ad['status'])
            ->addText('sort', '排序', $ad['sort'], '* 从小到大排序', 'required', 'number')
            ->addTextarea('content', '内容', $ad['content'], '广告文字内容')
            ->setUrl(url('adUpdate'))
            ->setTrigger('type', "1", 'img', false)
            ->setTrigger('type', "2", 'js', false)
            ->setAjax()
            ->fetch();
    }

    /**
    * 广告位修改操作
    * @author rainfer <81818832@qq.com>
    */
    public function adUpdate()
    {
        $type    = input('type', 1, 'intval');
        $sl_data = [
            'id'       => input('id'),
            'lang'     => input('lang', 'zh-cn'),
            'adtypeid' => input('adtypeid'),
            'name'     => input('name'),
            'url'      => input('url'),
            'sort'     => input('sort'),
            'type'     => $type,
            'content'  => input('content'),
            'js'       => input('js'),
            'img'      => ($type == 1) ? input('img') : '',
        ];
        $rst     = $this->ad_model->isUpdate(true)->save($sl_data);
        if ($rst !== false) {
            $this->success('广告修改成功', 'adIndex', ['is_frame' => 1]);
        } else {
            $this->error('广告修改失败', 'adIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 广告删除
     * @author rainfer <81818832@qq.com>
     */
    public function adDel()
    {
        $id  = input('id');
        $rst = $this->ad_model->where('id', $id)->delete();
        if ($rst) {
            $this->success('广告删除成功', 'adIndex');
        } else {
            $this->error('广告删除失败', 'adIndex');
        }
    }

    /**
     * 批量排序
     * @author rainfer <81818832@qq.com>
     */
    public function adOrder()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', url('admin/Plug/plug_ad_list'));
        } else {
            $post = input('post.');
            foreach ($post as $id => $sort) {
                $this->ad_model->where('id', $id)->setField('sort', $sort);
            }
            $this->success('广告排序更新成功', 'adIndex');
        }
    }

    /**
     * 广告状态
     * @author rainfer <81818832@qq.com>
     */
    public function adState()
    {
        $id = input('id', 0, 'intval');
        if (!$id) {
            $this->error('广告不存在', 'adIndex');
        }
        $status = $this->ad_model->where('id', $id)->value('status');
        $status = $status ? 0 : 1;
        $this->link_model->where('id', $id)->setField('status', $status);
        $this->success($status ? '启用' : '禁用', null, ['result' => $status]);
    }

    /**
     * 广告位列表
     * @author rainfer <81818832@qq.com>
     */
    public function adtypeIndex()
    {
        $key = input('key', '');
        $map = [];
        $key && $map[] = ['name', 'like', "%" . $key . "%"];
        $adtype_list = $this->adtype_model->where($map)->order('sort')->paginate(config('paginate.list_rows'), false, ['query' => request()->param()]);
        $page        = $adtype_list->render();
        $page        = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $page);
        $data        = $adtype_list->items();
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '广告位名称', 'field' => 'name'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
        ];
        //主键
        $pk = 'id';
        //右侧操作按钮
        $right_action = [
            'edit'   => ['href' => url('adtypeEdit'), 'is_pop' => 1],
            'delete' => url('adtypeDel')
        ];
        $order        = url('adtypeOrder');
        $search       = [
            ['text', 'key', '', $key, '', '', 'text', ['placeholder' => '输入链接名称或URL', 'is_formgroup' => false], 'search-query'],
            ['button', '搜索', ['class' => 'btn btn-purple btn-sm search-query ajax-search-form', 'type' => 'submit', 'icon_l' => 'ace-icon fa fa-search icon-on-right bigger-110']]
        ];
        $form         = [
            'href'  => url('adtypeIndex'),
            'class' => 'form-search',
        ];
        //实例化表单类
        $widget = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, $page, $order, '', 1);
        } else {
            return $widget
                ->addToparea(['add' => ['href' => url('adtypeAdd'), 'is_pop' => 1]], [], $search, $form)
                ->addtable($fields, $pk, $data, $right_action, $page, $order)
                ->setButton()
                ->fetch();
        }
    }

    /**
     * 广告位添加操作
     * @author rainfer <81818832@qq.com>
     */
    public function adtypeAdd()
    {
        $widget = new Widget();
        return $widget
            ->addText('name', '广告位名称', '', '', 'required', 'text', ['placeholder' => '输入广告位名称'])
            ->addText('sort', '排序', '50', '* 从小到大排序', 'required', 'number')
            ->setUrl(url('adtypeSave'))
            ->setAjax()
            ->fetch();
    }

    /**
     * 广告位添加操作
     * @author rainfer <81818832@qq.com>
     */
    public function adtypeSave()
    {
        $rst = $this->adtype_model->insert(input('post.'));
        if ($rst !== false) {
            $this->success('广告位添加成功', 'adtypeIndex', ['is_frame' => 1]);
        } else {
            $this->error('广告位添加失败', 'adtypeIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 广告位修改操作
     * @author rainfer <81818832@qq.com>
     */
    public function adtypeEdit()
    {
        $id     = input('id');
        $adtype = $this->adtype_model->find($id);
        $widget = new Widget();
        return $widget
            ->addText('id', '', $id, '', '', 'hidden')
            ->addText('name', '广告位名称', $adtype['name'], '', 'required', 'text', ['placeholder' => '输入广告位名称'])
            ->addText('sort', '排序', $adtype['sort'], '* 从小到大排序', 'required', 'number')
            ->setUrl(url('adtypeUpdate'))
            ->setAjax()
            ->fetch();
    }

    /**
     * 广告位修改操作
     * @author rainfer <81818832@qq.com>
     */
    public function adtypeUpdate()
    {
        $rst = $this->adtype_model->isUpdate(true)->save(input('post.'));
        if ($rst !== false) {
            $this->success('广告位修改成功', 'adtypeIndex', ['is_frame' => 1]);
        } else {
            $this->error('广告位修改失败', 'adtypeIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 广告位删除
     * @author rainfer <81818832@qq.com>
     */
    public function adtypeDel()
    {
        $id  = input('id');
        $rst = $this->ad_model->where('adtypeid', $id)->find();
        if ($rst) {
            $this->error('请先删除该广告位的广告', 'adIndex');
        }
        if ($rst !== false) {
            $rst = $this->adtype_model->where('id', $id)->delete();//删除广告位
            if ($rst !== false) {
                $this->success('广告位删除成功', 'adtypeIndex');
            } else {
                $this->error('广告位删除失败', 'adtypeIndex');
            }
        } else {
            $this->error('广告位删除失败', 'adtypeIndex');
        }
    }

    /**
     * 广告位排序
     * @author rainfer <81818832@qq.com>
     */
    public function adtypeOrder()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', 'adtypeIndex');
        } else {
            $post = input('post.');
            foreach ($post as $id => $sort) {
                $this->adtype_model->where('id', $id)->setField('sort', $sort);
            }
            $this->success('广告位排序更新成功', 'adtypeIndex');
        }
    }

    /**
     * 文章来源列表
     * @author rainfer <81818832@qq.com>
     */
    public function sourceIndex()
    {
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '来源名称', 'field' => 'name'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
        ];
        //主键
        $pk = 'id';
        //数据&页码
        $source = $this->source_model->order('sort,id desc')->paginate(config('paginate.list_rows'));
        $page   = $source->render();
        $page   = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $page);
        $data   = $source->items();
        //右侧操作按钮
        $right_action = [
            'edit'   => ['href' => url('sourceEdit'), 'is_pop' => 1],
            'delete' => url('sourceDel')
        ];
        $order        = url('sourceOrder');
        //实例化表单类
        $widget = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, $page, $order, '', 1);
        } else {
            return $widget
                ->addToparea(['add' => ['href' => url('sourceAdd'), 'is_pop' => true]])
                ->addtable($fields, $pk, $data, $right_action, $page, $order)
                ->setButton()
                ->fetch();
        }
    }

    /**
     * 添加来源操作
     * @author rainfer <81818832@qq.com>
     */
    public function sourceAdd()
    {
        $widget = new Widget();
        return $widget
            ->addText('name', '来源名称', '', '', 'required', 'text', ['placeholder' => '输入来源名称'])
            ->addText('sort', '排序', '50', '* 从小到大排序', 'required', 'number')
            ->setUrl(url('sourceSave'))
            ->setAjax()
            ->fetch();
    }

    /**
    * 添加来源操作
    * @author rainfer <81818832@qq.com>
    */
    public function sourceSave()
    {
        $data = input('post.');
        $rst  = $this->source_model->insert($data);
        if ($rst) {
            $this->success('来源添加成功', 'sourceIndex', ['is_frame' => 1]);
        } else {
            $this->error('来源添加失败', 'sourceIndex');
        }
    }

    /**
     * 来源修改返回值操作
     * @author rainfer <81818832@qq.com>
     */
    public function sourceEdit()
    {
        $id     = input('id');
        $source = $this->source_model->where('id', $id)->find();
        $widget = new Widget();
        return $widget
            ->addText('id', '', $source['id'], '', '', 'hidden')
            ->addText('name', '来源名称', $source['name'], '', 'required', 'text', ['placeholder' => '输入来源名称'])
            ->addText('sort', '排序', $source['sort'], '* 从小到大排序', 'required', 'number')
            ->setUrl(url('sourceUpdate'))
            ->setAjax()
            ->fetch();
    }

    /**
     * 来源修改返回值操作
     * @author rainfer <81818832@qq.com>
     */
    public function sourceUpdate()
    {
        $sl_data = [
            'id'   => input('id'),
            'name' => input('name'),
            'sort' => input('sort'),
        ];
        $rst     = $this->source_model->isUpdate(true)->save($sl_data);
        if ($rst !== false) {
            $this->success('来源修改成功', 'sourceIndex', ['is_frame' => 1]);
        } else {
            $this->error('来源修改失败', 'sourceIndex', ['is_frame' => 1]);
        }
    }

    /**
     * 来源删除操作
     * @author rainfer <81818832@qq.com>
     */
    public function sourceDel()
    {
        $rst = $this->source_model->where('id', input('id'))->delete();
        if ($rst !== false) {
            $this->success('来源删除成功', 'sourceIndex');
        } else {
            $this->error('来源删除失败', 'sourceIndex');
        }
    }

    /**
     * 来源排序
     * @author rainfer <81818832@qq.com>
     */
    public function sourceOrder()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', 'sourceIndex');
        } else {
            foreach (input('post.') as $id => $sort) {
                $this->source_model->where('id', $id)->setField('sort', $sort);
            }
            $this->success('排序更新成功', 'sourceIndex');
        }
    }
}
