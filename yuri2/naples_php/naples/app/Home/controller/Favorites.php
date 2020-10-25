<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2017/2/28
 * Time: 10:32
 */

namespace naples\app\Home\controller;


use naples\lib\base\Controller;
use naples\lib\Factory;
use Respect\Validation\Validator;

class Favorites extends Controller
{
    private $favs=[];
    private $db;

    public function __construct()
    {
        config('show_debug_btn',false);
        $db=Factory::getArrDatabase('sys/favorites');
        $this->db=$db;
        $this->favs=$db->data;
        $this->assign('favs',$this->favs);
    }

    /** 收藏夹展示页 */
    public function index(){

        $this->render();
    }

    public function addTestData(){
        $data=[];
        for ($i=0;$i<12;$i++){
            $data[\Yuri2::uniqueID()]=[
                'href'=>'href'.$i,
                'name'=>'name'.$i,
            ];
        }
        $this->db->data=$data;
        $this->db->save();
    }

    /**
     * @method post
     * @method ajax
     */
    public function getTitle(){
        $msg='success';
        $errno=0;
        $url=post('url');
        $data=[
            'msg'=>$msg,
            'errno'=>$errno,
            'data'=>'',
        ];
        try{
            Validator::url()->check($url);
            $html=file_get_contents($url);
            preg_match("/<title>(.*)<\\/title>/i",$html, $title);
            $data['data']= $title[1];
            return $data;
        }catch (\Exception $e){
            return $data;
        }

    }

    /**
     * 添加一项收藏夹
     * @method post
     */
    public function addAFav(){
        $href=post('href');
        $name=post('name');
        $id=\Yuri2::uniqueID();
        $this->db->data[$id]=['href'=>$href,'name'=>$name];
        $this->db->save();
        redirect(urlBased('index'));
    }

    public function delFav(){
        if ($id=get('id')){
            unset($this->db->data[$id]);
            $this->db->save();
        }
        redirect(urlBased('index'));
    }
}