<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Attachment_model extends CI_Model {

    public $siteid;
    public $domain;

	/**
	 * 附件操作模型类
	 */
    public function __construct() {
        parent::__construct();
        $this->siteid = (int)$this->session->userdata('siteid');
        $this->siteid = $this->siteid ? $this->siteid : SITE_ID;
    }
	
    /**
	 * 会员附件
	 *
	 * @param	intval	$uid	uid
	 * @return	array
	 */
    public function limit($uid, $page, $pagesize, $ext, $table) {

    	$sql = ' `'.$this->db->dbprefix('attachment').'` AS `a`,`'.$this->db->dbprefix('attachment_'.(int)substr((string)$uid, -1, 1)).'` AS `b`';
    	$sql.= ' WHERE (`a`.`id`=`b`.`id` AND `a`.`siteid`='.$this->siteid.' AND `a`.`uid`='.$uid.')';
    	if ($ext) {
			$data = explode(',', $ext);
			$where = array();
			foreach ($data as $e) {
				$where[] = '`b`.`fileext`="'.$e.'"';
			}
			$sql.= ' AND ('.implode(' OR ', $where).')';
		}
		$table && $sql.= ' AND `b`.`related` LIKE "'.$this->db->dbprefix($this->siteid.'_'.$table).'-%"';

		$data = $this->db->query('SELECT count(*) as total FROM'.$sql)->row_array();
		$total = (int)$data['total'];

		$sql.= ' ORDER BY `b`.`inputtime` DESC LIMIT '. $pagesize * ($page - 1).','.$pagesize;

		$data = $this->db->query('SELECT * FROM'.$sql)->result_array();
		
		return array($total, $this->_get_format_data($data));
    }
    
    /**
	 * Api附件
	 *
	 * @param	intval	$uid	uid
	 * @param	string	$ext	扩展
	 * @param	intval	$total	总数
	 * @param	intval	$page	当前页
	 * @return	array
	 */
    public function limit_page($uid, $ext, $total, $page) {
    	
    	$sql = 'FROM `'.$this->db->dbprefix('attachment').'` AS `a`,`'.$this->db->dbprefix('attachment_'.(int)substr((string)$uid, -1, 1)).'` AS `b` ';
    	$sql.= 'WHERE (`a`.`id`=`b`.`id` AND `a`.`siteid`='.$this->siteid.' AND `a`.`uid`='.$uid.')';
    	
    	if ($ext) {
			$data = explode('|', $ext);
			$where = array();
			foreach ($data as $e) {
				$where[] = '`b`.`fileext`="'.$e.'"';
			}
			$sql.= ' AND ('.implode(' OR ', $where).')';
		}
    	
    	if (!$total) {
			$data = $this->db->query('SELECT count(*) as total '.$sql)->row_array();
			$total = (int)$data['total'];
			if (!$total) {
                return array(array(), 0);
            }
		}
		
		$sql.= ' ORDER BY `b`.`inputtime` DESC LIMIT '. 7 * ($page - 1).',7';
		
		$data = $this->db->query('SELECT * '.$sql)->result_array();
		
		return array($this->_get_format_data($data), $total);
    }
    
	/**
	 * 将未使用附件更新至附件表
	 *
	 * @param	intval	$uid		uid
	 * @param	string	$related	相关表
	 * @param	array	$attach		附件id集合
	 * @return	void
	 */
	public function replace_attach($uid, $related, $attach) {

		!IS_ADMIN && $this->db->where('uid', $uid);

		$info = $this->db->where_in('id', $attach)->get('attachment_unused')->result_array();
		if (!$info) {
            return NULL;
        }

		$tableid = (int)substr((string)$uid, -1, 1);
		// 判断会员所属情况
		if ($info['uid'] != $uid) {
			$m = dr_member_info($uid);
			$author = $m['username'] ? $m['username'] : '';
		} else {
			$author = $info['author'] ? $info['author'] : '';
		}


		foreach ($info as $t) {
			// 归档附表id
			$id = (int)$t['id'];
			// 更新主索引表
			$this->db->where('id', $id)->update('attachment', array(
				'uid' => $uid,
				'author' => $author,
				'tableid' => $tableid,
				'related' => $related
			));
			// 更新至附表
			$this->db->replace('attachment_'.$tableid, array(
				'id' => $t['id'],
				'uid' => $t['uid'],
				'remote' => $t['remote'],
				'author' => $t['author'],
				'related' => $related,
				'fileext' => $t['fileext'],
				'filesize' => $t['filesize'],
				'filename' => $t['filename'],
				'inputtime' => $t['inputtime'],
				'attachment' => $t['attachment'],
				'attachinfo' => $t['attachinfo'],
			));
			// 删除未使用附件
			$this->db->delete('attachment_unused', 'id='.$id);
		}
		
		return NULL;
	}
	
	/**
	 * 更新时的删除附件
	 *
	 * @param	intval	$uid		uid	用户id
	 * @param	string	$related	当前关联字符串
	 * @param	intval	$id			id	附件id
	 * @return	NULL
	 */
	public function delete_for_handle($uid, $related, $id) {
	
		if (!$id || !$uid) {
            return NULL;
        }
		
		// 查询附件
		$data = $this->db->where('id', $id)->get('attachment')->row_array();
		
		// 判断附件归属权限
		if ($related != $data['related']) {
            return NULL;
        }

		// 删除附件数据
		$this->db->delete('attachment', 'id='.(int)$id);
		
		// 查询附件附表
		$tableid = (int)$data['tableid'];
		$info = $this->db->select('attachment,remote')->where('id', (int)$id)->get('attachment_'.$tableid)->row_array();
		if (!$info) {
            return NULL;
        }
		
		// 删除附件文件
		$info['id'] = $id;
		$info['tableid'] = $tableid;
		$this->_delete_attachment($info);
		
		return TRUE;
	}
	
	/**
	 * 删除附件
	 *
	 * @param	intval	$uid		uid	用户id
	 * @param	string	$related	当前关联字符串
	 * @param	intval	$id			id	附件id
	 * @return	NULL
	 */
	public function delete($uid, $related, $id) {
	
		if (!$id || !$uid) {
            return NULL;
        }

		// 查询附件
		$data = $this->db->select('tableid,related')->where('id', $id)->get('attachment')->row_array();
        if (!$data) {
            return NULL;
        }

		// 删除附件数据
		$this->db->delete('attachment', 'id='.(int)$id);
		
		// 查询附件附表
		$tableid = (int)$data['tableid'];
		$info = $this->db->select('attachment,remote')->where('id', (int)$id)->get('attachment_'.$tableid)->row_array();
		if (!$info) {
            return NULL;
        }
		
		// 删除附件文件
		$info['id'] = $id;
		$info['tableid'] = $tableid;
		$this->_delete_attachment($info);
		
		return TRUE;
	}
	

	
	/**
	 * 按表删除附件
	 *
	 * @param	string	$related	相关表标识
	 * @param	intval	$is_all		是否全部表附件
	 * @return	NULL
	 */
	public function delete_for_table($related, $is_all = FALSE) {
		
		if (!$related) {
            return NULL;
        }
		
		$data = $is_all 
			? $this->db->query('select id,tableid from `'.$this->db->dbprefix('attachment').'` where `related` like "%'.$related.'%"')->result_array() 
			: $this->db->select('id,tableid')->where('related', $related)->get('attachment')->result_array();
		
		if (!$data) {
            return NULL;
        }
		
		// 删除附件
		foreach ($data as $t) {
		
            if (!isset($t['id'])) {
                continue;
            }
			
			$this->db->delete('attachment', 'id='.$t['id']);
			
			$info = $this->db->select('attachment,remote')->where('id', $t['id'])->get('attachment_'.(int)$t['tableid'])->row_array();
			if (!$info) {
                return NULL;
            }
			
			$info['id'] = $t['id'];
			$info['tableid'] = $t['tableid'];
			$this->_delete_attachment($info);
		}
		
		return 1;
	}
	
	/**
	 * 按站点删除附件
	 *
	 * @param	intval	$siteid	站点id
	 * @return	NULL
	 */
	public function delete_for_site($siteid) {
		
		if (!$siteid) {
            return NULL;
        }
		
		$data = $this->db->select('id,tableid')->where('siteid', $siteid)->get('attachment')->result_array();
		if (!$data) {
            return NULL;
        }
		
		// 删除附件
		foreach ($data as $t) {
		
			$this->db->delete('attachment', 'id='.$t['id']);
			$info = $this->db->select('attachment,remote')->where('id', $t['id'])->get('attachment_'.(int)$t['tableid'])->row_array();
			if (!$info) {
                continue;
            }
			
			$info['id'] = $t['id'];
			$info['tableid'] = $t['tableid'];
			$this->_delete_attachment($info);
		}
		
		// 删除未使用
		$data = $this->db->where('siteid', $siteid)->get('attachment_unused')->result_array();
		if (!$data) {
            return NULL;
        }
		
		// 删除附件
		foreach ($data as $t) {
			$this->db->delete('attachment_unused', 'id='.$t['id']);
			$this->_delete_attachment($t);
		}
	}
	
	/**
	 * 按会员删除附件
	 *
	 * @param	intval	$siteid	站点id
	 * @return	NULL
	 */
	public function delete_for_uid($uid) {
		
		if (!$uid) {
            return NULL;
        }
		
		$data = $this->db->select('id,tableid')->where('uid', $uid)->get('attachment')->result_array();
		if (!$data) {
            return NULL;
        }
		
		// 删除附件
		foreach ($data as $t) {
			
			$this->db->delete('attachment', 'id='.$t['id']);
			
			$info = $this->db->select('attachment,remote')->where('id', $t['id'])->get('attachment_'.$t['tableid'])->row_array();
			if (!$info) {
                continue;
            }
			
			$info['id'] = $t['id'];
			$info['tableid'] = $t['tableid'];
			$this->_delete_attachment($info);
		}
		
		// 删除未使用
		$data = $this->db->where('uid', $uid)->get('attachment_unused')->result_array();
		if (!$data) {
            return NULL;
        }
		
		// 删除附件
		foreach ($data as $t) {
			$this->db->delete('attachment_unused', 'id='.$t['id']);
			$this->_delete_attachment($t);
		}
	}
	
	/**
	 * 查询未使用附件
	 *
	 * @param	intval	$uid	uid	用户id
	 * @param	string	$ext	扩展名
	 * @return	NULL
	 */
	public function get_unused($uid, $ext, $limit = 20) {

		if (defined('SYS_ATTACHMENT_DB') && (int)SYS_ATTACHMENT_DB) {
			$this->db->where('uid', $uid);
			$this->db->where('siteid', $this->siteid);
			$this->db->where_in('fileext', explode(',', $ext));
			$limit && $this->db->limit($limit);
			$this->db->order_by('inputtime DESC');
			$data = $this->db->get('attachment_unused')->result_array();

			return $this->_get_format_data($data);
		}

		return array();
	}
	
	/**
	 * 下载远程文件
	 *
	 * @param	intval	$uid	uid	用户id
	 * @param	string	$url	文件url
	 * @return	array
	 */
	public function catcher($uid, $url) {
	
		if (!$uid || !$url) {
            return NULL;
        }

        if (!$this->domain) {
            // 站点信息
            $siteinfo = $this->ci->get_cache('siteinfo', $this->siteid);
            // 域名验证
            $this->domain = require WEBPATH.'config/domain.php';
            foreach ($siteinfo['remote'] as $t) {
                $this->domain[$t['SITE_ATTACH_URL']] = TRUE;
            }
            $this->domain['baidu.com'] = TRUE;
            $this->domain['google.com'] = TRUE;
        }
		
		foreach ($this->domain as $uri => $t) {
			if (stripos($url, $uri) !== FALSE) {
				return NULL;
			}
		}

		$path = SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
		!is_dir($path) && dr_mkdirs($path);
		
		$filename = substr(md5(time()), 0, 7).rand(100, 999);
		$data = dr_catcher_data($url);
		if (!$data) {
            return NULL;
        }
		
		$fileext = strtolower(trim(substr(strrchr($url, '.'), 1, 10))); //扩展名
		if (file_put_contents($path.$filename.'.'.$fileext, $data)) {
			$info = array(
				'file_ext' => '.'.$fileext,
				'full_path' => $path.$filename.'.'.$fileext,
				'file_size' => filesize($path.$filename.'.'.$fileext)/1024,
				'client_name' => $url,
			);
			return $this->upload($uid, $info, NULL);
		}
		
		return NULL;
	}

    // 队列下载文件
    public function cron_catcher($uid, $url, $field) {

        if (!$this->domain) {
            // 站点信息
            $siteinfo = $this->ci->get_cache('siteinfo', $this->siteid);
            // 域名验证
            $this->domain = require WEBPATH.'config/domain.php';
            if ($siteinfo['remote']) {
                foreach ($siteinfo['remote'] as $t) {
                    $this->domain[$t['SITE_ATTACH_URL']] = TRUE;
                }
            }
        }
		
		$this->domain['baidu.com'] = TRUE;
		$this->domain['google.com'] = TRUE;
		foreach ($this->domain as $uri => $t) {
			if (stripos($url, $uri) !== FALSE) {
				return NULL;
			}
		}

        // 入库附件
        $this->db->replace('attachment', array(
            'uid' => (int)$uid,
            'author' => '',
            'siteid' => $this->siteid,
            'tableid' => (int)substr((string)$uid, -1, 1),
            'related' => '',
            'fileext' => '',
            'filemd5' => 0,
            'download' => 0,
            'filesize' => 0,
        ));
        $id = $this->db->insert_id();

        // 入库失败
        if (!$id) {
            return;
        }

        // 增加至未使用附件表
        $this->db->replace('attachment_unused', array(
            'id' => $id,
            'uid' => $uid,
            'author' => '',
            'siteid' => $this->siteid,
            'remote' => 0,
            'fileext' => '',
            'filename' => '',
            'filesize' => 0,
            'inputtime' => SYS_TIME,
            'attachment' => $url,
            'attachinfo' => '', // 留空保留
        ));

        // 入库对列表
        $this->db->insert('cron_queue', array(
            'type' => 6,
            'value' => dr_array2string(array('id'=>$id, 'uid'=>$uid, 'url'=>$url, 'field' => $field)),
            'error' => '',
            'status' => 0,
            'inputtime' => SYS_TIME,
            'updatetime' => 0,
        ));

        return $id;
    }

    // 远程下载文件入库
    public function add_catcher($uid, $path) {

        $path = trim($path, '/');
        // 入库附件
        $this->db->replace('attachment', array(
            'uid' => (int)$uid,
            'author' => '',
            'siteid' => $this->siteid,
            'tableid' => (int)substr((string)$uid, -1, 1),
            'related' => '',
            'fileext' => '',
            'filemd5' => 0,
            'download' => 0,
            'filesize' => 0,
        ));
        $id = $this->db->insert_id();

        // 入库失败
        if (!$id) {
            return;
        }

        // 增加至未使用附件表
        $this->db->replace('attachment_unused', array(
            'id' => $id,
            'uid' => $uid,
            'author' => '',
            'siteid' => $this->siteid,
            'remote' => 0,
            'fileext' => '',
            'filename' => '',
            'filesize' => 0,
            'inputtime' => SYS_TIME,
            'attachment' => $path,
            'attachinfo' => '', // 留空保留
        ));

        return $id;
    }

	/**
	 * 上传
	 *
	 * @param	intval	$uid	uid	用户id
	 * @param	array	$info	ci 文件上传成功返回数据
     * @param	intval	$id	id	指定附件id
	 * @return	array
	 */
	public function upload($uid, $info, $id = 0) {

		$_ext = strtolower(substr($info['file_ext'], 1));
		$author = $this->_get_member_name($uid);
        $replace = 0;
        $content = @file_get_contents($info['full_path']);

        // 附件信息
        $attachinfo = array();
        list($attachinfo['width'], $attachinfo['height']) = @getimagesize($info['full_path']);

        // 查询指定附件
        if ($id) {
            $row = $this->db->where('id', $id)->get('attachment')->row_array();
            if ($row) {
                $replace = 1;
                $this->siteid = intval($row['siteid']);
            } else {
                return '当前附件不存在';
            }
        }

        // 入库附件
        if (!$id) {
            $this->db->replace('attachment', array(
                'uid' => (int)$uid,
                'author' => $author,
                'siteid' => $this->siteid,
                'tableid' => (int)substr((string)$uid, -1, 1),
                'related' => '',
                'fileext' => $_ext,
                'filemd5' => $content ? md5($content) : 0,
                'download' => 0,
                'filesize' => $info['file_size'] * 1024,
            ));
            $id = $this->db->insert_id();
            // 入库失败，返回错误且删除附件
            if (!$id) {
                @unlink($info['full_path']);
                return fc_lang('文件入库失败，请重试');
            }
        }

		// 生成缩略图
		$thumb = array();
		if ($attachinfo['width']) {
			require_once FCPATH.'dayrui/libraries/Wp_image.php';
			// 获取系统配置尺寸
			$sizes = $this->ci->get_cache('siteinfo', SITE_ID, 'image');
			$editor = new WP_Image_Editor_GD($info['full_path']);
			$loaded = $editor->load();
			$loaded && $sizes && $thumb = $editor->multi_resize( $sizes );
		}

		// 存储处理
		$remote = 0;
		$attachment = trim(substr($info['full_path'], strlen(SYS_UPLOAD_PATH)), '/'); // 附件储存地址
        $file = (SYS_UPLOAD_DIR ? SYS_UPLOAD_DIR.'/' : '').$attachment; // 附件网站上的路径
        // 远程附件信息
        $remote_cfg = $this->ci->get_cache('attachment');
        if (isset($remote_cfg[$this->siteid]['ext'][$_ext])
            && $rid = $remote_cfg[$this->siteid]['ext'][$_ext]) {
            // 根据模式来存储
            $config = $remote_cfg[$this->siteid]['data'][$rid];
            list($remote, $file, $attachment) = $this->upload2($config, $info['full_path'], array(), $thumb);
        }

        // 非远程附件补全本地地址
        $file = !$remote ? SYS_ATTACHMENT_URL.$attachment : $file;

		$pos = strrpos($info['client_name'], '.');
		$filename = strpos($info['client_name'], 'http://') === 0 ? trim(strrchr($info['client_name'], '/'), '/') : $info['client_name'];
		$filename = $pos ? substr($filename, 0, $pos) : $filename;

        if ($replace) {
            // 替换主表
            $this->db->where('id', $id)->update('attachment', array(
                'author' => $author,
                'fileext' => $_ext,
                'filemd5' => $content ? md5($content) : 0,
                'filesize' => $info['file_size'] * 1024,
            ));
            // 更新替换已使用的附件表
            $this->db->where('id', $id)->update('attachment_'.$row['tableid'], array(
                'uid' => $uid,
                'author' => $author,
                'remote' => $remote,
                'fileext' => $_ext,
                'filename' => $filename,
                'filesize' => $info['file_size'] * 1024,
                'attachment' => $attachment,
            ));
            // 更新替换未使用的附件表
            $this->db->where('id', $id)->update('attachment_unused', array(
                'uid' => $uid,
                'author' => $author,
                'remote' => $remote,
                'fileext' => $_ext,
                'filename' => $filename,
                'filesize' => $info['file_size'] * 1024,
                'attachment' => $attachment,
            ));
            $this->ci->clear_cache('attachment-'.$id);
        } else {
            // 增加至未使用附件表
            $this->db->replace('attachment_unused', array(
                'id' => $id,
                'uid' => $uid,
                'author' => $author,
                'siteid' => $this->siteid,
                'remote' => $remote,
                'fileext' => $_ext,
                'filename' => $filename,
                'filesize' => $info['file_size'] * 1024,
                'inputtime' => SYS_TIME,
                'attachment' => $attachment,
                'attachinfo' => dr_array2string($attachinfo),
            ));
        }

		return $replace ? $row : array($id, $file, $_ext);
	}
	
	// 会员名称
	private function _get_member_name($uid) {
		$data = $this->db->where('uid', $uid)->select('username')->get('member')->row_array();
		return isset($data['username']) ? $data['username'] : '';
	}
	
	// 格式化输出数据
	private function _get_format_data($data) {
		
		if (!$data) {
            return NULL;
        }
		
		foreach ($data as $i => $t) {
			$data[$i]['ext'] = $t['fileext'];
			$data[$i]['attachment'] = $t['remote'] ? $this->ci->get_cache('attachment', $this->siteid, 'data', $t['remote'], 'url').'/'.$t['attachment'] : dr_file(dr_ck_attach($t['attachment']));
			if (in_array($t['fileext'], array('jpg', 'gif', 'png'))) {
				$data[$i]['show'] = $data[$i]['attachment'];
				$data[$i]['icon'] = THEME_PATH.'admin/images/ext/jpg.gif';
			} else {
				$data[$i]['show'] = is_file(WEBPATH.'statics/admin/images/ext/'.$t['fileext'].'.png') ? THEME_PATH.'admin/images/ext/'.$t['fileext'].'.png' : THEME_PATH.'admin/images/ext/blank.png';
				$data[$i]['icon'] = is_file(WEBPATH.'statics/admin/images/ext/'.$t['fileext'].'.gif') ? THEME_PATH.'admin/images/ext/'.$t['fileext'].'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
			}
			$data[$i]['size'] = dr_format_file_size($t['filesize']);
		}
		
		return $data;
	}

    // 远程附件上传
    public function upload2($config, $file, $info = array(), $thumb = array()) {
        set_time_limit(0);
		if ($thumb) {
			$locals = $thumb;
			$locals[] = array('file' => $file);
		} else {
			$locals = array(
				array('file' => $file),
			);
		}
        $remote = 0;
        if ($config['type'] == 1) {
            // ftp附件模式
            $this->load->library('ftp');
            if ($this->ftp->connect(array(
                'port' => $config['value']['port'],
                'debug' => FALSE,
                'passive' => $config['value']['pasv'],
                'hostname' => $config['value']['host'],
                'username' => $config['value']['username'],
                'password' => $config['value']['password'],
            ))) {
                // 连接ftp成功
                $dir = basename(dirname($file)).'/';
                $path = $config['value']['path'].'/'.$dir;
				$file = basename($file);
                $attachment = $dir.$file;
                $this->ftp->mkdir($path);

				foreach ($locals as $local2) {
					$local = $local2['file'];
					$_file = basename($local);
					if ($this->ftp->upload($local, $path.$_file, $config['value']['mode'], 0775)) {
						$file = $config['url'].'/'.$dir.$_file;
						$remote = $config['id'];
						unlink($local);
					}
				}
                $this->ftp->close();
            } else {
                log_message('error', '远程附件ftp模式：ftp连接失败');
            }
        } elseif ($config['type'] == 2) {
            // 百度云存储模式
            $attachment = $file = basename(dirname($file)).'/'.basename($file);
            require_once FCPATH . 'dayrui/libraries/Remote/BaiduBCS/bcs.class.php';
            $bcs = new BaiduBCS($config['value']['ak'], $config['value']['sk'], $config['value']['host']);
            $opt = array();
            $opt['acl'] = BaiduBCS::BCS_SDK_ACL_TYPE_PUBLIC_WRITE;
            $opt['curlopts'] = array(CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 1800);

			foreach ($locals as $local2) {
				$local = $local2['file'];
				$_file = basename(dirname($local)).'/'.basename($local);
				$response = $bcs->create_object($config['value']['bucket'], '/' . $_file, $local, $opt);
				if ($response->status == 200) {
					$file = $config['url'] . '/' . $_file;
					$remote = $config['id'];
					unlink($local);
				} else {
					log_message('error', '远程附件百度云存储失败');
				}
			}
        } elseif ($config['type'] == 3) {
            // 阿里云存储模式
            $attachment = $file = basename(dirname($file)).'/'.basename($file);
            require_once FCPATH . 'dayrui/libraries/Remote/AliyunOSS/sdk.class.php';
            $oss = new ALIOSS($config['value']['id'], $config['value']['secret'], $config['value']['host']);
            $oss->set_debug_mode(FALSE);

			foreach ($locals as $local2) {
				$local = $local2['file'];
				$_file = basename(dirname($file)).'/'.basename($file);
				$response = $oss->upload_file_by_file($config['value']['bucket'], $_file, $local);
				if ($response->status == 200) {
					$file = $config['url'] . '/' . $_file;
					$remote = $config['id'];
					unlink($local);
				} else {
					log_message('error', '远程附件阿里云存储模式：' . $response->body);
				}
			}
        } elseif ($config['type'] == 4) {
            // 腾讯云存储模式
            $attachment = $file = basename(dirname($file)).'/'.basename($file);
			require_once FCPATH . 'dayrui/libraries/Remote/QcloudCOS/Conf.php';
			Conf::init($config['value']['qcloud_app'], $config['value']['qcloud_id'], $config['value']['qcloud_key']);
			foreach ($locals as $local2) {
				$local = $local2['file'];
				$_file = basename(dirname($file)).'/'.basename($file);
				$result = Cosapi::upload($local, $config['value']['qcloud_bucket'], '/' . $_file, "biz_attr");
				if ($result['code'] == 0) {
					$file = trim($config['url'], '/') . '/' . $_file;
					$remote = $config['id'];
					unlink($local);
				} else {
					log_message('error', '远程附件腾讯云存储模式：' . $result['message']);
				}
			}
        } else {
            log_message('error', '远程附件类别（#'.(int)$config['type'].'）未定义');
        }

        // 修改图片时的参数
        if ($info && isset($info['id']) && $info['id'] && $attachment) {
            $this->db->where('id', (int)$info['id'])->update('attachment_'.(int)$info['tableid'], array(
                'remote' => $remote,
                'attachment' => $attachment,
            ));
            $this->db->where('id', (int)$info['id'])->update('attachment_unused', array(
                'remote' => $remote,
                'attachment' => $attachment,
            ));
            // 清空附件缓存
            $this->ci->clear_cache('attachment-'.$info['id']);
        }

        return array($remote, $file, $attachment);
    }

	// 删除文件
	public function _delete_attachment($info) {

		if ($info['remote'] && isset($info['attachment']) && $info['attachment']) {
			// 删除远程文件
			$config = $this->ci->get_cache('attachment', $this->siteid, 'data', $info['remote']);
			// 根据模式来存储
			if ($config && $config['type'] == 1) {
				// ftp附件模式
				set_time_limit(0);
				$this->load->library('ftp');
				if ($this->ftp->connect(array(
					'port' => $config['value']['port'],
					'debug' => FALSE,
					'passive' => $config['value']['pasv'],
					'hostname' => $config['value']['host'],
					'username' => $config['value']['username'],
					'password' => $config['value']['password'],
				))) {
					// 连接ftp成功
					$this->ftp->delete_file($config['value']['path'].'/'.$info['attachment']);
					$this->ftp->close();
				} else {
					log_message('error', '远程附件ftp模式：ftp连接失败');
				}
			} elseif ($config && $config['type'] == 2) {
				// 百度云存储模式
				require_once FCPATH . 'dayrui/libraries/Remote/BaiduBCS/bcs.class.php';
				$bcs = new BaiduBCS($config['value']['ak'], $config['value']['sk'], $config['value']['host']);
				$bcs->delete_object($config['value']['bucket'], '/'.$info['attachment']);
			} elseif ($config && $config['type'] == 3) {
				// 阿里云存储模式
				require_once FCPATH . 'dayrui/libraries/Remote/AliyunOSS/sdk.class.php';
				$oss = new ALIOSS($config['value']['id'], $config['value']['secret'], $config['value']['host']);
				$oss->set_debug_mode(FALSE);
				$response = $oss->delete_object($config['value']['bucket'], $info['attachment']);
				if ($response->status != 200 || $response->status != 204) {
					log_message('error', '('.$info['attachment'].')阿里云存储删除失败：'.$response->body);
				}
			} elseif ($config && $config['type'] == 4) {
				// 腾讯云存储模式
				require_once FCPATH . 'dayrui/libraries/Remote/QcloudCOS/Conf.php';
				Conf::init($config['value']['qcloud_app'], $config['value']['qcloud_id'], $config['value']['qcloud_key']);
				$result = Cosapi::del($config['value']['qcloud_bucket'], '/'.$info['attachment']);
				if ($result['code'] != 0) {
					log_message('error', '('.$info['attachment'].')腾讯云存储删除失败：' . $result['message']);
				}
			}
		} else {
			// 删除本地文件
			$file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
			//$file = str_replace('member/uploadfile/member/uploadfile', 'member/uploadfile', $file);
			@unlink($file);
		}

		isset($info['tableid']) && $this->db->delete('attachment_'.(int)$info['tableid'], 'id='.(int)$info['id']);

		// 清空附件缓存
		$this->ci->clear_cache('attachment-'.$info['id']);
	}
}