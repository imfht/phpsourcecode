<?php

Class ApiCloudModel {

    protected $model   = '';
    protected $id      = '';
    protected $class   = '';
    protected $filter  = '';
    protected $limit   = '';
    protected $skip    = '';
    protected $order   = '';
    protected $where   = '';
    protected $fields  = '';



    protected $url      = 'https://d.apicloud.com/';

    protected $appKey = '';

    public function __construct($model =''){
        if($model) $this->model   =   $model;
        $API_ID = session('API_ID');
        $API_KEY = session('API_KEY');
        $this->appKey = sha1($API_ID."UZ".$API_KEY."UZ".getMillisecond()).".".getMillisecond();
    }

    public function getPage($map = '',$page =1, $limit = 16,$order =' createAt DESC ')
    {
        $countList = $this->where($map)->select();

        $volist = $this->where($map)->page($page,$limit)->order($order)->select();
        $data['cpage'] = $page;
        $data['limit'] = $limit;
        $data['count'] = count($countList);
        $pagecount = ceil($data['count'] / $data['limit']);
        if ($pagecount < 1) $pagecount =1;
        $data['pages'] = $pagecount;
        $data['volist'] = $volist;

        return $data;
    }

    public function page($page = 1,$limit = 16)
    {
        $this->limit = $limit;
        if ($page > 1) {
            $this->skip = ($page - 1) * $limit;
        }
        return $this;
    }

    public function limit($limit = 16)
    {
        $this->limit = $limit;
        return $this;
    }

    public function order($order='createAt DESC')
    {
        $this->order = $order;
        return $this;
    }

    public function fields($fields='')
    {
        $field_arr = explode(",", $fields);
        $data = array();
        foreach ($field_arr as $k) {
            $f[$k] = 'true';
            $data[] = $f;
        }
        $this->fields = $data;
        return $this;
    }


    public function where($where){
        $this->class = $where['class'];
        $this->id = $where['id'];
        unset($where['class']);
        unset($where['id']);

        $this->where = $where;

        return $this;
    }

    public function find($id ='')
    {
        if ($this->class) {
            $url = $this->url.'mcm/api/'.$this->class;
        }else{
            return false;
        }
        if ($id)
            $this->id = $id;

        if ($this->id) {
            $url .= '/'.$this->id;
            $ret  = $this->baseHttp($url);
            if ($ret[0])
                $vo = $ret[0];
            else
                $vo = $ret;
            return $vo;
        }


        $filter = array();
        if ($this->fields) {
            $filter['fields'] = $this->fields;
        }
        if ($this->where) {
            $filter['where'] = $this->where;
        }
        if ($this->skip) {
            $filter['skip'] = $this->skip;
        }
        if ($this->limit) {
            $filter['limit'] = $this->limit;
        }
        if ($filter) {
            $filter = json_encode($filter);
            $url.='?filter='.$filter;
        }
        $ret  = $this->baseHttp($url);
        if ($ret[0]) {
            return $ret[0];
        }
        return false;
    }

    public function select()
    {
        if ($this->class) {
            $url = $this->url.'mcm/api/'.$this->class;
        }

        $filter = array();
        if ($this->fields) {
            $filter['fields'] = $this->fields;
        }
        if ($this->where) {
            $filter['where'] = $this->where;
        }
        if ($this->skip) {
            $filter['skip'] = $this->skip;
        }
        // if ($this->order) {
        //     $filter['order'] = $this->order;
        // }
        if ($this->limit) {
            $filter['limit'] = $this->limit;
        }
        if ($filter) {
            $filter = json_encode($filter);
            $url.='?filter='.$filter;
        }

        $data =  $this->baseHttp($url);
        return $data;
    }

    public function save($data='')
    {
        if ($data['id']) {
            $this->id = $data['id'];
        }
        unset($data['class']);
        unset($data['id']);

        if ($this->class) {
            $url = $this->url.'mcm/api/'.$this->class;
        }
        if ($this->id) {
            $url.= '/'.$this->id;
        }else{
            return false;
        }
        $ret = $this->baseHttp($url,'PUT',$data);
        return $ret ;
    }

    public function add($data ='')
    {

        unset($data['class']);
        unset($data['id']);
        if ($this->class) {
            $url = $this->url.'mcm/api/'.$this->class;
        }
        $ret = $this->baseHttp($url,'POST',$data);
        return $ret ;
    }

    public function delete($id='')
    {
        if ($id) $this->id = $id;
        if ($this->class)
            $url = $this->url.'mcm/api/'.$this->class;
        else
            return false;
        if ($this->id)
            $url.= '/'.$this->id;
        else
            return false;
        $ret = $this->baseHttp($url,'delete');
        return $ret ;

    }


    public function baseHttp($url = '',$method = 'GET',$data = '')
    {
        $API_ID = session('API_ID');
        $header = array(
            'X-APICloud-AppId: '.$API_ID,
            'X-APICloud-AppKey: '.$this->appKey,
            'Content-Type: application/json',
        );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        if (strtolower($method) == 'post')
            curl_setopt($ch, CURLOPT_POST, 1);
        if (strtolower($method) == 'delete')
            curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
        elseif (strtolower($method) == 'put')
            curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        curl_close($ch);
        $ret = json_decode($result,true);
        if ($result && $ret['msg']) return false;
        return $ret;
    }
}
