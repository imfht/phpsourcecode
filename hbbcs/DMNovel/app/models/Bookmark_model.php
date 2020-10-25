<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 17-1-9
 * Time: 上午9:55
 */
class Bookmark_model  extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    function get($id=null,$where=null,$select=null,$join=false,$limit='',$offset=0,$order='id',$sort='ASC') {
        if ($where) $this->db->where($where);
        if ($select) $this->db->select($select);
        if ($limit) $this->db->limit($limit,$offset);
        if ($order) $this->db->order_by($order,$sort);
        if ($join) {
            $this->db->join('story','story.id=bookmark.story_id','left');
        }
        if ($id) {
            return $this->db->get_wehre('bookmark',['bookmark.id'=>$id])->row_array();
        }

        return $this->db->get('bookmark')->result_array();
    }

}