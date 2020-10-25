<?php
class Category_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    public function getOne($id)
    {
        $query = $this->db->get_where('cms_category',array('id'=>$id));
        return $query->row_array();
    }
    public function getOneByCatName($catname)
    {
        $query = $this->db->get_where('cms_category',array('nickname'=>$catname));
        return $query->row_array();
    }
    public function getList($filter = array(),$limit = 0,$offset = 0,$order ="")
    {
        $this->db->order_by($order);
        $query = $this->db->get_where('cms_category',$filter,$limit,$offset);
        return $query->result_array();
    }
    public function add($post)
    {
        $data = array(
            'name'=>$post['name'],
            //'link'=>$post['link'],
            'nickname'=>$post['nickname'],
            'fid'=>$post['fid'],
            'intro'=>$post['intro'],
            'orders'=>$post['orders'],
            'status'=>$post['status'],
            //'thumbpic'=>$post['thumbpic'],
            'keywords'=>$post['keywords'],
            'description'=>$post['description']
        );
        $result = $this->db->insert('cms_category',$data);
        if ($result && $this->db->affected_rows() != 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function edit($id,$post)
    {
        $data = array(
            'name'=>$post['name'],
            //'link'=>$post['link'],
            'nickname'=>$post['nickname'],
            'fid'=>$post['fid'],
            'intro'=>$post['intro'],
            'orders'=>$post['orders'],
            'status'=>$post['status'],
            //'thumbpic'=>$post['thumbpic'],
            'keywords'=>$post['keywords'],
            'description'=>$post['description']
        );
        $result = $this->db->update('cms_category',$data,array('id'=>$id));
        if ($result && $this->db->affected_rows() != 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function delete($id)
    {
        $result = $this->db->delete('cms_category',array('id'=>$id));
        if ($result && $this->db->affected_rows() != 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>