<?php

/**
 * 项目公用controller
 * Created by PhpStorm.
 * User: li
 * Date: 15-10-9
 * Time: 下午4:18
 */


/**
 * Class MY_Controller
 */
class MY_Controller extends CI_Controller
{
    /**
     * 模块
     *
     * @var string
     */
    protected $module = '';

    /**
     * 所在模块controllers下的目录名称
     *
     * @var string
     */
    protected $directory = '';

    /**
     * 控制器名称
     *
     * @var string
     */
    protected $controller;

    /**
     * 控制器方法名称
     *
     * @var string
     */
    protected $action;

    /**
     * 用户信息
     *
     * @var array
     */
    protected $_login_user=array();

    protected $_page_size = 10;

    public function __construct()
    {
        parent::__construct();
        $this->module = $this->router->module;
        $this->controller = $this->router->class;
        $this->action = $this->router->method;
        if (empty($this->module)) {
            $this->directory = trim($this->router->directory, '/');
        } else {
            $str = substr(
                $this->router->directory,
                strpos($this->router->directory, 'controllers')
            );
            $this->directory = substr($str, 12);
            $this->directory = trim($this->directory, '/');
            $this->directory = trim($this->directory, '\\');
        }
    //    $this->load->helper('url');
    //    $this->load->library('Auth_nuan');
    //    $this->_login_user = @$_SESSION['login_user'];
    }

    /**
     *
     */
    protected function auth(){

    }

    /**
     * 返回json数据
     *
     * @param mix  $status 状态、状态号
     * @param mix  $data   数据
     * @param mix  $msg    提示信息
     *
     * @return void
     */
    public function json_response($status = true, $data = array(), $msg = '')
    {
        echo json_encode(array(
            'status' => $status,
            'data' => $data,
            'msg' => $msg,
        ));

        exit;

    }

