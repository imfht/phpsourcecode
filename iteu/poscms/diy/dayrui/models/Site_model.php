<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Site_model extends CI_Model {

	public $config;

    public function __construct() {
        parent::__construct();
		$this->config = array(
			'SITE_NAME'					=> '网站的名称',
			'SITE_DOMAIN'				=> '网站的域名',
			'SITE_DOMAINS'				=> '网站的其他域名',
			'SITE_MOBILE'				=> '移动端域名',
            'SITE_CLOSE'				=> '网站是否是关闭状态',
            'SITE_CLOSE_MSG'			=> '网站关闭时的显示信息',
			'SITE_LANGUAGE'				=> '网站的语言',
			'SITE_THEME'				=> '网站的主题风格',
			'SITE_TEMPLATE'				=> '网站的模板目录',
			'SITE_TIMEZONE'				=> '所在的时区常量',
			'SITE_TIME_FORMAT'			=> '时间显示格式，与date函数一致，默认Y-m-d H:i:s',
			'SITE_TITLE'				=> '网站首页SEO标题',
			'SITE_SEOJOIN'				=> '网站SEO间隔符号',
			'SITE_KEYWORDS'				=> '网站SEO关键字',
			'SITE_DESCRIPTION'			=> '网站SEO描述信息',
			'SITE_NAVIGATOR'			=> '网站导航信息，多个导航逗号分开',
            'SITE_MOBILE_OPEN'		    => '是否自动识别移动端并强制定向到移动端域名',
            'SITE_IMAGE_CONTENT'		=> '是否内容编辑器显示水印图片',
			'SITE_IMAGE_RATIO'			=> '是否宽度自动适应',
			'SITE_IMAGE_HTML'			=> '图片静态化',
            'SITE_URL_301'			    => '控制URL唯一301跳转的开关',

		);
    }
	
	/**
	 * 创建站点
	 *
	 * @return	id
	 */
	public function add_site($data) {


        exit('体验版不支持');
	}

	/**
	 * 修改站点
	 *
	 * @return	void
	 */
	public function edit_site($id, $data) {
	
		if (!$data || !$id) {
            return NULL;
        }

		$this->db->where('id', $id)->update('site', array(
			'name' => $data['name'],
			'domain' => $data['domain'],
			'setting' => dr_array2string($data['setting'])
		));
	}
	
	/**
	 * 站点
	 *
	 * @return	array|NULL
	 */
	public function get_site_data() {
	
		$_data = $this->db->order_by('id ASC')->get('site')->result_array();
		if (!$_data) {
            return NULL;
        }

		$data = array();
		foreach ($_data as $t) {
			$t['setting'] = dr_string2array($t['setting']);
			$t['setting']['SITE_NAME'] = $t['name'];
			$t['setting']['SITE_DOMAIN'] = $t['domain'];
			$data[$t['id']]	= $t;
		}

		return $data;
	}

	/**
	 * 站点信息
	 *
	 * @return	array|NULL
	 */
	public function get_site_info($id) {

		$data = $this->db->where('id', $id)->get('site')->row_array();
		if (!$data) {
            return NULL;
        }

        $data['setting'] = dr_string2array($data['setting']);
        $data['setting']['SITE_NAME'] = $data['name'];
        $data['setting']['SITE_DOMAIN'] = $data['domain'];

		return $data['setting'];
	}

    // 站点缓存
    public function cache() {

        $data = $this->get_site_data();
        $oldfile = directory_map(WEBPATH.'config/site/');
        foreach ($oldfile as $file) {
            @unlink(WEBPATH.'config/site/'.$file);
        }

        $this->load->library('dconfig');
        $this->ci->dcache->delete('siteinfo');
        $cache = $domain = $module_domain = array();

        // 站点域名归类和写入配置文件
        foreach ($data as $id => $t) {
            // 站点域名归类
            $t['domain'] && $domain[$t['domain']] = $id;
            // 站点的其他域名
            if ($t['setting']['SITE_DOMAINS']) {
                $arr = @explode(',', $t['setting']['SITE_DOMAINS']);
                if ($arr) {
                    foreach ($arr as $a) {
                        $a && $domain[$a] = $id;
                    }
                }
            }
            // 移动端域名归类
            $t['setting']['SITE_MOBILE'] && $domain[$t['setting']['SITE_MOBILE']] = $id;
            // 写入配置文件
            $this->dconfig->file(WEBPATH.'config/site/'.$id.'.php')->note('站点配置文件')->space(32)->to_require_one($this->config, $t['setting'], 1);
            // 写入缓存文件
            $cache[$id] = $t['setting'];
            $this->ci->dcache->delete('tag-'.$id);
        }
        $this->ci->dcache->set('siteinfo', $cache);

        // tag缓存
        $tag = array();
        // 查询所有可用模块
        $data = $this->db->where('disabled', 0)->select('site,dirname')->order_by('displayorder ASC')->get('module')->result_array();
        if ($data) {
            $module = array();
            $tableinfo = $this->ci->get_cache('table');
            if (!$tableinfo) {
                $this->ci->load->model('system_model');
                $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
            }
            foreach ($data as $t) {
                // 排除不存在的模块
                if (!is_dir(FCPATH.'module/'.$t['dirname'])) {
                    continue;
                }
                // 排除自定义数据的模块
                $cfg = require FCPATH.'module/'.$t['dirname'].'/config/module.php';
                if (isset($cfg['nodb']) && $cfg['nodb']) {
                    continue;
                }
                // 模块域名归类
                $site = dr_string2array($t['site']);
                foreach ($site as $sid => $s) {
                    if ($s['use']) {
                        if ($s['domain']) {
                            $domain[$s['domain']] = $sid; // 更新模块域名
                            $module_domain[$s['domain']] = $t['dirname']; // 模块域名归类
                        }
                        $module[$sid][] = $t['dirname']; // 将模块归类至站点
                        // tag
                        $table = $this->db->dbprefix($sid . '_' . $t['dirname'] . '_tag');
                        if ($tableinfo[$table] && $this->site[$sid]) {
                            $tags = $this->site[$sid]->get($table)->result_array();
                            if ($tags) {
                                foreach ($tags as $tt) {
                                    $tag[$sid][] = $tt['name'];
                                }
                            }
                        }
                    }
                }

            }
            $this->ci->dcache->set('module', $module);
        } else {
            $this->ci->dcache->delete('module');
        }

        // tag缓存存储
        if ($tag) {
            foreach ($tag as $sid => $t) {
                $this->ci->dcache->set('tag-'.$sid, array_unique($t));
            }
        }


        // 会员域名归类
        $data = $this->db->where('name', 'member')->limit(1)->get('member_setting')->row_array();
        if ($data) {
            $data = dr_string2array($data['value']);
            if ($data['domain']) {
                foreach ($data['domain'] as $sid => $url) {
                    if ($url) {
                        $domain[$url] = $sid;
                        $module_domain[$url] = 'member'; // 会员域名归类
                    }
                }
            }
        }

        // 空间域名归类
        $data = $this->db->where('name', 'space')->limit(1)->get('member_setting')->row_array();
        if ($data) {
            $data = dr_string2array($data['value']);
            $data['domain'] && $module_domain[$data['domain']] = 'space'; // 空间域名归类
            $data['spacedomain'] && $module_domain['space'] = $data['spacedomain']; // 空间泛域名归类
        }

		// 判断是否有分支系统域名整合
        function_exists('dr_save_domain') && $domain = dr_save_domain($domain, $cache);

        // 生成站点域名归属
        $this->dconfig->file(WEBPATH.'config/domain.php')->note('站点域名文件')->space(32)->to_require_one($domain);
        $this->dconfig->file(WEBPATH.'config/module_domain.php')->note('模块域名归类文件')->space(32)->to_require_one($module_domain);


    }
}