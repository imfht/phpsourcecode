<?php
namespace app\sale\service;
/**
 * 用户接口
 */
class MemberService extends \app\base\service\BaseService {

    /**
     * 注册接口
     */

    public function regField() {
        $code = request('', 'sale_code');
        return [
            [
                'label' => '推荐码',
                'tip' => '用户推荐码',
                'name' => 'sale_code',
                'attr' => $code ? 'readonly' : '',
                'value' => $code
            ]
        ];
    }

    /**
     * 注册接口
     * @param $userId
     * @param $nickname
     * @return bool
     */
    public function reg($userId, $nickname) {
        if (!$this->regSale($userId, $nickname)) {
            return false;
        }
        unset($_GET['sale_code']);

        return true;
    }

    /**
     * 注册推广
     * @param $userId
     * @param $nickname
     * @return bool
     */
    protected function regSale($userId, $nickname) {
        $code = request('post', 'sale_code', 0);
        $config = target('sale/saleConfig')->getConfig();
        $info = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        if (!empty($info)) {
            return true;
        }

        if (empty($code)) {
            if (!$config['apply_check'] && $config['apply_type'] == 1) {
                if (!target('sale/Sale', 'service')->addAgent($userId, 0, 0)) {
                    return $this->error(target('sale/Sale', 'service')->getError());
                }
            }
            target('sale/Sale', 'service')->noticeSale('join', $userId, [
                '推荐人' => '无',
                '时间' => date('Y-m-d H:i', time())
            ]);

            return true;
        }
        $info = target('sale/SaleUser')->getWhereInfo([
            'code' => $code
        ]);
        if (empty($info)) {
            return $this->error('推荐码有误，请核对后输入！');
        }
        if (!$info['agent']) {
            return true;
        }
        if (!$config['apply_check'] && $config['apply_type'] == 1) {
            if (!target('sale/Sale', 'service')->addAgent($userId, $info['user_id'], 0)) {
                return $this->error(target('sale/Sale', 'service')->getError());
            }
        } else {
            if (!target('sale/Sale', 'service')->addUser($userId, $info['user_id'])) {
                return $this->error(target('sale/Sale', 'service')->getError());
            }
        }
        $time = time();

        //加入通知
        target('sale/Sale', 'service')->noticeSale('join', $userId, [
            '推荐人' => $info['show_name'],
            '时间' => date('Y-m-d H:i', $time)
        ]);

        //通知上级
        $parentList = target('sale/saleUser')->loadParentList($info['user_id'], 0, $config['notice_next_level']);

        if (empty($parentList)) {
            return true;
        }

        $i = 0;
        foreach ($parentList as $vo) {
            $i++;
            target('sale/Sale', 'service')->noticeSale('next', $vo['user_id'], [
                '昵称' => $nickname,
                '时间' => date('Y-m-d H:i', $time),
                '下线层级' => $i
            ]);
        }

        return true;
    }

    /**
     * 删除接口
     * @param $id
     * @return bool
     */
    public function del($id) {
        if (!target('sale/SaleUser')->where(['user_id' => $id])->delete()) {
            return $this->error('推广用户删除失败！');
        }

        return true;

    }


}

