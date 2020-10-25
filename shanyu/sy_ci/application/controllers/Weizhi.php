<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weizhi extends CI_Controller {

	public function index()
	{
		$this->load->library('xmlrpc');
		$this->load->library('xmlrpcs');

        // MetaWeblog API (with MT extensions to structs)
        // http://www.xmlrpc.com/stories/storyReader
		$config['functions']['metaWeblog.newPost'] = array('function' => 'Weizhi.post_entry');
        $config['functions']['metaWeblog.editPost'] = array('function' => 'Weizhi.post_entry');
        $config['functions']['metaWeblog.getCategories'] = array('function' => 'Weizhi.get_category');
        // $config['functions']['metaWeblog.getPost'] = array('function' => 'Weizhi.get_category');
        // $config['functions']['metaWeblog.getRecentPosts'] = array('function' => 'Weizhi.get_category');
        $config['functions']['metaWeblog.newMediaObject'] = array('function' => 'Weizhi.upload_entry');
        // $config['functions']['metaWeblog.deletePost'] = array('function' => 'Weizhi.get_category');
        // $config['functions']['metaWeblog.getUsersBlogs'] = array('function' => 'Weizhi.get_category');

		$config['object'] = $this;
		$this->xmlrpcs->initialize($config);
		$this->xmlrpcs->serve();
	}

	private function check_user($user='',$pass='')
    {
        $weblog=config_item('weblog');
		if($user != $weblog['username'] || $pass != $weblog['password']){
            $this->xmlrpc->send_error_message('101', 'User Error');
        }
	}

	public function post_entry($request)
    {
        $parameters = $request->output_parameters();
        //file_put_contents('data.xml', serialize($parameters));

        //检测用户
        $this->check_user($parameters[1],$parameters[2]);

        //检测分类
        $category_title=$parameters[3]['categories'][0];
        $category_list=config_item('category_list');
        foreach ($category_list as $k => $v) {
            if(array_search($category_title, $v)){
                $category_id=$k;
                break;
            }
        }
        if(!isset($category_id)){
            return $this->xmlrpc->send_error_message('102', 'Category Error');
        }

        //存入数据
        $this->load->database();
        $content=htmlspecialchars_decode($parameters[3]['description']);
        $description=mb_substr(str_replace("\n","",strip_tags($content)),0,150);
        //$this->load->library('parsedown');
        //$content=$this->parsedown->text($content);
        $content=htmlspecialchars($content);
        $title=$parameters[3]['title'];
        $data = array(
            'title'=>$title,
            'cid' => $category_id,
            'content'=>$content,
            'description'=>$description,
            'edit_time'=>time()
        );
        //判断修改添加
        if($parameters[0]){
            $where = "id = ".$parameters[0];
            $sql_string = $this->db->update_string('article', $data, $where);
        }else{
            $data['add_time']=time();
            $sql_string = $this->db->insert_string('article', $data);
        }
        $query=$this->db->query($sql_string);
        if(!$query) return $this->xmlrpc->send_error_message('103', 'Query Error');

        //删除首页缓存
        $this->load->driver('cache');
        $this->cache->file->clean();

        if($parameters[0]){
            $response = array('true','boolean');
        }else{
            $insert_id=$this->db->insert_id();
            $response = array($insert_id,'string');
        }
        return $this->xmlrpc->send_response($response);
	}

	public function get_category($request)
    {
		$parameters = $request->output_parameters();

        $category_list=config_item('category_list');
        $struct=array();
        foreach ($category_list as $k => $v) {
            $_html=config_item('base_url').'article/'.$v['name'].'.html';
            $_rss=config_item('base_url').'article/'.$v['name'].'/rss.html';
            $struct[]=array(
                array(
                    'categoryId' => array($k, 'int'),
                    'parentId' => array('0', 'int'),
                    'description' => array($v['title'], 'string'),
                    'categoryDescription' => array('', 'string'),
                    'categoryName' => array($v['title'], 'string'),
                    'htmlUrl' => array($_html, 'string'),
                    'rssUrl' => array($_rss, 'string'),
                ),
                'struct'
            );
        }

        $response=array(
            $struct,
            'array'
        );
        return $this->xmlrpc->send_response($response);
	}

    public function upload_entry($request){
        $parameters = $request->output_parameters();

        $file_name=$parameters[3]['name'];
        $file=FCPATH.'/uploads/'.$file_name;

        file_put_contents($file, $parameters[3]['bits']);

        $response=array(
            array(
                'url'=>array('/uploads/'.$file_name,'string')
            ),
            'struct'
        );
        return $this->xmlrpc->send_response($response);
    }

    // public function show_xml(){
    //     $parameters=unserialize(file_get_contents('data.xml'));
    //     $file_name=$parameters[3]['name'];
    // }

}