<?php
namespace app\weiwork_app\controller;

use app\common\api\Dict;
use app\common\logic\EbaLogic;
use think\Config;
use think\Db;
use think\WeChat;
use app\common\controller\WeiWorkBase;
use app\common\api\WeiWork;
use app\common\logic\WeiWorkLogic;
use app\common\model\app\AppEmp;
use hanvon\businesscard\OcrBcard;
use think\Log;

/**
 * Class Eba
 * 客户管理
 * @package app\weiwork_app\controller
 */
class Eba extends WeiWorkBase {
    public $web_card_path = ROOT_PATH . 'public' . DS .'static' . DS .'images' . DS . 'eba' . DS . 'card' . DS;
    public $temp_path = TEMP_PATH . 'file' . DS;

    /**
     * 客户列表
     * @return string
     */
    public function index() {
        $user_info_system = WeiWork::find_sys_user_info();
        Dict::init_static_list();

        if ($user_info_system['emp_id'] == '') {
            exit(lang("请系统管理员绑定此操作用户的员工编号,才能继续使用 客户管理 功能"));
        }

        //$this->assign('data', $data);
        //$this->assign('js_data', json_encode($js_data));
        return $this->fetch();
    }

    /**
     * 返回eba_list json数据
     * @return \think\response\Json
     */
    public function get_eba_list() {
        $user_info_system = WeiWork::find_sys_user_info();
        $eba_list = WeiWorkLogic::wk_get_eba_list($user_info_system);

        return json($eba_list);
    }

    /**
     * 保存
     * @return \think\response\Json
     * 1 不能写入 should_in 字段 - 应收款
     */
    public function save() {
        $allow_field = [
            'eba_id', 'eba_name', 'linkman', 'office_no', 'state',
            'mobile_no', 'e_mail', 'address', 'other_im_no', 'order_id',
            'emp_id', 'dept_id',
            // 'gender', 'service_id',
        ];
        $eba = new \app\common\model\eba\Eba();
        $post_data = input('post.');
        $config = Config::get('work_eba');
        if (empty($post_data)) {
            return json(lang('请提交数据'));
        }
        // 这里不能使用oit中的 直接删除数据再新增的做法是，
        // 界面中并没有所有数据，不能直接保证完整性
        // 注意使用事务,保证数据完整性
        // 在企业微信中增加的客户资料，oit 客户端需要重启才能刷新该字典
        if (empty($post_data['eba_id'])) {
            // 新增
            // 首先生成 临时区域 客户编码 流水码
            Db::startTrans();
            try {
                $allow_field[] = 'service_id';
                $user_info_system = WeiWork::find_sys_user_info();
                // 默认新增值
                $post_data['service_id'] = $config['temp_service_id'];
                $post_data['state'] = $config['temp_eba_state'];
                if (empty($post_data['service_id'])) {
                    return json(lang('请定义默认临时客户所在的区域'));
                }
                $post_data['eba_id'] = $eba->get_new_id(['service_id' => $post_data['service_id']], 'pad', $post_data['service_id'], 5);
                $post_data['order_id'] = $eba->get_new_order_id('order_id');
                $post_data['emp_id'] = $user_info_system['emp_id'];
                $post_data['dept_id'] = AppEmp::get($post_data['emp_id'])->dept_id;
                $result = $eba->allowField($allow_field)->save($post_data);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
            }
        } else {
            // 修改
            $result = $eba->isUpdate(true)->allowField($allow_field)->save($post_data);
        }

        if (!empty($result)) {
            $data = [
                'state' => 'success',
                'info' => lang('保存成功'),
                'pk_id' => $post_data['eba_id'],
                'result' => $result,
            ];
            // 数据保存成功，检测有没有上传了名片文件，有，将名片文件分别保存在 web框架目录与oit文件目录中
            // 注意是分别保存的
            if (!empty($post_data['pict_left_name'])) {
                // 检测是否曾经保存过文件,有 就先删除
                $obj_id = 'eba.' . $post_data['eba_id'];
                $file_type = $config['card_front_file_type'];

                //AppFsFile::destroy();

                $save_file = move_file_to_oit($post_data['pict_left_name'], $this->temp_path, $this->web_card_path, [
                    'file_type' => $file_type,
                    'obj_id' => $obj_id,
                ]);
                if ($save_file['state'] != true) {
                    Log::write('wework eba 保存名片的正面失败', 'notice');
                }
            }
            if (!empty($post_data['pict_right_name'])) {
                $obj_id = 'eba.' . $post_data['eba_id'];
                $file_type = $config['card_obverse_file_type'];

                $save_file = move_file_to_oit($post_data['pict_right_name'], $this->temp_path, $this->web_card_path, [
                    'file_type' => $file_type,
                    'obj_id' => $obj_id,
                ]);
                if ($save_file['state'] != true) {
                    Log::write('wework eba 保存名片的反面失败', 'notice');
                }
            }
        } else {
            $data = [
                'state' => 'error',
                'info' => lang('保存失败'),
                'result' => $result,
            ];
        }

        return json($data);
    }

