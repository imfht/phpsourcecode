<?php
namespace Home\Controller;

use Think\Controller;

class CommonController extends Controller
{

    public function setMenuSession($id, $subid)
    {
        $_SESSION['mid'] = $id;
        $_SESSION['cmid'] = $subid;
        echo $_SESSION['mid'];
    }
}