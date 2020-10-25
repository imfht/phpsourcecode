<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article_model extends CI_model
{
    
    public function get_article_list($offset=1,$limit=10,$where='1=1')
    {
        $this->load->database();
        $offset=($offset-1)*$limit;
        $sql_article="SELECT id,cid,title,DATE(FROM_UNIXTIME(add_time)) as time FROM {$this->db->dbprefix('article')} WHERE {$where} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";
        $query=$this->db->query($sql_article);
        $this->db->close();
        return $query->result_array();
    }

    public function get_article_count($where='1=1')
    {
        $this->load->database();
        $sql_article="SELECT count(1) FROM {$this->db->dbprefix('article')} WHERE {$where}";
        $query=$this->db->query($sql_article);
        $this->db->close();

        return current($query->row_array());
    }

    public function get_article_detail($where='1=1')
    {
        $this->load->database();
        $sql_article="SELECT * FROM {$this->db->dbprefix('article')} WHERE {$where} LIMIT 1";
        $query=$this->db->query($sql_article);
        $this->db->close();
        return $query->row_array();
    }
}