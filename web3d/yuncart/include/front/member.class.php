<?php

defined('IN_CART') or die;

/**
 *
 * 用户个人中心
 *
 */
class Member extends Base
{

    private $uid;

    /**
     *
     * 构造函数，赋值uid
     *
     */
    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
        if (!isset($_SESSION["uid"])) {
            redirect(url("index", 'user', "login"));
        }
        $this->uid = intval($_SESSION["uid"]);
    }

    /**
     *
     * 个人中心首页
     *
     */
    public function index()
    {
        $time = time();
        //用户信息
        $where = "uid='" . $this->uid . "'";
        $this->data["user"] = DB::getDB()->selectrow("user", "*", $where);

        //正在进行的订单
        $this->data['runtrades'] = DB::getDB()->selectcount("trade", $where . " AND isfinish=0");

        //已经成功的订单
        $finishtrades = DB::getDB()->select("trade", "totalfee", $where . " AND isfinish=1 AND iscancel=0");
        $this->data['totalfee'] = 0;
        foreach ($finishtrades as $trade) {
            $this->data['totalfee'] += getPrice($trade['totalfee'], -2, 'float');
        }
        //我的优惠券
        $this->data["couponcount"] = Promotion::getCouponsCount($this->uid);
        $this->output("member");
    }

    /**
     *
     * 个人详情
     *
     */
    public function info()
    {
        if (ispostreq()) {
            $email = $_POST["email"];
            if (!$email || !isemail($email)) { //email格式不正确
                $this->setHint("email_error", "error");
            }
            $name = trim($_POST["name"]);
            $sex = !empty($_POST["sex"]) ? intval($_POST["sex"]) : 0;
            $link = trim($_POST["link"]);

            $data = array("email" => $email,
                "name" => $name,
                "sex" => $sex,
                "link" => $link);
            $uid = $_SESSION["uid"];
            DB::getDB()->update("user", $data, "uid='{$this->uid}'");
            $this->setHint("info_success", "success");
        } else {
            $this->getHint();
            $this->data["user"] = DB::getDB()->selectrow("user", "*", "uid='{$this->uid}'");
            $this->output("info");
        }
    }

    /**
     *
     * 修改密码
     *
     */
    public function pass()
    {
        if (ispostreq()) {
            $pass = trim($_POST["pass"]);
            $newpass = trim($_POST["newpass"]);
            $newpass2 = trim($_POST["newpass2"]);
            $uid = $_SESSION["uid"];
            //判断密码
            $len = strlen($newpass);
            if ($len < 4 || $len > 20) {
                $this->setHint("newpass_length_error", 'error');
            }
            if ($newpass != $newpass2) {
                $this->setHint("newpass_not_equal", 'error');
            }
            $user = DB::getDB()->selectrow("user", "uid,uname,pass,salt", "uid='$uid'");
            if (!empty($user) && checkpass($pass, $user["salt"], $user["pass"])) { //存在用户
                $data = encpass($newpass);
                DB::getDB()->update("user", $data, "uid={$this->uid}");
                $this->setHint("newpass_success", 'success');
            } else {
                $this->setHint("oldpass_error", 'error');
            }
        } else {
            $this->getHint();
            $this->output("pass");
        }
    }

    /**
     *
     * 首页，显示所有的地址
     *
     */
    public function address()
    {
        if (ispostreq()) {
            $addressid = intval($_POST["addressid"]);

            //如果是修改，但是没有权限
            if ($addressid && !DB::getDB()->selectexist("user_address", "addressid", "addressid='$addressid' AND uid='" . $this->uid . "'")) {
                $this->setHint("user_no_priv", "error");
            }

            //参数
            $receiver = trim($_POST["receiver"]);
            $province = trim($_POST["province"]);
            $city = trim($_POST["city"]);
            $district = trim($_POST["district"]);
            $zipcode = trim($_POST["zipcode"]);
            $link = trim($_POST["link"]);
            $address = trim($_POST["address"]);
            $data = array(
                "receiver" => $receiver,
                "province" => $province,
                "city" => $city,
                "district" => $district,
                "zipcode" => $zipcode,
                "link" => $link,
                "address" => $address,
                "uid" => $this->uid);
            $text = '';
            if ($addressid) { //修改地址
                $text = 'edit';
                DB::getDB()->update("user_address", $data, "addressid='$addressid'");
            } else { //增加地址
                $text = 'add';
                DB::getDB()->insert("user_address", $data);
            }
            $this->setHint("address_{$text}_success", "success");
        } else {
            $this->getHint();
            if (isset($_GET["op"]) && ($_GET['op'] == 'edit')) { //操作
                $addressid = intval($_GET["addressid"]);
                $this->data["address"] = DB::getDB()->selectrow("user_address", "*", "addressid='$addressid' AND uid='" . $this->uid . "'");
                if (!$this->data["address"]) {
                    $this->setHint("address_not_exist", "error");
                }
                $this->getDistrictopt($this->data["address"]['province'], $this->data["address"]['city'], $this->data["address"]['district']);
                $this->data['opertype'] = "edit";
            } else {
                $this->data['opertype'] = "add";
            }

            //收货地址列表
            $this->data['addresslist'] = DB::getDB()->select("user_address", "*", "uid='" . $this->uid . "'");
            $this->output("myaddress");
        }
    }

    /**
     *
     * 删除地址
     *
     */
    public function deladdress()
    {
        $addressid = intval($_POST["addressid"]);
        $where = "addressid='$addressid' AND uid='" . $this->uid . "' ";
        //判断地址是否存在
        if (!DB::getDB()->selectexist("user_address", "addressid", $where)) {
            exit("failure");
        } else {
            DB::getDB()->delete("user_address", $where);
            exit("success");
        }
    }

}
