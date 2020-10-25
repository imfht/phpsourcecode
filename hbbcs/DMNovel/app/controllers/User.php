<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 17-1-6
 * Time: 上午10:23
 */
class User extends CI_Controller {

    public $style, $user;

    function __construct() {
        parent::__construct();
        $this->load->model('users_model', 'users');
        $this->user  = $this->session->DMN_USER;
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
    }

    public function index() {

    }

    public function login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if ($username) {
            if (!$password) {
                show_json(['error' => '密码未填写。']);
                return;
            }
            $user = $this->users->valid($username, $password);
            if (!is_array($user)) {
                echo($user);
            } else {
                $this->session->set_userdata('DMN_USER', $user);
                show_json(['success' => '登录成功', 'user' => $user]);
                session_write_close();
            }
        } else {

            $this->load->view('login');
        }
    }

    function captcha() {
        $this->load->helper('captcha');
        $vals = array(
            'font_path'   => './theme/fonts/FFDIn.otf',
            'img_path'    => './books/captcha/',
            'img_url'     => site_url('books/captcha/'),
            'expiration'  => 600,
            'word_length' => 4,
            'font_size'   => 20
        );

        $cap = create_captcha($vals);

        $data = array(
            'captcha_time' => $cap['time'],
            'ip_address'   => $this->input->ip_address(),
            'word'         => $cap['word']
        );

        $query = $this->db->insert_string('captcha', $data);
        $this->db->query($query);

        echo $cap['image'];
    }

    public function logout() {
        session_destroy();
        redirect('/');
    }

    public function register() {
        $username   = $this->input->post('username');
        $password   = $this->input->post('password');
        $repassword = $this->input->post('repassword');
        $captcha    = $this->input->post('captcha');

        $expiration = time() - 600; // Two hour limit
        $this->db->where('captcha_time < ', $expiration)->delete('captcha');

        $sql   = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
        $binds = array($captcha, $this->input->ip_address(), $expiration);
        $query = $this->db->query($sql, $binds);
        $row   = $query->row();

        if ($row->count == 0) {
            show_json(['error' => '验证码错误。']);
            return;
        }

        if ($password != $repassword) {
            show_json(['error' => '两次输入的密码不相同。']);
            return;
        }

        if ($this->users->get(null, ['name' => $username], 'id')) {
            show_json(['error' => '用户名已存在，请重新输入。']);
            return;
        }

        $user = [
            'id'       => 0,
            'name'     => $username,
            'password' => md5($password),
            'vote'     => '',
            'level'    => 1
        ];

        $this->db->insert('users', $user);
        $user['id'] = $this->db->insert_id();
        $this->session->set_userdata('DMN_USER', $user);
        session_write_close();
        show_json(['success' => '注册成功', 'user' => $user]);
    }

    public function profile($active = '') {
        if (!$this->user) {
            show_error('您还未登录，请登陆后重试');
        }

        $this->load->model('category_model', 'category');

        $data['title']      = $this->user['name'] . '的空间';
        $data['active']     = $active;
        $data['style']      = $this->style;
        $data['user']       = $this->user;
        $data['categories'] = $this->category->get();
        $this->load->view('user/view', $data);
    }

    public function modify() {
        $old_password=$this->input->post('old_password');
        $new_password=$this->input->post('new_password');
        $re_password=$this->input->post('re_password');

        if ($old_password) {
            if (!$new_password || !$re_password) {
                show_json(['error' => '未输入新密码或重复新密码。']);
                return;
            }
            if ($new_password != $re_password) {
                show_json(['error' => '两次输入的密码不相同。']);
                return;
            }

            if (md5($old_password) != $this->user['password']) {
                show_json(['error' => '原密码输入错误。']);
                return;
            }
            $this->db->set('password',md5($new_password))->where('id',$this->user['id'])->update('users');
        } else {
            $mail=$this->input->post('mail');
            $this->db->set('mail',$mail)->where('id',$this->user['id'])->update('users');
        }
        $user=$this->users->get($this->user['id']);
        $this->session->set_userdata('DMN_USER', $user);
        show_json(['success'=>'更新成功。']);
    }

    public function view($active) {
        if (!$this->user) {
            show_error('您还未登录，请登陆后重试');
        }
        $data['user']=$this->user;
        switch ($active) {
            case 'bookmark':
                $this->load->model('bookmark_model', 'bookmark');

                $data['bookmarks'] = $this->bookmark->get(null, ['bookmark.user_id' => $this->user['id']], 'bookmark.*,story.title as story_title,story.image as story_image,story.desc as story_desc', TRUE);

                break;
            case 'avatar':
            case 'modify':
                break;
        }
        $this->load->view('user/'.$active, $data);
    }


    function bookmark_delete($id) {
        if (!$id) {
            show_error('没有选择要删除的书签。');
        }
        $this->db->where('id', $id)->delete('bookmark');
    }

    function avatar() {
        $config['upload_path']   = 'theme/images/avatar';
        $config['allowed_types'] = 'jpg|png|bmp|jpeg';
        $config['max_size']      = 100;
        $config['encrypt_name']  = true;


        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('imageUpload')) {
            show_error($this->upload->display_errors());
        } else {
            $data = array('upload_data' => $this->upload->data());

            $message = array(
                'path'    => $config['upload_path'],
                'profile' => $data['upload_data']
            );
            //删除原头像
            if ($this->user['avatar'] && $this->user['avatar'] != 'default.jpg' && file_exists('theme/images/avatar/'.$this->user['avatar'])) {
                unlink('theme/images/avatar/'.$this->user['avatar']);
            }
            $this->db->set('avatar',$data['upload_data']['file_name'])->where('id',$this->user['id'])->update('users');
            $this->user['avatar']=$data['upload_data']['file_name'];
            $this->session->DMN_USER=$this->user;
            show_json($message);
        }
    }
}