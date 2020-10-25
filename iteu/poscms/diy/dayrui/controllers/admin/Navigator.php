<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Navigator extends M_Controller {

	private $type;
	private $menu;
	private $field;
    
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$use = $menu = array();
		$data = explode(',', SITE_NAVIGATOR);
		$this->type = (int)$this->input->get('type');
		foreach ($data as $i => $name) {
			if ($name) {
                $use[$i] = $i;
				$menu[$name] = array('admin/navigator/index'.(isset($_GET['type']) || $i ? '/type/'.$i : ''), 'map-marker');
				$this->menu[$i] = $name;
			}
		}
        // 设置默认选中
        if (!isset($use[$this->type])) {
            $this->type = @reset($use);
            $_SERVER['QUERY_STRING'].= '&type='.$this->type;
        }
        // 带分类参数时的选中
        if (isset($_GET['pid'])) {
            $_SERVER['QUERY_STRING'] = str_replace('&pid='.$_GET['pid'], '', $_SERVER['QUERY_STRING']);
        }
        // 存在导航配置时才显示添加链接
        if ($this->menu) {
            $menu[fc_lang('添加')] = array('admin/navigator/add/type/'.$this->type, 'plus');
        }
		$this->template->assign('menu', $this->get_menu_v3($menu));
		$this->template->assign('name', $this->menu[$this->type]);
        // 导航默认字段
		$this->field = array(
			'name' => array(
				'ismain' => 1,
				'fieldname' => 'name',
				'fieldtype' => 'Text',
				'setting' => array(
					'option' => array(
						'width' => 200,
					)
				)
			),
			'title' => array(
				'ismain' => 1,
				'fieldname' => 'title',
				'fieldtype'	=> 'Text',
				'setting' => array(
					'option' => array(
						'width' => 300,
					)
				)
			),
			'url' => array(
				'name' => '',
				'ismain' => 1,
				'fieldname' => 'url',
				'fieldtype'	=> 'Text',
				'setting' => array(
					'option' => array(
						'width' => 400,
						'value' => 'http://',
					)
				)
			),
			'thumb' => array(
				'ismain' => 1,
				'fieldname' => 'thumb',
				'fieldtype' => 'File',
				'setting' => array(
					'option' => array(
						'ext' => 'jpeg,jpg,gif,png',
						'size' => 10,
					)
				)
			),
		);
        $this->load->model('page_model');
		$this->load->model('navigator_model');
    }
    
	/**
     * 管理列表
     */
    public function index() {
		
		if (IS_POST && $this->input->post('ids')) {
			$table = SITE_ID.'_navigator';
			if ($this->input->post('action') == 'del') {
				// 删除
                $ids = $this->input->post('ids');
				$this->navigator_model->delete($ids);
                $this->cache(1);
                $this->system_log('删除网站导航【'.$this->menu[$this->type].'#'.@implode(',', $ids).'】'); // 记录日志
			} elseif ($this->input->post('action') == 'order'
                && $this->is_auth('navigator/edit')) {
				// 修改
				$_ids = $this->input->post('ids');
				$_data = $this->input->post('data');
				foreach ($_ids as $id) {
					$this->db->where('id', (int)$id)->update($table, $_data[$id]);
				}
				$this->cache(1);
                $this->system_log('排序网站导航【'.$this->menu[$this->type].'#'.@implode(',', $_ids).'】'); // 记录日志
				unset($_ids, $_data);
			}
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
		
		$this->load->library('dtree');
		$this->dtree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$this->dtree->nbsp = '&nbsp;&nbsp;&nbsp;';
		
		$tree = array();
		$data = $this->navigator_model->get_data($this->type);
		
		if ($data) {
			foreach($data as $t) {
				$add = dr_url('navigator/add', array('pid' => $t['id'], 'type' => $this->type));
				$edit = dr_url('navigator/edit', array('id' => $t['id'], 'type' => $this->type));
				$t['option'] = '';
				$this->is_auth('admin/navigator/add') && $t['option'].= '<a class="aadd" title="'.fc_lang('添加').'" href="'.$add.'"> <i class="fa fa-plus"></i> '.fc_lang('添加').'</a>';
				$this->is_auth('admin/navigator/edit') && $t['option'].= '<a class="aedit" title="'.fc_lang('修改').'" href="'.$edit.'"> <i class="fa fa-edit"></i> '.fc_lang('修改').'</a>';
				$t['option'].= '<a class="ago" title="'.fc_lang('访问').'" href="'.$t['url'].'" target="_blank"> <i class="fa fa-paper-plane"></i> '.fc_lang('访问').'</a>';
                if (strpos($t['mark'], 'page') === 0) {
                    //1
                    $t['ntype'] = '<font color=blue>'.fc_lang('单页').'</font>';
                } elseif (strpos($t['mark'], 'module') === 0) {
                    //2
                    list($a, $dir, $catid) = explode('-', $t['mark']);
                    $t['ntype'] = '<font color=green>'.fc_lang('模块').'</font>';
                    if ($catid) {
                        $t['option'].= '<a class="aadd" href="'.dr_url($dir.'/category/add', array('id' => $catid)).'"> <i class="fa fa-plus"></i> '.fc_lang('添加栏目').'</a>';
                        $t['option'].= '<a class="aedit" href="'.dr_url($dir.'/category/edit', array('id' => $catid)).'"> <i class="fa fa-edit"></i> '.fc_lang('修改栏目').'</a>';
                    }
                } else {
                    //0
                    $t['ntype'] = fc_lang('链接');
                }
                $tree[$t['id']] = $t;
			}
		}
		
		$str = "<tr class='\$class'>";
		$str.= "<td><input name='ids[]' type='checkbox' class='dr_select toggle md-check' value='\$id' /></td>";
		$str.= "<td ><input class='input-text displayorder' type='text' name='data[\$id][displayorder]' value='\$displayorder' /></td>";
		$str.= "<td>\$id</td>";
		$str.= $this->is_auth('admin/navigator/edit') ? "<td>\$spacer<a href='".dr_url(APP_DIR.'/navigator/edit')."&id=\$id&type=".$this->type."'>\$name</a>  \$parent</td>" : "<td>\$spacer\$name  \$parent</td>";
		$str.= "<td>\$ntype</td>";
		$str.= "<td style='text-align:center'>";
		$str.= $this->is_auth('admin/navigator/edit') ? "<a href='".dr_url('navigator/target')."&id=\$id'><img src='".THEME_PATH."admin/images/\$target.gif' /></a>" : "<img src='".THEME_PATH."admin/images/\$target.gif' />";
		$str.= "</td>";
		$str.= "<td style='text-align:center'>";
		$str.= $this->is_auth('admin/navigator/edit') ? "<a href='".dr_url('navigator/show')."&id=\$id'><img src='".THEME_PATH."admin/images/\$show.gif' /></a>" : "<img src='".THEME_PATH."admin/images/\$show.gif' />";
		$str.= "</td>";
		$str.= "<td class='dr_option'>\$option</td>";
		$str.= "</tr>";
		
		$this->dtree->init($tree);
		
		$this->template->assign(array(
			'type' => $this->type,
			'list' => $this->dtree->get_tree(0, $str)
		));
		$this->template->display('navigator_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
		
		$pid = (int)$this->input->get('pid');
		
		if (IS_POST) {
			$data = $this->validate_filter($this->field);
            $ntype = (int)$this->input->post('ntype');
            if ($ntype == 0) {
                // 自定义
                $data[1]['mark'] = '';
            } elseif ($ntype == 1) {
                // 单页
                $page = $this->input->post('page');
                if (!$page['id']) {
                    // 单页不存在
                    $data = array(
                        'msg' => fc_lang('单页不存在'),
                        'error' => 1,
                    );
                } else {
                    $ppid = $page['id'];
                    $temp = $this->page_model->get($page['id']);
                    if ($temp) {
                        $data[1]['url'] = $temp['url'];
                        $data[1]['mark'] = 'page-'.$page['id'];
                        $data[1]['name'] = $data[1]['name'] ? $data[1]['name'] : $temp['name'];
                        $data[1]['thumb'] = $data[1]['thumb'] ? $data[1]['thumb'] : $temp['thumb'];
                        $data[1]['title'] = $temp['title'];
                        $data[1]['extend'] = (int)$page['extend'];
                        $data[1]['extends'] = array();
                        $childs = explode(',', $temp['childids']);
                        if ($childs && $data[1]['extend']) {
                            $page = $this->page_model->get_data_all();
                            foreach ($childs as $i) {
								$i != $ppid && $data[1]['extends'][$i] = $page[$i];
                            }
                        }
                        unset($childs);
                    } else {
                        // 单页不存在
                        $data = array(
                            'msg' => fc_lang('单页不存在'),
                            'error' => 1,
                        );
                    }
                }
                unset($temp, $page);
            } elseif ($ntype == 2) {
                // 模块
                $module = $this->input->post('module');
                if (!$module['dir']) {
                    // 模块不存在
                    $data = array(
                        'msg' => fc_lang('模块不存在'),
                        'error' => 1,
                    );
                } else {
                    $temp = $this->get_cache('module-'.SITE_ID.'-'.$module['dir']);
                    if ($temp) {
                        $catid = (int)$module['catid'];
                        $data[1]['url'] = $temp['url'];
                        $data[1]['mark'] = 'module-'.$module['dir'].'-0';
                        $data[1]['name'] = $data[1]['name'] ? $data[1]['name'] : $temp['name'];
                        $data[1]['extend'] = (int)$module['extend'];
                        $data[1]['extends'] = 0;
                        if ($catid) {
                            // 选择的有继承栏目
                            $data[1]['mark'] = 'module-'.$module['dir'].'-'.$catid;
                            if (isset($temp['category'][$catid]) && $temp['category'][$catid]) {
                                $data[1]['url'] = $temp['category'][$catid]['url'];
                                $data[1]['name'] = $temp['category'][$catid]['name'];
                                $data[1]['thumb'] = $data[1]['thumb'] ? $data[1]['thumb'] : $temp['category'][$catid]['thumb'];
                                $data[1]['extends'] = array();
                                $childs = explode(',', $temp['category'][$catid]['childids']);
                                if ($childs && $module['extend']) {
                                    foreach ($childs as $i) {
										$i != $catid && $data[1]['extends'][$i] = $temp['category'][$i];
                                    }
                                }
                                unset($childs);
                            } else {
                                $data[1]['extends'] = $temp['category'];
                            }
                        } else {
                            $data[1]['extends'] = $temp['category'];
                        }
                        unset($module);
                    } else {
                        // 模块不存在
                        $data = array(
                            'msg' => fc_lang('模块不存在'),
                            'error' => 1,
                        );
                    }
                }
                unset($temp, $page);
            }
			if (isset($data['error'])) {
				$error = $data['msg'];
				$data = $this->input->post('data');
			} else {
				$data[1]['pid'] = (int)$this->input->post('pid');
                $data[1]['type'] = (int)$this->type;
				$id = (int)$this->navigator_model->add($data[1]);
				$this->cache(1);
                $this->system_log('添加网站导航【'.$this->menu[$this->type].'#'.$id.'】'); // 记录日志
				$this->attachment_handle($this->uid, $this->navigator_model->tablename.'-'.$id, $this->field);
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('navigator/index', array('type' => $this->type)), 1);
			}
		} else {
            $error = '';
            $ntype = $ppid =  0;
            $data['extend'] = 1;
        }

		$this->template->assign(array(
			'data' => $data,
			'ntype' => $ntype,
			'error' => $error,
			'field' => $this->field,
			'select' => $this->_select($this->navigator_model->get_data($this->type), $pid, 'class="form-control" name=\'pid\'', fc_lang('作为顶级')),
			'select_page' => $this->_select($this->page_model->get_data_all(), (int)$ppid, 'class="form-control" name=\'page[id]\'', ''),
		));
		$this->template->display('navigator_add.html');
	}
	
	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$nav = $this->navigator_model->get_data($this->type);
		$data = $nav[$id];
		!$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
		
        if (strpos($data['mark'], 'page') === 0) {
            list($a, $ppid) = explode('-', $data['mark']);
            $ntype = 1;
        } elseif (strpos($data['mark'], 'module') === 0) {
            list($a, $dir, $catid) = explode('-', $data['mark']);
            $ntype = 2;
        } else {
            $ntype = 0;
        }

		if (IS_POST) {

			$post = $this->validate_filter($this->field);
            $extends = array();

            if ($ntype == 0) {
                // 自定义
                $post[1]['extend'] = 0;
            } elseif ($ntype == 1) {
                // 单页
                $page = $this->input->post('page');
                $post[1]['extend'] = $page['extend'];
                // 查询下级所有数据项
                if ($ppid && $page['extend']) {
                    $temp = $this->page_model->get($ppid);
                    if ($temp) {
                        $childs = explode(',', $temp['childids']);
                        if ($childs) {
                            $page = $this->page_model->get_data_all();
                            foreach ($childs as $i) {
								$i != $ppid && $extends[$i] = $page[$i];
                            }
                        }
                        unset($childs);
                    }
                    unset($temp);
                }
            } elseif ($ntype == 2) {
                // 模块
                $module = $this->input->post('module');
                $post[1]['extend'] = $module['extend'];
                // 查询下级所有数据项
                if ($dir && $module['extend']) {
                    $temp = $this->get_cache('module-'.SITE_ID.'-'.$dir);
                    if ($temp) {
                        // 选择的有继承栏目
                        if (isset($temp['category'][$catid]) && $temp['category'][$catid]) {
                            $childs = explode(',', $temp['category'][$catid]['childids']);
                            if ($childs) {
                                foreach ($childs as $i) {
									$i != $catid && $extends[$i] = $temp['category'][$i];
                                }
                            }
                            unset($childs);
                        } else {
                            $extends = $temp['category'];
                        }
                    }
                    unset($temp);
                }
                unset($module);
            }

			if (isset($post['error'])) {
				$data = $this->input->post('data');
				$error = $post['msg'];
			} else {
				$post[1]['pid'] = $this->input->post('pid');
				$id = (int)$this->navigator_model->edit($id, $post[1]);
                if ($ntype && $data['extend'] != $post[1]['extend']) {
                    $this->navigator_model->update_extend($data['childs'], $post[1]['extend']);
					$post[1]['extend'] && $extends && $this->navigator_model->set_extend($id, $data['mark'], $extends, $this->type);
                }
				$this->cache(1);
                $this->system_log('修改网站导航【'.$this->menu[$this->type].'#'.$id.'】'); // 记录日志
				$this->attachment_handle($this->uid, $this->navigator_model->tablename.'-'.$id, $this->field, $data);
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('navigator/index', array('type' => $this->type)), 1);
			}
		}
		
		$this->template->assign(array(
            'dir' => $dir,
			'data' => $data,
			'error' => $error,
            'ntype' => $ntype,
            'catid' => (int)$catid,
			'field' => $this->field,
			'select' => $this->_select($nav, $data['pid'], 'class="form-control" name=\'pid\'', fc_lang('作为顶级')),
            'select_page' => $this->_select($this->page_model->get_data_all(), (int)$ppid, 'class="form-control" disabled name=\'page[id]\'', ''),
		));
		$this->template->display('navigator_add.html');
	}
	
	/**
     * 新窗口打开
     */
    public function target() {
		if ($this->is_auth('admin/navigator/edit')) {
			$id = (int)$this->input->get('id');
			$data = $this->db
						 ->select('target,type')
						 ->where('id', $id)
						 ->limit(1)
						 ->get(SITE_ID.'_navigator')
						 ->row_array();
			$this->db->where('id', $id)->update(SITE_ID.'_navigator', array('target' => ($data['target'] == 1 ? 0 : 1)));
            $this->system_log('修改网站导航【'.$this->menu[$this->type].'#'.$id.'】'); // 记录日志
			$this->cache(1);
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('navigator/index', array('type' => $data['type'])), 1);
		} else {
			$this->admin_msg(fc_lang('您无权限操作'));
		}
    }
	
	/**
     * 显示
     */
    public function show() {
		if ($this->is_auth('admin/navigator/edit')) {
			$id = (int)$this->input->get('id');
			$data = $this->db
						 ->select('show,type')
						 ->where('id', $id)
						 ->limit(1)
						 ->get(SITE_ID.'_navigator')
						 ->row_array();
			$this->db->where('id', $id)->update(SITE_ID.'_navigator', array('show' => ($data['show'] == 1 ? 0 : 1)));
			$this->cache(1);
            $this->system_log('修改网站导航【'.$this->menu[$this->type].'#'.$id.'】'); // 记录日志
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('navigator/index', array('type' => $data['type'])), 1);
		} else {
			$this->admin_msg(fc_lang('您无权限操作'));
		}
    }
	
	/**
     * 缓存
	 * array(
	 *			'站点id' =>	array(
	 *						'导航类型id' => array(导航数据),
	 *						... ,
	 *					),
	 *			... ,
	 *		)
     */
    public function cache($update = 0) {
		$this->navigator_model->cache(isset($_GET['site']) && $_GET['site'] ? (int)$_GET['site'] : SITE_ID);
		((int)$_GET['admin']|| $update) or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
	
	/**
	 * 上级选择
	 *
	 * @param array			$data		数据
	 * @param intval/array	$id			被选中的ID
	 * @param string		$str		属性
	 * @param string		$default	默认选项
	 * @return string
	 */
	private function _select($data, $id = 0, $str = '', $default = ' -- ') {
	
		$tree = array();
		$string = '<select '.$str.'>';
		
		if ($default) $string.= "<option value='0'>$default</option>";
		
		if (is_array($data)) {
			foreach($data as $t) {
				$t['selected'] = ''; // 选中操作
				if (is_array($id)) {
					$t['selected'] = in_array($t['id'], $id) ? 'selected' : '';
				} elseif(is_numeric($id)) {
					$t['selected'] = $id == $t['id'] ? 'selected' : '';
				}
				
				$tree[$t['id']] = $t;
			}
		}
		
		$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
		$str2 = "<optgroup label='\$spacer \$name'></optgroup>";
		
		$this->load->library('dtree');
		$this->dtree->init($tree);
		
		$string.= $this->dtree->get_tree_category(0, $str, $str2);
		$string.= '</select>';
		
		return $string;
	}
}