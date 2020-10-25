<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public $title, $style, $user;

    function __construct() {
        parent::__construct();
        $this->load->model('setting_model');
        $this->title = $this->setting_model->get('title');
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
        $this->user  = $this->session->DMN_USER;
        if (!$this->user) {
            redirect('/');
        }
        if ($this->user['level'] < 7) {
            show_error('您没有权限查看此内容。');
        }
    }

    public function index() {
        $data['dirSize']     = $this->dir_size();
        $data['sqlSize']     = $this->mysql_size();
        $data['title']       = $this->title;
        $data['style']       = $this->style;
        $data['logs']        = $this->logs_file();
        $data['log_content'] = read_file('app/logs/' . $data['logs'][0] . '.php');
        $this->load->view('admin/main', $data);
    }

    public function story_approve() {
        $search = $this->input->get_post('search');
        $this->load->library('Datatables');

        if ($search['value']) {
            $this->db->like('story.title', $search['value'])->or_like('story.author', $search['value']);
        }
        $this->datatables->select("story.id,title,author,users.name as user_name,time", false)
            ->where('approve', 0)
            ->from('story')
            ->join('users', 'story.user_id=users.id', 'left')
            ->add_column('DT_RowId', '$1', 'id')
            ->add_column('action', <<<ETO
<div class="btn-group btn-group-sm">
    <a href="#" class="btn btn-default dropdown-toggle"  data-toggle="dropdown" title="审核小说">
        <i class="icon-check"></i>
    </a>
    <ul class="dropdown-menu" role="menu">
    <li><a href="#" data-approve="1" class="approveStory"><i class="icon-ok"></i> 审核通过</a></li>
    <li><a href="#" data-approve="2" class="unApproveStory"><i class="icon-remove"></i> 审核未通过</a></li>
  </ul>
</div>
ETO
        );
        echo $this->datatables->generate();
    }

    function logs($file) {
        echo read_file('app/logs/' . $file . '.php');
    }

    function deletelog($file) {
        if (unlink('app/logs/' . $file . '.php')) {
            show_json(['success' => '删除文件' . $file . '成功']);
        } else {
            show_json(['error' => '删除文件' . $file . '失败']);
        }
    }

    private function dir_size() {
        $dir       = new RecursiveDirectoryIterator(str_replace('system/', '', BASEPATH));
        $totalSize = 0;
        foreach (new RecursiveIteratorIterator($dir) as $file) {
            $totalSize += $file->getSize();
        }
        $t = round(@disk_total_space(".") / (1024 * 1024 * 1024), 3);
        $f = round(@disk_free_space(".") / (1024 * 1024 * 1024), 3);

        $d['total'] = $t;

        $d['data'] = array(
            array(
                'text' => '空闲',
                'data' => $f
            ),
            array(
                'text' => '已用',
                'data' => $t - $f
            ),
            array(
                'text' => 'DMNovel占用',
                'data' => round($totalSize / (1024 * 1024 * 1024), 3)
            )
        );

        $d['PCT'] = (floatval($t) != 0) ? round(($t - $f) / $t * 100, 2) : 0;

        return $d;
    }

    private function logs_file() {
        $map = get_filenames(APPPATH . 'logs/');
        foreach ($map as $file) {
            if ($file == 'index.html') continue;
            $name  = explode('.', $file);
            $log[] = $name[0];
        }
        rsort($log);
        return $log;
    }

    private function mysql_size() {
        include('app/config/database.php');
        if (isset($db)) {
            $sql = 'use information_schema;';
            $this->db->query($sql);
            $database = $db['default']['database'];
            $sql      = "select concat(round(sum(data_length/1024/1024),2),'MB') as data from tables where table_schema='{$database}';";
            $query    = $this->db->query($sql);
            $sqlSize  = $query->row_array();
            $sql      = 'use ' . $database;
            $this->db->query($sql);
            return $sqlSize['data'];
        }
    }


}
