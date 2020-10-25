<?php

/**
 * 实名认证
 */
namespace app\member\model;

use app\system\model\SystemModel;

class MemberRealModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'real_id',
        'into' => '',
        'out' => '',
    ];

    protected function base($where) {
        return $this->table('member_real(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
        }
        return $info;
    }

    public function real($userId, $name, $idCard, $card_image, $card_image_back, $card_image_hand) {
        $info = target('member/MemberReal')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        if($info['status']) {
            return true;
        }
        $name = html_clear($name);
        $idCard = html_clear($idCard);
        if(empty($name) || empty($idCard)) {
            $this->error = '请填写姓名与者身份证号码！';
            return false;
        }
		if (!$this->isIdcard($idCard)) {
            $this->error = '身份证号码输入不正确！';
            return false;
        }
		if(empty($card_image)){
			$this->error = '请上传身份证正面！';
            return false;
		}
		if(empty($card_image_back)){
			$this->error = '请上传身份证反面！';
            return false;
		}
		if(empty($card_image_hand)){
			$this->error = '请上传手持身份证照片！';
            return false;
		}
        if(empty($info)) {
            $status = $this->add([
                'user_id' => $userId,
                'name' => $name,
                'idcard' => $idCard,
				'card_image' => $card_image,
				'card_image_back' => $card_image_back,
				'card_image_hand' => $card_image_hand,
                'status' => 1,
                'time' => time()
            ]);
        }else {
            $status = $this->edit([
                'real_id' => $info['real_id'],
                'user_id' => $userId,
                'name' => $name,
                'idcard' => $idCard,
				'card_image' => $card_image,
				'card_image_back' => $card_image_back,
				'card_image_hand' => $card_image_hand,
                'status' => 1,
                'time' => time()
            ]);
        }
        if(!$status) {
            $this->error = '实名认证登记失败！';
            return false;
        }
        return true;
    }

    private function isIdcard($id) {
        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if (!preg_match($regx, $id)) {
            return FALSE;
        }
        if (15 == strlen($id)) {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return FALSE;
            } else {
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b = (int)$id{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id, 17, 1)) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            }
        }

    }


}