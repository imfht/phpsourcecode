<?php

/**
 * 商品管理
 */

namespace app\admin\controller;
use think\facade\View;
use think\facade\Db;
use think\facade\Lang;
use GatewayClient\Gateway;
/**
 * ============================================================================
 * DSKMS多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 控制器
 */
class InstantMessage extends AdminControl {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'admin/lang/' . config('lang.default_lang') . '/instant_message.lang.php');
    }

    /**
     * 商品管理
     */
    public function index() {
        $instant_message_model = model('instant_message');

        /**
         * 查询条件
         */
        $condition = array();

        $instant_message_verify = input('param.instant_message_verify');
        if (in_array($instant_message_verify, array('0', '1', '2'))) {
            $condition[]=array('instant_message_verify','=',$instant_message_verify);
        }

        $instant_message_list = $instant_message_model->getInstantMessageList($condition, 10);

        View::assign('instant_message_list', $instant_message_list);
        View::assign('show_page', $instant_message_model->page_info->render());



        View::assign('search', $condition);

        View::assign('instant_message_url',config('ds_config.instant_message_gateway_url'));
        $this->setAdminCurItem('index');
        return View::fetch();
    }

    public function join(){
      $msg='';
        $client_id=input('param.client_id');
        if(!config('ds_config.instant_message_register_url')){
            ds_json_encode(10001, lang('instant_message_gateway_url_empty'));
        }
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        try{
        Gateway::$registerAddress = config('ds_config.instant_message_register_url');
        // client_id与uid绑定
        Gateway::bindUid($client_id, '-1');
        }catch(\Exception $e){
          ds_json_encode(10001, $e->getMessage());
        }
        ds_json_encode(10000, $msg);
    }
    /**
     * 删除商品
     */
    public function del() {
        $instant_message_id = input('param.instant_message_id');
        $instant_message_id_array = ds_delete_param($instant_message_id);
        if ($instant_message_id_array == FALSE) {
            ds_json_encode('10001', lang('ds_common_op_fail'));
        }
        $condition = array();
        $condition[] = array('instant_message_id','in', $instant_message_id_array);
        model('instant_message')->delInstantMessage($condition);
        $this->log(lang('ds_del') . lang('instant_message') . ' ID:' . implode('、', $instant_message_id_array), 1);
        ds_json_encode('10000', lang('ds_common_op_succ'));
    }

    /**
     * 审核商品
     */
    public function view() {
        $instant_message_id_array = ds_delete_param(input('param.instant_message_id'));
        if ($instant_message_id_array == FALSE) {
            ds_json_encode(10001, lang('param_error'));
        }
        $instant_message_model = model('instant_message');
        $condition = array();
        $condition[] = array('instant_message_verify','=',0);
        $condition[] = array('instant_message_id','in',$instant_message_id_array);
        $instant_message_list = $instant_message_model->getInstantMessageList($condition);
        if (!$instant_message_list) {
            ds_json_encode(10001, lang('message_empty'));
        }


        if (intval(input('param.verify_state')) == 0) {
            $condition = array();
            $condition[] = array('instant_message_verify','=',0);
            $condition[] = array('instant_message_id','in',$instant_message_id_array);
            $instant_message_model->editInstantMessage(array('instant_message_verify' => 2), $condition);
        } else {
            foreach ($instant_message_list as $instant_message_info) {
                Db::startTrans();
                try {
                    //立即发送
                    $instant_message_info['instant_message_from_avatar']= get_member_avatar_for_id($instant_message_info['instant_message_from_id']);
                    $res = $instant_message_model->sendInstantMessage($instant_message_info);
                    if (!$res['code']) {
                        throw new \think\Exception($res['msg'], 10006);
                    }
                } catch (\Exception $ex) {
                    Db::rollback();
                    ds_json_encode(10001, $e->getMessage());
                }
                Db::commit();
            }
        }

        $this->log(lang('ds_verify') . lang('instant_message') . ' ID:' . implode('、', $instant_message_id_array), 1);
        ds_json_encode(10000, lang('ds_common_op_succ'));
    }
    /*
     * 直播设置
     */
    public function setting() {
        $config_model = model('config');
        if (!request()->isPost()) {
            $list_config = rkcache('config', true);
            View::assign('list_config', $list_config);
            $this->setAdminCurItem('setting');
            return View::fetch();
        } else {
            $update_array=array();
            $update_array['instant_message_gateway_url'] = input('param.instant_message_gateway_url');
            $update_array['instant_message_register_url'] = input('param.instant_message_register_url');
            $update_array['instant_message_verify'] = input('param.instant_message_verify');
            $result = $config_model->editConfig($update_array);
            if ($result) {
                dkcache('config');
                $this->log(lang('ds_setting') . lang('instant_message'), 1);
                $this->success(lang('ds_common_save_succ'));
            } else {
                $this->log(lang('ds_setting') . lang('instant_message'), 0);
            }
        }
    }
    /**
     * 获取卖家栏目列表,针对控制器下的栏目
     */
    protected function getAdminItemList() {
        $menu_array = array(
            array(
                'name' => 'index',
                'text' => lang('ds_list'),
                'url' => url('InstantMessage/index')
            ),
            array(
                'name' => 'setting',
                'text' => lang('ds_setting'),
                'url' => url('InstantMessage/setting')
            ),
        );
        return $menu_array;
    }

}

?>