    /**
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    protected function set_cookie($name,$value)
    {
        $expire = 60*60*24*365;
        set_cookie(array(
            'name'              => $name,
            'value'             => $value,
            'expire'    => $expire
        ));
    }
    /**
     * 获取完整的当前url
     *
     * @return string
     */
    public function get_self_url()
    {
        return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
    /**
     * 上传图片
     * @param $file_name
     * @param $settings
     * @param $type_id
     * @return bool
     */
    protected function upload_image($file_name, $settings , $type_id)
    {
        $this->load->helper('string');
        //$this->load->config('upload_images');

        $file_path = $settings['file_path'].'/'. $type_id;

        $config = array();
        $config['upload_path'] = FCPATH . 'data/'.$file_path;
        $config['allowed_types'] = $settings['allowed_types'];

        $config['max_size'] = $settings['max_size'];
        $config['file_name'] = random_string('md5').random_string() . '.jpg';


        $this->load->library('upload', $config);
        if (!is_dir($config['upload_path'])) {
            _mkdir($config['upload_path']);
        }
       //  print_r($_FILES);exit;

        if ( !$this->upload->do_upload($file_name))
        {
            return false;
        } else {

            $upload_data = $this->upload->data();
            $upload_data['save_path'] = $file_path . '/' . $upload_data['file_name'];


            $thumbs = @$settings['thumbs'];
            if ($thumbs) {
                //print_r($thumbs);
                $this->load->library('Image_lib');
                foreach ($thumbs as $key => $thumb) {
                    $thumb_config = array();
                    $thumb_config['image_library'] = 'gd2';
                    $thumb_config['source_image'] = $upload_data['full_path'];
                    $thumb_config['create_thumb'] = false;

                    if (isset($thumb[2]) && $thumb[2] == 1) {
                        $thumb_config['maintain_ratio'] = false;
                    } else {
                        $thumb_config['maintain_ratio'] = TRUE;
                    }


                    $thumb_config['width'] = $thumb[0];
                    $thumb_config['height'] = $thumb[1];
                    $thumb_config['new_image'] = $upload_data['file_path'].$upload_data['file_name'].'_'.$key.'.jpg';


                    if (isset($thumb[3]) && $thumb[3] == 1){
                        if(isset($settings['wm_text'])){
                            $thumb_config['wm_text'] = $settings['wm_text'];
                        }
                        if(isset($settings['wm_type'])){
                            $thumb_config['wm_type'] = $settings['wm_type'];
                        }
                        if(isset($settings['wm_overlay_path'])){
                            $thumb_config['wm_overlay_path'] = $settings['wm_overlay_path'];
                        }
                        if(isset($settings['wm_font_path'])){
                            $thumb_config['wm_font_path'] = $settings['wm_font_path'];
                        }
                        if(isset($settings['wm_overlay_path'])){
                            $thumb_config['wm_overlay_path'] = FCPATH .'static/img/'.$settings['wm_overlay_path'];
                        }
                        if(isset($settings['wm_vrt_alignment'])){
                            $thumb_config['wm_vrt_alignment'] = $settings['wm_vrt_alignment'];
                        }
                        if(isset($settings['wm_hor_alignment'])){
                            $thumb_config['wm_hor_alignment'] = $settings['wm_hor_alignment'];
                        }
                        if(isset($settings['wm_hor_offset'])){
                            $thumb_config['wm_hor_offset'] = $settings['wm_hor_offset'];
                        }
                        if(isset($settings['wm_vrt_offset'])){
                            $thumb_config['wm_vrt_offset'] = $settings['wm_vrt_offset'];
                        }
                    }



                    //计算等比压缩
                    $image_size = getimagesize($upload_data['full_path']);
                    $src_width = $image_size[0];
                    $src_height = $image_size[1];

                    $ratio_w = $src_width/$thumb[0];
                    $ratio_h = $src_height/$thumb[1];

                    if($ratio_w>$ratio_h){
                        $thumb_config['width'] = $src_width/$ratio_w;
                        $thumb_config['height'] = $src_height/$ratio_w;
                    }else{
                        $thumb_config['width'] = $src_width/$ratio_h;
                        $thumb_config['height'] = $src_height/$ratio_h;
                    }


                    $this->image_lib->initialize($thumb_config);
                    $this->image_lib->resize();

                    $thumb_config['source_image'] = $upload_data['file_path'].$upload_data['file_name'].'_'.$key.'.jpg';
                    $this->image_lib->initialize($thumb_config);

                    if (isset($thumb[3]) && $thumb[3] == 1){
                        $this->image_lib->watermark();
                    }

                    $this->image_lib->clear();
                }
            }
            /**************************************************************
            [file_name] => 584b66fe8038fccc7a35d5bea82df799dXsQaHPp.jpg
            [file_type] => image/jpeg
            [file_path] => E:/wamp/www/vhosts/alec/1/data/1/ads/201403/
            [full_path] => E:/wamp/www/vhosts/alec/1/data/1/ads/201403/584b66fe8038fccc7a35d5bea82df799dXsQaHPp.jpg
            [raw_name] => 584b66fe8038fccc7a35d5bea82df799dXsQaHPp
            [orig_name] => 584b66fe8038fccc7a35d5bea82df799dXsQaHPp.jpg
            [client_name] => Universe_and_planets_digital_art_wallpaper_albireo.jpg
            [file_ext] => .jpg
            [file_size] => 322.5
            [is_image] => 1
            [image_width] => 1680
            [image_height] => 1050
            [image_type] => jpeg
            [image_size_str] => width="1680" height="1050"
            [save_path] => 1/ads/201403/584b66fe8038fccc7a35d5bea82df799dXsQaHPp.jpg
             ************************************************************************/
            return $upload_data;
        }

    }


    protected function send_email($type = '', $email  = '', $subject = '', $data = array())
    {
        $this->load->config('email');
        $admin = $this->config->item('admin');

        $data['site_name'] = $admin['name'];


        $this->load->library('email');
        $this->email->from($admin['email'], $admin['name']);
        $this->email->reply_to($admin['email'], $admin['name']);
        $this->email->to($email);

        $this->email->subject(sprintf($subject, $admin['name']));
        $this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
        //$this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));


        if(!$this->email->send())
        {
            /*echo $this->email->print_debugger();
            throw new Exception('邮件发送失败');*/
            return false;
        }
        return true;

    }

    protected function _curl_get_content($url,$json=true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        if($json)
            $result = json_decode($result,true);
        return $result;
    }

    protected function machining($array,$array_key){
        if(isset($array_key['content_key'])){
            $array = $this->remove_html($array,$array_key['content_key']);
        }
        if(isset($array_key['image_key'])){
            $array = $this->image_url($array,$array_key['image_key']);
        }
        return $array;
    }

    private function remove_html($array,$key){
        $array_dimensional = $this->check_key_exists($array,$key);

        if($array_dimensional!=0){
            $html = $this->input->get('html');
            if(!empty($html)){
                if($array_dimensional==1){
                    $array[$key] = strip_tags($array[$key]);
                }else{
                    foreach($array as &$v){
                        $v[$key] = strip_tags($v[$key]);
                        unset($v);
                    }
                }
            }
        }
        return $array;
    }

    private function image_url($array,$key){
        $array_dimensional = $this->check_key_exists($array,$key);

        if($array_dimensional!=0){
            if($array_dimensional==1) {
                $array[$key] = base_url() . $array[$key];
            }else{
                foreach ($array as &$v) {
                    if (strpos($v[$key], 'http') !== 0) {
                        $v[$key] = base_url() . $v[$key];
                        unset($v);
                    }
                }
            }
        }
        return $array;
    }

    private function check_key_exists($array,$key){
        if(isset($array[$key]) ){
            return 1;
        }
        if(isset($array[0][$key])){
            return 2;
        }
        return 0;
    }


}