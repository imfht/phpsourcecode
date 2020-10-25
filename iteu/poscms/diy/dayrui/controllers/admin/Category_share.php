<?php



class Category_share extends M_Controller {

    private $field;
    private $thumb;
    private $content;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('共享栏目') => array('admin/category_share/index', 'list-ul'),
                fc_lang('自定义URL') => array('admin/category_share/url', 'code-fork'),
                fc_lang('自定义字段') => array('admin/field/index/rname/category-share/rid/0', 'plus-square'),
                fc_lang('添加') => array('admin/category_share/add', 'plus'),
                fc_lang('更新缓存') => array('admin/module/cache/dir/share', 'refresh'),
            )),
            'module' => $this->get_module(SITE_ID),
        ));
        $this->thumb = array(
            'thumb' => array(
                'name' => fc_lang('缩略图'),
                'ismain' => 1,
                'fieldtype' => 'File',
                'fieldname' => 'thumb',
                'setting' => array(
                    'option' => array(
                        'ext' => 'jpg,gif,png',
                        'size' => 10,
                    )
                )
            )
        );
        $this->content = array(
            'content' => array(
                'name' => fc_lang('单网页内容'),
                'ismain' => 1,
                'fieldtype' => 'Ueditor',
                'fieldname' => 'content',
                'setting' => array(
                    'option' => array(
                        'mode' => 1,
                        'height' => 300,
                        'width' => '100%'
                    )
                )
            )
        );
        $this->field = array();
        $field = $this->db
                       ->where('disabled', 0)
                       ->where('relatedid', 0)
                       ->where('relatedname', 'category-share')
                        ->order_by('displayorder ASC, id ASC')
                        ->get('field')
                        ->result_array();
        if ($field) {
            foreach ($field as $t) {
                $t['setting'] = dr_string2array($t['setting']);
                $this->field[$t['fieldname']] = $t;
            }
            unset($field);
        }
        $this->load->model('category_share_model');
    }

    /*
     * 删除
     */
    public function delete($ids) {

        if (!$ids) {
            return NULL;
        }

        // 筛选栏目id
        $catid = '';
        $category = $this->get_cache('module-'.SITE_ID.'-share', 'category');
        foreach ($ids as $id) {
            $catid.= ','.($category[$id]['childids'] ? $category[$id]['childids'] : $id);
        }

        $catid = explode(',', trim($catid, ','));
        $catid = array_flip(array_flip($catid));
        $data = $this->db->select('tableid,mid,id')->where_in('catid', $catid)->get(SITE_ID.'_share')->result_array();
        if ($data) {
            // 逐一删除内容
            foreach ($data as $t) {
                $this->load->add_package_path(FCPATH.'module/'.$t['mid'].'/');
                $this->load->model('content_model');
                $this->content_model->mdir = $t['mid'];
                $this->content_model->prefix = $this->db->dbprefix(SITE_ID.'_'.$t['mid']);
                $this->content_model->delete_for_id((int)$t['id'], (int)$t['tableid']);
            }
        }

        // 删除栏目
        $this->db->where_in('id', $catid)->delete($this->category_share_model->tablename);

        $this->load->model('attachment_model');
        foreach ($catid as $id) {
            // 删除导航数据
            $this->db->where('mark', 'module-'.APP_DIR.'-'.$id)->delete(SITE_ID.'_navigator');
            // 删除栏目附件
            $this->attachment_model->delete_for_table($this->category_share_model->tablename.'-'.$id);
        }
    }

    /**
     * 获取树结构
     */
    protected function _get_tree($data) {

        $tree = array();
        $category = $this->get_cache('module-'.SITE_ID.'-share', 'category');

        foreach($data as $t) {
            $url = $category[$t['id']]['url'] ? $category[$t['id']]['url'] : 'index.php?c=category&id='.$t['id'];
            //$t['child'] = MODULE_PCATE_POST ? 0 : $t['child'];
            $t['option'] = '<a class="ago" href="'.$url.'" target="_blank"> <i class="fa fa-send"></i> '.fc_lang('访问').'</a>';
            // 可操作的权限判断
            if ($this->is_auth('admin/category_share/add')) {
                $t['option'].= '<a class="aadd onloading" href='.dr_url('category_share/add', array('id' => $t['id'])).'> <i class="fa fa-plus"></i> '.fc_lang('添加子类').'</a>';
            }
            if ($this->is_auth('admin/category_share/edit')) {
                $t['option'].= '<a class="aedit onloading" href='.dr_url('category_share/edit', array('id' => $t['id'])).'> <i class="fa fa-edit"></i> '.fc_lang('修改').'</a>';
            }
            if ($t['tid']==1 && !$t['child'] && $this->is_auth($t['mid'].'/admin/home/add')) {
                $t['option'].= '<a class="aadd onloading" href='.dr_url($t['mid'].'/home/add', array('catid' => $t['id'])).'> <i class="fa fa-pencil"></i> '.fc_lang('发布内容').'</a>';
            }
            if ($t['tid']==1 && !$t['child']) {
                $t['option'].= '<a class="ago onloading" href='.dr_url($t['mid'].'/home/index', array('catid' => $t['id'])).'> <i class="fa fa-navicon"></i> '.fc_lang('管理').'</a>';
            }
            // 判断是否生成静态
            if ($t['setting']['html']) {
                $t['html'] = '<a class="badge badge-success" href='.dr_url('category_share/html', array('id' => $t['id'])).'> '.fc_lang('是').' </a>';
            } else {
                $t['html'] = '<a class="badge badge-warning" href='.dr_url('category_share/html', array('id' => $t['id'])).'> '.fc_lang('否').' </a>';
            }
            // 栏目类型
            $t['type'] = '<span class="badge badge-info"> '.fc_lang('单网页').' </span>';
            if ($t['tid'] == 1) {
                $t['type'] = '<span class="badge badge-success"> '.fc_lang('共享模块').' </span>';
            } elseif ($t['tid'] == 2) {
                $t['type'] = '<span class="badge badge-warning"> '.fc_lang('外部链接').' </span>';
                $t['html'] = '';
            }
            $t['dirname'] = dr_strcut($t['dirname'], 15);
            $tree[$t['id']] = $t;
        }

        return $tree;
    }

    /**
     * 开启和关闭生成静态功能
     */
    public function html() {
        if ($this->is_auth('admin/category_share/edit')) {
            $id = (int)$this->input->get('id');
            $data = $this->category_share_model->get($id);
            $value = $data['setting']['html'] == 1 ? 0 : 1;
            $data['setting']['html'] = $value;
            $this->db->where('id', $id)->update(
                $this->category_share_model->tablename,
                array(
                    'setting' => dr_array2string($data['setting'])
                )
            );
            $name = $value ? '开启静态生成功能' : '关闭静态生成功能';
            $this->system_log('共享栏目【'.$data['name'].'】'.$name); // 记录日志
            $this->clear_cache('module');
            $this->admin_msg(fc_lang($name), $_SERVER['HTTP_REFERER'], 1);
        } else {
            $this->admin_msg(fc_lang('您无权限操作'));
        }
    }

    /**
     * 批量自定义URL
     */
    public function url() {
        $category = $this->get_cache('module-'.SITE_ID.'-share', 'category');
        if (IS_POST) {
            $catid = $this->input->post('catid');
            if ($catid) {
                foreach ($catid as $id) {
                    $setting = $category[$id]['setting'];
                    if ($setting) {
                        $setting['urlrule'] = (int)$this->input->post('urlrule');
                        $this->db->where('id', $id)->update($this->category_share_model->tablename, array(
                            'setting' => dr_array2string($setting)
                        ));
                    }
                }
                $this->clear_cache('module');
                $this->admin_msg(fc_lang('总共批量设置了%s个栏目<br>请更新缓存和更新地址', count($catid)), dr_url('category_share/index'), 1, 5);
            } else {
                $error = fc_lang('请选择一个的栏目');
            }
        }
        $this->template->assign(array(
            'error' => $error,
            'select' => $this->select_category($category, 0, ' class=\'form-control\' id=\'dr_catid\' name=\'catid[]\' multiple style="min-width:200px;height:250px;"', ''),
        ));
        $this->template->display('category_url.html');
    }

    /**
     * 首页
     */
    public function index() {

        if (IS_POST) {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            if ($this->input->post('action') == 'order') {
                $data = $this->input->post('data');
                foreach ($ids as $id) {
                    $this->db->where('id', $id)->update($this->category_share_model->tablename, $data[$id]);
                }
                $this->clear_cache('module');
                $this->system_log('排序站点【#'.SITE_ID.'】共享栏目【#'.@implode(',', $ids).'】'); // 记录日志
                exit(dr_json(1, fc_lang('操作成功')));
            } else {
                if (!$this->is_auth('admin/category_share/index')) {
                    exit(dr_json(0, fc_lang('您无权限操作')));
                }
                $this->delete($ids);
                $this->system_log('删除站点【#'.SITE_ID.'】共享栏目【#'.@implode(',', $ids).'】'); // 记录日志
                $this->clear_cache('module');
                exit(dr_json(1, fc_lang('操作成功')));

            }
        }

        $this->load->library('dtree');
        $this->category_share_model->repair();
        $this->dtree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $this->dtree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $tree = array();
        $data = $this->category_share_model->get_data();
        if ($data) {
            $tree = $this->_get_tree($data);
        }
        $str = "<tr class='\$class'>";
        $str.= "<td align='right'><input name='ids[]' type='checkbox' class='dr_select toggle md-check' value='\$id' /></td>";
        $str.= "<td><input class='input-text displayorder' type='text' name='data[\$id][displayorder]' value='\$displayorder' /></td>";
        $str.= "<td>\$id</td>";
        if ($this->is_auth('admin/category_share/edit')) {
            $str.= "<td>\$spacer<a class='onloading' href='".dr_url('category_share/edit')."&id=\$id'>\$name</a>  \$parent</td>";
        } else {
            $str.= "<td>\$spacer\$name  \$parent</td>";
        }
        $str.= "<td>\$dirname</td>";
        $str.= "<td>\$type</td>";
        $str.= "<td>\$mid</td>";
        $str.= "<td>\$html</td>";
        $str.= "<td class='dr_option'>\$option</td>";
        $str.= "</tr>";
        $this->dtree->init($tree);

        $this->template->assign(array(
            'page' => (int)$this->input->get('page'),
            'list' => $this->dtree->get_tree(0, $str),
        ));
        $this->template->display('category_share_index.html');
    }

    /**
     * 添加
     */
    public function add() {

        $id = (int)$this->input->get('id');
        $data = array('pid' => $id);
        $result	= '';

        // 初始化配置信息
        if ($id){
            $parent = $this->category_share_model->get($id);
            $data['tid'] = $parent['tid'];
            $data['mid'] = $parent['mid'];
            $data['setting'] = $parent['setting'];
            unset($parent);
        } else {
            $data['setting']['template']['list'] = 'list.html';
            $data['setting']['template']['show'] = 'show.html';
            $data['setting']['template']['extend'] = 'extend.html';
            $data['setting']['template']['category'] = 'category.html';
            $data['setting']['template']['search'] = 'search.html';
            $data['setting']['template']['page'] = 'page.html';
            $data['setting']['template']['pagesize'] = 20;
            $data['setting']['seo']['list_title'] = '[第{page}页{join}]{name}{join}{SITE_NAME}';
            $data['setting']['seo']['show_title'] = '[第{page}页{join}]{title}{join}{catname}{join}{SITE_NAME}';
            $data['setting']['seo']['extend_title'] = '{extend}{join}{title}{join}{catname}{join}{SITE_NAME}';
        }

        if (IS_POST) {
            $field = $this->field ? array_merge($this->field, $this->thumb, $this->content) : array_merge($this->thumb, $this->content);
            $data = $this->input->post('data', TRUE);
            $backurl = $this->input->post('backurl');
            $tmp = $this->validate_filter($field);
            if ($tmp) {
                if (isset($tmp['error'])) {
                    $this->admin_msg($tmp['msg']);
                } else {
                    // 删除老数据
                    foreach ($field as $i => $t) {
                        unset($data[$i]);
                    }
                    // 归类新数据
                    foreach ($tmp[1] as $i => $t) {
                        if (isset($field[$i])
                            || strpos($i, '_lng')
                            || strpos($i, '_lat')) {
                            $data[$i] = $t;
                        }
                    }
                }
            }
            if ($this->input->post('_all') == 1) {
                $names = $this->input->post('names', TRUE);
                $number	= $this->category_share_model->add_all($names, $data, $field);
                $this->system_log('批量添加站点【#'.SITE_ID.'】共享栏目【'.$number.'个】'); // 记录日志
                //$this->clear_cache('module');
                $this->admin_msg(fc_lang('批量添加栏目%s个', $number), dr_url('category_share/index'), 1);
            } else {
                $result	= $this->category_share_model->add($data, $field);
                if (is_numeric($result)) {
                    $this->clear_cache('module');
                    $this->system_log('添加站点【#'.SITE_ID.'】共享栏目【#'.$result.'】'); // 记录日志
                    $this->attachment_handle($this->uid, $this->category_share_model->tablename.'-'.$result, $field);
                    $this->admin_msg(fc_lang('操作成功'), $backurl, 1, 2);
                }
            }
        }

        $this->template->assign(array(
            'id' => $id,
            'page' => 0,
            'data' => $data,
            'role' => $this->dcache->get('role'),
            'content' => $this->field_input($this->content, $data, TRUE),
            'thumb' => $this->field_input($this->thumb, $data, TRUE),
            'field' => $this->field_input($this->field, $data, TRUE),
            'result' => $result,
            'select' => $this->select_category($this->category_share_model->get_data(), $id, 'name=\'data[pid]\'', fc_lang('顶级栏目')),
            'backurl' => $backurl ? $backurl : $_SERVER['HTTP_REFERER'],
        ));
        $this->template->display('category_share_add.html');
    }

    /**
     * 修改
     */
    public function edit() {

        $id = (int)$this->input->get('id');
        $data = $this->category_share_model->get($id);
        $page = (int)$this->input->get('page');
        $result	= '';
        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

        if (IS_POST) {
            $field = $this->field ? array_merge($this->field, $this->thumb, $this->content) : array_merge($this->thumb, $this->content);
            $_data = $data;
            $page = (int)$this->input->post('page');
            $data = $this->input->post('data', TRUE);
            $tmp = $this->validate_filter($field);
            if ($tmp) {
                if (isset($tmp['error'])) {
                    $this->admin_msg($tmp['msg']);
                } else {
                    // 删除老数据
                    foreach ($field as $i => $t) {
                        unset($data[$i]);
                    }
                    // 归类新数据
                    foreach ($tmp[1] as $i => $t) {
                        if (isset($field[$i])
                            || strpos($i, '_lng')
                            || strpos($i, '_lat')) {
                            $data[$i] = $t;
                        }
                    }
                }
            }
            $data['pid'] = $data['pid'] == $id ? $_data['pid'] : $data['pid'];
            $data['rule'] = $this->input->post('rule');
            $result	= $this->category_share_model->edit($id, $data, $_data, $field);
            $this->category_share_model->syn($data, $_data);
            $data['id']	= $id;
            $data['permission'] = $data['rule'];
            $this->attachment_handle($this->uid, $this->category_share_model->tablename.'-'.$id, $field, $_data);
            //$this->clear_cache('module');
            $this->system_log('修改站点【#'.SITE_ID.'】共享栏目【#'.$id.'】'); // 记录日志
            $this->admin_msg(fc_lang('操作成功'), $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : dr_url('category_share/edit', array('id' => $id)), 1, 2);
        }

        $category = $this->category_share_model->get_data();
        $this->template->assign(array(
            'id' => $id,
            'page' => $page,
            'data' => $data,
            'role' => $this->get_cache('role'),
            'content' => $this->field_input($this->content, $data, TRUE),
            'thumb' => $this->new_field_input($this->thumb, $data, TRUE),
            'field' => $this->new_field_input($this->field, $data, TRUE),
            'result' => $result,
            'select' => $this->select_category($category, $data['pid'], ' class=\'form-control\' name=\'data[pid]\'', fc_lang('顶级栏目')),
            'select_syn' => $this->select_category($category, 0, ' class=\'form-control\' id="dr_synid" name=\'synid[]\' multiple style="min-width:150px;height:200px;"', '')
        ));
        $this->template->display('category_share_add.html');
    }

    /**
     * Ajax调用栏目附加字段
     *
     * @return void
     */
    public function field() {

    }

    /**
     * 设置规则
     */
    public function rule() {

        $id = $this->input->get('id');
        $catid = $this->input->get('catid');
        $data = $this->category_share_model->get_permission($catid);

        if (IS_POST) {
            $temp = $data[$id];
            $value = $this->input->post('data');
            $data[$id] = $value;
            $data[$id]['add'] = $temp['add'];
            $data[$id]['del'] = $temp['del'];
            $data[$id]['show'] = $temp['show'];
            $data[$id]['edit'] = $temp['edit'];
            $data[$id]['forbidden'] = $temp['forbidden'];
            $this->db->where('id', $catid)->update($this->category_share_model->tablename, array(
                'permission' => dr_array2string($data)
            ));
            //$this->clear_cache('module');
            $this->system_log('站点【#'.SITE_ID.'】共享栏目【#'.$catid.'】权限设置'); // 记录日志
            exit;
        }

        $html = '<select name="data[verify]"><option value="0"> -- </option>';
        $verify = $this->get_cache('verify');
        if ($verify) {
            foreach ($verify as $t) {
                $html.= '<option value="'.$t['id'].'" '.($data[$id]['verify'] == $t['id'] ? 'selected' : '').'> '.$t['name'].'('.$t['num'].') </option>';
            }
        }
        $html.= '</select>';

        $this->template->assign(array(
            'data' => $data[$id],
            'verify' => $html,
            'extend' => 1
        ));
        $this->template->display('category_rule.html');
    }

    public function select() {
        $id = (int)$this->input->get('id');
        echo $this->select_category($this->category_share_model->get_data(), $id, ( $id ? 'disabled ' : '').' class=\'form-control\' name=\'module[catid]\' onChange=\'dr_select_category(this.value)\'', fc_lang('全部栏目'));
    }

    /**
     * 栏目选择
     *
     * @param array			$data		栏目数据
     * @param intval/array	$id			被选中的ID，多选是可以是数组
     * @param string		$str		属性
     * @param string		$default	默认选项
     * @param intval		$onlysub	只可选择子栏目
     * @param intval		$is_push	是否验证权限
     * @param intval		$is_first	是否返回第一个可用栏目id
     * @return string
     */
    public function select_category($data, $id = 0, $str = '', $default = ' -- ', $onlysub = 0, $is_push = 0, $is_first = 0) {

        $cache = md5(dr_array2string($data).dr_array2string($id).$str.$default.$onlysub.$is_push.$is_first.$this->member['uid']);
        if ($cache_data = $this->get_cache_data($cache)) {
            return $cache_data;
        }

        $tree = array();
        $first = 0; // 第一个可用栏目
        $string = '<select class=\'form-control\' '.$str.'>';

        if ($default) {
            $string.= "<option value='0'>$default</option>";
        }

        if (is_array($data)) {
            foreach($data as $t) {
                // 外部链接不显示
                if (isset($t['setting']['linkurl']) && $t['setting']['linkurl']) {
                    continue;
                }
                // 验证权限
                if (MODULE_PCATE_POST) {
                    // 父栏目可发布时的权限
                    if ($is_push) {
                        if (IS_MEMBER && !$this->module_rule[$t['id']]['add']) {
                            // 会员中心用户发布权限
                            if ($is_push && $t['child']) {
                                $t['html_disabled'] = 1;
                            } else {
                                continue;
                            }
                        } elseif (IS_ADMIN && !$this->is_category_auth($t['id'], 'add') && !$this->is_category_auth($t['id'], 'edit')) {
                            // 后台角色发布和修改权限
                            if ($is_push && $t['child']) {
                                $t['html_disabled'] = 1;
                            } else {
                                continue;
                            }
                        }
                    } else {
                        // 是否可选子栏目
                        $t['html_disabled'] = $onlysub ? 1 : 0;
                    }
                    // 选中操作
                    $t['selected'] = '';
                    if (is_array($id)) {
                        $t['selected'] = in_array($t['id'], $id) ? 'selected' : '';
                    } elseif(is_numeric($id)) {
                        $t['selected'] = $id == $t['id'] ? 'selected' : '';
                    }
                } else {
                    // 正常栏目权限
                    if ($is_push && $t['child'] == 0) {
                        if (IS_MEMBER && !$this->module_rule[$t['id']]['add']) {
                            continue;
                        } elseif (IS_ADMIN && !$this->is_category_auth($t['id'], 'add') && !$this->is_category_auth($t['id'], 'edit')) {
                            continue;
                        }
                    }
                    // 选中操作
                    $t['selected'] = '';
                    if (is_array($id)) {
                        $t['selected'] = in_array($t['id'], $id) ? 'selected' : '';
                    } elseif(is_numeric($id)) {
                        $t['selected'] = $id == $t['id'] ? 'selected' : '';
                    }
                    // 是否可选子栏目
                    $t['html_disabled'] = $onlysub && $t['child'] != 0 ? 1 : 0;
                }
                // 第一个可用子栏目
                if ($first == 0 && $t['child'] == 0) {
                    $first = $t['id'];
                }
                unset($t['permission'], $t['setting'], $t['catids'], $t['url']);
                $tree[$t['id']] = $t;
            }
        }

        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $str2 = "<optgroup label='\$spacer \$name'></optgroup>";

        $this->load->library('dtree');
        $this->dtree->init($tree);

        $string.= $this->dtree->get_tree_category(0, $str, $str2);
        $string.= '</select>';

        if ($is_first) {
            $mark = "value='";
            $first2 = (int)substr($string, strpos($string, $mark) + strlen($mark));
            $first = $first2 ? $first2 : $first;
        }
        $data = $is_first ? array($string, $first) : $string;
        if ($tree) {
            $this->set_cache_data($cache, $data, 7200);
        }

        return $data;
    }
}