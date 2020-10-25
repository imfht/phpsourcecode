<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Page_model extends CI_Model {
	
	public $link;
	public $tablename;
	private	$categorys;
	
	/**
	 * 单页模型
	 */
    public function __construct() {
        parent::__construct();

		$this->tablename = $this->db->dbprefix(SITE_ID.'_page');
    }
	
	/**
	 * 单页数据
	 *
	 * @param	intval	$id
	 * @return	array
	 */
	public function get($id) {
		return $this->db->where('id', $id)->get($this->tablename)->row_array();
	}

    /**
     * 全部模块单页
     *
     * @return	array
     */
    public function get_data() {

        $data = array();
        $_data = $this->db->where('module', APP_DIR)->order_by('displayorder ASC,id ASC')->get($this->tablename)->result_array();
        if (!$_data) {
            return $data;
        }

        foreach ($_data as $t) {
            $t['setting'] = dr_string2array($t['setting']);
            $data[$t['id']]	= $t;
        }

        return $data;
    }

    /**
     * 全部单页
     *
     * @return	array
     */
    public function get_data_all() {

        $data = array();
        $_data = $this->db->order_by('displayorder ASC,id ASC')->get($this->tablename)->result_array();
        if (!$_data) {
            return $data;
        }

        foreach ($_data as $t) {
            $t['setting'] = dr_string2array($t['setting']);
            $data[$t['id']]	= $t;
        }

        return $data;
    }
	
	/**
	 * 更新url
	 *
	 * @param	intval	$id
	 * @return	void
	 */
	private function _update_url($id, $site) {
	
		$data = $this->get($id);
        $site = $site ? $site : SITE_ID;

		$url = $data['urllink'] ? $data['urllink'] : dr_page_url($data, 0, $site);
		
		$this->site[$site]->where('id', $id)->update($site.'_page', array('url' => $url));
	}

    // 获取URL
    public function get_url($data, $site) {

		$data['urllink'] ?  $url = $data['urllink'] : $url = dr_page_url($data, 0, $site);

        return $url;
    }
	
	/**
	 * 添加
	 *
	 * @param	array	$data
	 * @return	intval
	 */
	public function add($data) {
	
		if (!$data) {
            return NULL;
        } elseif ($this->dirname_exitsts($data['dirname'])) {
            return fc_lang('目录已经存在了');
        }

		$insert = array(
            'url' => '',
            'pid' => (int)$data['pid'],
            'pids' => '',
            'child' => 0,
            'module' => APP_DIR,
			'urlrule' => (int)$data['urlrule'],
            'setting' => dr_array2string($this->input->post('setting')),
            'pdirname' => '',
            'childids' => '',
            'displayorder' => 0
        );
        $insert = array_merge($insert, $data);
		$this->db->insert($this->tablename, $insert);
		
		$id = $this->db->insert_id();
		
		$this->repair();
		$this->_update_url($id, SITE_ID);
		
		return $id;
	}
	
	/**
	 * 修改
	 *
	 * @param	intval	$id
	 * @param	array	$data
	 * @return	string
	 */
	public function edit($id, $data) {
	
		if (!$data || !$data['dirname']) {
            return fc_lang('对不起，数据被删除或者查询不存在');
        } elseif ($this->dirname_exitsts($data['dirname'], $id)) {
            return fc_lang('目录已经存在了');
        }

        $update = array(
            'pid' => (int)$data['pid'],
            'module' => APP_DIR,
            'setting' => dr_array2string($this->input->post('setting'))
        );
        $update = array_merge($update, $data);
		$this->db->where('id', $id)->update($this->tablename, $update);
		
		$this->repair();
		$this->_update_url($id, SITE_ID);
		
		return $id;
	}
	
	/**
	 * 同步
	 *
	 * @param	array	$syn
	 * @param	array	$data
	 * @return	NULL
	 */
	public function syn($syn, $urlrule) {
	
		if (!$syn) {
            return NULL;
        }
		
		foreach ($syn as $id) {
			$this->db->where('id', $id)->update($this->tablename, array(
                'urlrule' => (int)$urlrule
             ));
			$this->_update_url($id, SITE_ID);
		}
	}
	
	/**
	 * 判断目录
	 *
	 * @param	array	$data
	 * @return	bool
	 */
	private function dirname_exitsts($dir, $id = 0) {
	
		return $dir ? $this->db->where('dirname', $dir)->where('module', APP_DIR)->where('id<>', $id)->count_all_results($this->tablename) : 1;
	}
	
	/**
	 * 筛选数�?
	 *
	 * @param	array	$data
	 * @return	bool
	 */
	private function get_categorys($data = array()) {
	
		if (is_array($data) && !empty($data)) {
			foreach ($data as $catid => $c) {
				$this->categorys[$catid] = $c;
				$result = array();
				foreach ($this->categorys as $_k => $_v) {
					$_v['pid'] && $result[] = $_v;
				}
			}
		} 
		
		return true;
	}
	
	
	/**
	 * 所以父id
	 * 
	 * @param	integer	$catid	��ĿID
	 * @param	array	$pids	��Ŀ¼ID
	 * @param	integer	$n		���ҵĲ��?
	 * @return	string
	 */
	private function get_pids($catid, $pids = '', $n = 1) {
	
		if ($n > 5 || !is_array($this->categorys)
            || !isset($this->categorys[$catid])) {
            return FALSE;
        }
		
		$pid = $this->categorys[$catid]['pid'];
		$pids = $pids ? $pid.','.$pids : $pid;
		
		$pid ? $pids = $this->get_pids($pid, $pids, ++$n) : $this->categorys[$catid]['pids'] = $pids;
		
		return $pids;
	}
	
	/**
	 * 所有子id
	 * 
	 * @param	$catid	��ĿID
	 * @return	string
	 */
	private function get_childids($catid) {
	
		$childids = $catid;
		
		if (is_array($this->categorys)) {
			foreach ($this->categorys as $id => $cat) {
				$cat['pid'] && $id != $catid && $cat['pid'] == $catid && $childids.= ','.$this->get_childids($id);
			}
		}
		
		return $childids;
	}
	
	/**
	 * 所有父目录
	 * 
	 * @param	$catid	��ĿID
	 * @return	string
	 */
	public function get_pdirname($catid) {
	
		if ($this->categorys[$catid]['pid']==0) {
            return '';
        }
		
		$t = $this->categorys[$catid];
		$pids = $t['pids'];
		$pids = explode(',', $pids);
		$catdirs = array();
		krsort($pids);
		
		foreach ($pids as $id) {
			if ($id == 0) {
                continue;
            }
			$catdirs[] = $this->categorys[$id]['dirname'];
			if ($this->categorys[$id]['pdirname'] == '') {
                break;
            }
		}
		krsort($catdirs);
		
		return implode('/', $catdirs).'/';
	}
	
	/**
     * 修复数据
	 */
	public function repair() {
	
		$this->categorys = $categorys = array();
		$this->categorys = $categorys = $this->get_data();
		$this->get_categorys($categorys);
		
		if (is_array($this->categorys)) {
			foreach ($this->categorys as $catid => $cat) {
				$pids = $this->get_pids($catid);
				$childids = $this->get_childids($catid);
				$child = is_numeric($childids) ? 0 : 1;
				$pdirname = $this->get_pdirname($catid);
				if ($categorys[$catid]['pdirname'] != $pdirname 
				|| $categorys[$catid]['pids'] != $pids 
				|| $categorys[$catid]['childids'] != $childids 
				|| $categorys[$catid]['child'] != $child) {
					$this->db->where('id', $cat['id'])->update($this->tablename, array(
						'pids' => $pids,
						'child' => $child,
						'childids' => $childids,
						'pdirname' => $pdirname
					));
				}
			}
		}
	}
	
	/**
	 * 缓存
	 *
	 * @param	int		$id
	 * @return	NULL
	 */
	public function cache($siteid = SITE_ID) {

		$this->ci->clear_cache('page-'.$siteid);
		$this->dcache->delete('page-'.$siteid);
		$this->dcache->delete('page-field-'.$siteid);
        $this->tablename = $this->db->dbprefix($siteid.'_page');
		$data = $this->db->order_by('displayorder ASC,id ASC')->get($siteid.'_page')->result_array();
		if ($data) {
            $cache = array();
            foreach ($data as $t) {
                $attachment = dr_string2array($t['attachment']);
                $t['attachment'] = NULL;
                if ($attachment) {
                    foreach ($attachment['file'] as $i => $file) {
                        $t['attachment'][] = array(
                            'file' => $file,
                            'title' => $attachment['title'][$i]
                        );
                    }
                }
                $t['module'] ? $cache['data'][$t['module']][$t['id']] = $t : $cache['data']['index'][$t['id']] = $t;
                $pid = explode(',', $t['pids']);
				$t['topid'] = isset($pid[1]) ? $pid[1] : $t['id'];
                $this->_update_url($t['id'], $siteid);
                $t['setting'] = dr_string2array($t['setting']);
                $cache['dir'][$t['dirname']] = $t['id'];
            }
            $this->dcache->set('page-'.$siteid, $cache);
        }

        // 自定义字段
        $cache = array();
        $field = $this->db->where('disabled', 0)->where('relatedid', $siteid)->where('relatedname', 'page')->order_by('displayorder ASC,id ASC')->get('field')->result_array();
        if ($field) {
            foreach ($field as $f) {
                $f['setting'] = dr_string2array($f['setting']);
                $cache[$f['fieldname']] = $f;
            }
        }
        $this->dcache->set('page-field-'.$siteid, $cache);

	}
}