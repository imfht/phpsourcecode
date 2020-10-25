<?php

defined('IN_CART') or die;

/**
 *
 * 上传，每个图片都会生成一个小图，给图片空间展示用
 * 
 */
class Uploadfile extends Base
{

    protected $ext = array();
    protected $thumbs = array();
    private $upload = '';
    private $spic = "50";
    private $bpic = "310";
    private $mpic = "160";

    public function __construct()
    {
        $this->upload = new Upload();
        $this->spic = $this->spic;
        $this->mpic = $this->mpic;
        $this->bpic = $this->bpic;
    }

    /**
     *
     * 上传普通图片
     * 
     */
    public function pic()
    {
        $this->upload->setExts(array("jpg", "jpeg", "gif", "png"));
        $this->upload->setThumbs(array("s" => $this->spic));
        $data = $this->_upload();
        exit(json_encode($data));
    }

    /**
     *
     * 替换图片
     * 
     */
    public function replacepic()
    {
        $picid = intval($_GET["picid"]);
        $pic = DB::getDB()->selectrow("pic", "*", "picid='$picid'");

        $ret = array("err" => '', "msg" => '', 'picid' => $picid, 'name' => $pic['name']);
        if (!$pic) {
            $ret["err"] = __("file_not_exists");
        } else {
            $thumbs = array();
            $pic['spic'] && $thumbs["s"] = $this->spic;
            $pic['mpic'] && $thumbs["m"] = $this->mpic;
            $pic['bpic'] && $thumbs["b"] = $this->bpic;

            $this->upload->setExts(array("jpg", "jpeg", "gif", "png"));
            $this->upload->setThumbs($thumbs);
            $this->upload->setCustomname(basename($pic['pic']));
            $this->upload->setSaveDir(dirname($pic['pic']));
            if ($this->upload->uploadfile()) {//如果上传成功
                $info = $this->upload->uploadInfo[0];
                $msg = $info['url'];
                $name = $info['name'];
                $this->adminlog("al_pic", array("do" => "replace", "name" => $name));
                $file = SITEPATH . '/' . $msg;
                $ret['msg'] = $file;
                $ret['name'] = $name;
                if (file_exists($file)) {//如果文件存在
                    list($width, $height, $type, $attr) = @getimagesize($file);
                    $filesize = filesize($file);
                    if ($width && $height && $filesize) {//更新图片
                        DB::getDB()->update("pic", array("name" => $name,
                            "width" => $width,
                            "height" => $height,
                            "pic" => $msg,
                            "spic" => isset($info['spic']) ? 1 : 0,
                            "mpic" => isset($info['mpic']) ? 1 : 0,
                            "bpic" => isset($info['bpic']) ? 1 : 0,
                            "size" => $filesize), "picid='$picid'"); //直接更新图片
                    }
                }
                $ret['msg'] = $msg;
            } else {//上传失败
                $ret['err'] = jsonString($this->upload->getError());
            }
        }
        exit(json_encode($ret));
    }

    /**
     *
     * 商品图片
     * 
     */
    public function item()
    {
        $this->upload->setExts(array("jpg", "jpeg", "gif", "png"));
        $this->upload->setThumbs(array("s" => $this->spic, "m" => $this->mpic, "b" => $this->bpic));
        $data = $this->_upload();
        exit(json_encode($data));
    }

    /**
     *
     * 自定义规格图片
     * 
     */
    public function specselfpic()
    {
        $buttonid = trim($_GET["buttonid"]);
        $this->upload->setExts(array("jpg", "jpeg", "gif", "png"));
        $this->upload->setThumbs(array("s" => $this->spic, "b" => $this->bpic));
        $data = $this->_upload();
        $data['buttonid'] = $buttonid;
        exit(json_encode($data));
    }

    /**
     *
     * 规格图片
     * 
     */
    public function spec()
    {
        $buttonid = trim($_GET["buttonid"]);
        $savepath = SITEPATH . "/uploads/spec";
        $this->upload->setExts(array("jpg", "jpeg", "gif", "png"));
        $this->upload->setThumbs(array("s" => $this->spic));
        $this->upload->setSaveDir($savepath);
        $data = $this->_upload();
        $data['buttonid'] = $buttonid;
        exit(json_encode($data));
    }

    /**
     *
     * 快递单图片
     * 
     */
    public function expresstpl()
    {
        $savepath = SITEPATH . "/uploads/express";
        $this->upload->setExts(array("jpg", "jpeg", "gif", "png"));
        $this->upload->setThumbs(array("s" => $this->spic));
        $this->upload->setSaveDir($savepath);
        $data = $this->_upload();
        exit(json_encode($data));
    }

    /**
     *
     * 返回上传文件信息
     *
     */
    private function _upload()
    {
        $data = array("err" => '', "msg" => '');
        if (!$this->upload->uploadfile()) {
            $data['err'] = jsonString($this->upload->getError());
        } else {
            $info = $this->upload->uploadInfo[0];
            $msg = $info['url'];
            $name = $info['name'];
            $this->adminlog("al_pic", array("do" => "add", "name" => $name));
            $file = SITEPATH . '/' . $msg;
            if (file_exists($file)) {
                list($width, $height, $type, $attr) = @getimagesize($file);
                $filesize = filesize($file);
                if ($width && $height && $filesize) {
                    DB::getDB()->insert("pic", array("pic" => $msg, "addtime" => time(),
                        "spic" => isset($info['spic']) ? intval($info['spic']) : 0,
                        "mpic" => isset($info['mpic']) ? intval($info['mpic']) : 0,
                        "bpic" => isset($info['bpic']) ? intval($info['bpic']) : 0,
                        "name" => $name, "size" => $filesize,
                        "width" => $width, "height" => $height));
                }
            }

            $upyun_status = getConfig("upyun_status", 0); //又拍云
            if ($upyun_status) {
                require THIRDPATH . "/upyun/upyun.class.php";
                $upyun_domain = getConfig("upyun_domain");
                $upyun_uname = getConfig("upyun_uname");
                $upyun_pass = getConfig("upyun_pass");
                $upyun_space = getConfig("upyun_space");
                $upyun = new UpYun($upyun_space, $upyun_uname, $upyun_pass);
                $ret = $upyun->writeFile("/" . trim($msg, "/"), file_get_contents($msg), true);
                if ($ret) {
                    $data['msg'] = trim($upyun_domain, "/") . '/' . $msg;
                } else {
                    $data['err'] = __("upload_upyun_error");
                }
            } else {
                $data['msg'] = $msg;
            }
        }
        return $data;
    }

}
