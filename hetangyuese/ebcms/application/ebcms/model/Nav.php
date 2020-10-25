<?php
namespace app\ebcms\model;

use think\Model;

class Nav extends Model
{

    protected $pk = 'id';
    protected $type = [
        'eb_ext' => 'json',
    ];

    public function lists(){
        $apps = array_keys(get_app());
        $apps[] = '';
        $where = [
            'status'    =>  1,
            'app'       =>  ['in',$apps]
        ];
        return $this -> where($where)->order('sort desc,id asc');
    }

    // 获取链接
    protected function getUrlAttr($value, $data)
    {
        $url = htmlspecialchars_decode($data['eb_url']);
        return (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : \think\Url::build($url);
    }
    
}