    /**
     * 删除
     * 1 临时客户才能删除, 状态是 L, 并且区域是 临时区域LS
     * 2 系统默认 可删除检查
     * @return \think\response\Json
     */
    public function remove() {
        $eba_id = input('post.eba_id');
        if (empty($eba_id)) {
            return json([
                'state' => 'error',
                'info' => lang('请传递参数') . ' $eba_id',
            ]);
        }
        $eba = new \app\common\model\eba\Eba();
        // 微信微信删除条件
        $eba_info = $eba->where(['eba_id' => $eba_id])->select()->toArray();
        if (empty($eba_info)) {
            return json([
                'state' => 'error',
                'info' => lang('没有查询到将要删除的记录'),
            ]);
        }
        $config = Config::get('work_eba');
        if ($eba_info[0]['service_id'] != $config['temp_service_id']) {
            return json([
                'state' => 'error',
                'info' => lang('不能删除,所选客户已经不在临时区域了' . $eba_id),
            ]);
        }
        if ($eba_info[0]['state'] != $config['temp_eba_state']) {
            return json([
                'state' => 'error',
                'info' => lang('不能删除,所选客户已经不是临时客户' . $eba_id),
            ]);
        }

        // 检测 满足系统默认可删除条件
        $can_remove = EbaLogic::remove_check($eba_id);
        if ($can_remove['result'] == false) {
            return json([
                'state' => 'error',
                'info' => $can_remove['info'],
            ]);
        }
        $result = $eba->where(['eba_id' => $eba_id])->delete();
        if (empty($result)) {
            return json([
                'state' => 'error',
                'info' => lang('删除失败'),
            ]);
        } else {
            return json([
                'state' => 'success',
                'info' => lang('删除成功'),
            ]);
        }
    }

    /**
     * 从微信中下载名片并识别
     * @return \think\response\Json
     */
    public function read_card() {
        $img_id = input('post.img_id');
        if (empty($img_id)) {
            return json([
                'state' => 'error',
                'info' => lang('请传递名片在微信服务器上的id'),
            ]);
        }
        // 从微信服务器上下载图片
        $media = $this->agent->media;
        $temp_name = md5(time() + rand()) . '.jpg';
        $temp_path = $this->temp_path . $temp_name;
        $get_file_info = file_put_contents(
            $temp_path,
            $media->get($img_id)
        );

        while ($get_file_info == false) {
            sleep(5);
        }

        // 对接识别名片信息
        $card_info = OcrBcard::read($temp_path);
        if($card_info['state'] == 'success') {
            $card_info['temp_name'] = $temp_name;
        }
        return json($card_info);
    }

    // 与微信服务器后台通信的接口
    public function api() {
        $work = WeChat::agent('Eba')->server;
        echo $work->reply();

        return;
    }

}
