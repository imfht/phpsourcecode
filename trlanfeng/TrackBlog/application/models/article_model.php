<?php

class Article_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function getOne($id)
    {
        $query = $this->db->get_where('cms_cms', array('id' => $id));
        return $query->row_array();
    }

    public function getList($filter = array(), $limit = 0, $offset = 0, $order = "")
    {
        $this->db->order_by($order);
        $query = $this->db->get_where('cms_cms', $filter, $limit, $offset);
        return $query->result_array();
    }
    public function getListByTag($tagname, $limit = 0, $offset = 0, $order = "")
    {
        $this->db->order_by($order);
        $this->db->like('tags',$tagname);
        $query = $this->db->get_where('cms_cms',array() , $limit, $offset);
        return $query->result_array();
    }

    public function add($post)
    {
//        if ($post['times'] == '')
//        {
//            $time = time();
//        }
//        else
//        {
//            $time = $post['times'];
//        }
        if (!isset($post['content'])) {
            $post['content'] = "";
        }
        if (empty($post['slug'])) {
            $post['slug'] = "";
        } else {
            $post['slug'] = implode(',', $post['slug']);
        }
        $data = array(
            'name' => $post['name'],
            'link' => $post['link'],
            'content' => $post['content'],
            'cat' => $post['cat'],
            'times'=>time(),
            'allowcmt' => $post['allowcmt'],
            'slug' => $post['slug'],
            'orders' => $post['orders'],
            'status' => $post['status'],
            'thumbpic' => $post['thumbpic'],
            'tags' => $post['tags'],
            'keywords' => $post['keywords'],
            'description' => $post['description']
        );

        $result = $this->db->insert('cms_cms', $data);
        if ($result && $this->db->affected_rows() != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function edit($id, $post)
    {
        $data = array(
            'name' => $post['name'],
            'link' => $post['link'],
            'content' => $post['content'],
            'cat' => $post['cat'],
            //'times'=>$time,
            'allowcmt' => $post['allowcmt'],
            'slug' => implode(',', $post['slug']),
            'orders' => $post['orders'],
            'status' => $post['status'],
            'thumbpic' => $post['thumbpic'],
            'tags' => $post['tags'],
            'keywords' => $post['keywords'],
            'description' => $post['description']
        );
        $result = $this->db->update('cms_cms', $data, array('id' => $id));
        if ($result && $this->db->affected_rows() != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $result = $this->db->delete('cms_cms', array('id' => $id));
        if ($result && $this->db->affected_rows() != 0) {
            return true;
        } else {
            return false;
        }
    }
}

?>