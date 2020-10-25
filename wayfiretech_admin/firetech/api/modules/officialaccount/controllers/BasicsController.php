<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-09 01:32:28
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-16 20:54:27
 */

namespace api\modules\officialaccount\controllers;

use api\controllers\AController;
use common\helpers\ArrayHelper;
use common\helpers\FileHelper;
use common\helpers\ResultHelper;
use common\models\DdCorePaylog;
use Yii;
use yii\helpers\Json;

/**
 * Default controller for the `wechat` module.
 */
class BasicsController extends AController
{
    protected $authOptional = ['signup', 'auth', 'notify', 'userinfo'];

    public $modelClass = 'api\modules\officialaccount\models\DdWechatFans';

    /**
     * @SWG\Post(path="/officialaccount/basics/signup",
     *     tags={"微信基础接口"},
     *     summary="微信接口测试",
     *     @SWG\Response(
     *         response = 200,
     *         description = "微信接口测试"
     *     ),
     *     @SWG\Parameter(
     *      in="query",
     *      name="code",
     *      type="string",
     *      description="微信授权code",
     *      required=true,
     *    ),
     *    @SWG\Parameter(
     *      name="user_id",
     *      type="string",
     *      in="formData",
     *      description="用户id",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="avatarUrl",
     *      type="string",
     *      in="formData",
     *      description="头像",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="nickname",
     *      type="string",
     *      in="formData",
     *      description="昵称",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="gender",
     *      type="string",
     *      in="formData",
     *      description="性别",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="country",
     *      type="string",
     *      in="formData",
     *      description="国家",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="city",
     *      type="string",
     *      in="formData",
     *      description="城市",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="province",
     *      type="string",
     *      in="formData",
     *      description="省份",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="openid",
     *      type="string",
     *      in="formData",
     *      description="openId",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="unionid",
     *      type="string",
     *      in="formData",
     *      description="unionId",
     *      required=false
     *    ),
     *
     * )
     */
    public function actionSignup()
    {
        global $_GPC;
        $users = $_GPC;

        $code = $users['code'];

        unset($users['code']);
        $logPath = Yii::getAlias('@runtime/wechat/signup/'.date('ymd').'.log');

        FileHelper::writeLog($logPath, '登录日志：请求code的用户数据'.json_encode($users));

        $wechat = Yii::$app->wechat->getApp();

        if (empty($users['openid'])) { // 当openid有值的时候为app登录
            $user = $wechat->auth->session($code);
            FileHelper::writeLog($logPath, '登录日志：获取信息'.json_encode($user));

            if (key_exists('errcode', $user)) {
                FileHelper::writeLog($logPath, '登录日志：请求错误信息'.json_encode($user));

                return ResultHelper::json(401, $user['errmsg'], []);
            }
            $users['openid'] = $user['openid'];
            $users['unionid'] = key_exists('unionid', $user) ? $user['unionid'] : '';
        }
        // 查询该openID是否存在
        $res = Yii::$app->fans->signup($users);
        FileHelper::writeLog($logPath, '登录成功：'.json_encode($res));

        return ResultHelper::json(200, '登录成功', $res);
    }

    /**
     * @SWG\Post(path="/officialaccount/basics/auth",
     *     tags={"微信授权"},
     *     summary="微信授权",
     *     @SWG\Response(
     *         response = 200,
     *         description = "微信授权"
     *     ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="redirect_uri",
     *      type="string",
     *      description="回调根域名，由于VUE的路径问题，这里传递根域名",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="route",
     *      type="string",
     *      description="回调路由",
     *      required=true,
     *    )
     * )
     */
    public function actionAuth()
    {
        global $_GPC;

        $redirect_uri = $_GPC['redirect_uri'];
        $route = $_GPC['route'];

        $wechat = Yii::$app->wechat->app;
        $response = $wechat->oauth->scopes(['snsapi_userinfo'])
        ->redirect($redirect_uri);

        return ResultHelper::json(200, '授权成功', $response);
    }

    /**
     * @SWG\Get(path="/officialaccount/basics/userinfo",
     *     tags={"获取微信用户信息"},
     *     summary="获取用户信息",
     *     @SWG\Response(
     *         response = 200,
     *         description = "获取用户信息"
     *     ),
     *     @SWG\Parameter(
     *      in="query",
     *      name="code",
     *      type="string",
     *      description="获取用户信息的code",
     *      required=true,
     *    )
     * )
     */
    public function actionUserinfo()
    {
        global $_GPC;
        $logPath = Yii::getAlias('@runtime/officialaccount/signup/'.date('ymd').'.log');
        if (empty($_GPC['code'])) {
            return ResultHelper::json(400, 'code 参数不能为空', []);
        }
        $code = $_GPC['code'];
        $wechat = Yii::$app->wechat->app;
        $user = $wechat->oauth->user()->toArray();
        if (empty($user)) {
            return ResultHelper::json(400, '用户信息获取失败', $user);
        } else {
            FileHelper::writeLog($logPath, '用户信息获取成功');
            // "id": "ogEDnjlsqUJJbT1KNB36QuVVyji8",
            // "name": "王春生",
            // "nickname": "王春生",
            // "avatar": "http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLUDricS0ibwcYGtp3LU9uvHREQtobWk95QLf4IHbzTCyrkniacDIrYXHhwiaQe7UXYqeNjNlZcEW7sfQ/132",
            // "email": null,
            // "original": {
            // "openid": "ogEDnjlsqUJJbT1KNB36QuVVyji8",
            // "nickname": "王春生",
            // "sex": 1,
            // "language": "zh_CN",
            // "city": "西安",
            // "province": "陕西",
            // "country": "中国",
            // "headimgurl": "http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLUDricS0ibwcYGtp3LU9uvHREQtobWk95QLf4IHbzTCyrkniacDIrYXHhwiaQe7UXYqeNjNlZcEW7sfQ/132",
            // "privilege": []
            // },
            // "token": "37_aw6o8m4VAFCLsKFU2fUN00h9Ao3jVs6ZeOdagfG9n05cbgo0c8PeAZppGt1KIZ66weFy_hSO4RSQFvpSa5RW4A",
            // "provider": "WeChat"
            $user['avatarUrl'] = $user['avatar'];
            $user['gender'] = !empty($user['gender']) ? $user['gender'] : 0;
            $user['openid'] = $user['original']['openid'];
            $user['country'] = $user['original']['country'];
            $user['city'] = $user['original']['city'];
            $user['province'] = $user['original']['province'];

            $Res = Yii::$app->fans->signup($user);

            return ResultHelper::json(200, '用户信息获取成功', $Res);
        }
    }

    /**
     * @SWG\Post(path="/officialaccount/basics/payparameters",
     *     tags={"微信支付"},
     *     summary="获取支付参数",
     *     @SWG\Response(
     *         response = 200,
     *         description = "获取支付参数"
     *     ),
     *     @SWG\Parameter(
     *      in="query",
     *      name="access-token",
     *      type="string",
     *      description="access-token",
     *      required=true,
     *    ),
     *    @SWG\Parameter(
     *      name="openid",
     *      type="string",
     *      in="formData",
     *      description="openid",
     *      required=false
     *    ),
     *    @SWG\Parameter(
     *      name="trade_type",
     *      type="string",
     *      in="formData",
     *      default="JSAPI",
     *      enum={"JSAPI","NATIVE","APP","MWEB","MICROPAY"},
     *      description="支付类型:JSAPI-js支付/NATIVE-Native支付/APP--app支付/MWEB--h5支付/MICROPAY--付款码支付",
     *      required=true
     *    ),
     *   @SWG\Parameter(
     *      name="body",
     *      type="string",
     *      in="formData",
     *      description="订单名称",
     *      required=true
     *    ),
     *    @SWG\Parameter(
     *      name="out_trade_no",
     *      type="string",
     *      in="formData",
     *      description="订单编号",
     *      required=true
     *    ),
     *    @SWG\Parameter(
     *      name="total_fee",
     *      type="string",
     *      in="formData",
     *      description="支付总金额",
     *      required=true
     *    ),
     * )
     */
    public function actionPayparameters()
    {
        global $_GPC;

        $bloc_id = Yii::$app->params['bloc_id'];
        $store_id = Yii::$app->params['store_id'];
        $data = Yii::$app->request->post();
        // 生成订单
        $domain = Yii::$app->request->hostInfo;
        $orderData = [
            'openid' => $data['openid'],
            'spbill_create_ip' => Yii::$app->request->userIP,
            'fee_type' => 'CNY',
            'body' => $data['body'], // 内容
            'out_trade_no' => $data['out_trade_no'], // 订单号
            'total_fee' => $data['total_fee'] * 100,
            'trade_type' => $data['trade_type'], //支付类型
            'notify_url' => Yii::$app->params['wechatPaymentConfig']['notify_url'], // 回调地址
            // 'open_id' => 'okFAZ0-',  //JS支付必填
            // 'auth_code' => 'ojPztwJ5bRWRt_Ipg',  刷卡支付必填
        ];
        $logPath = Yii::getAlias('@runtime/wechat/payparameters'.date('ymd').'.log');
        FileHelper::writeLog($logPath, '订单数据'.json_encode(ArrayHelper::toArray($orderData)));

        // 生成支付配置
        $payment = Yii::$app->wechat->payment;
        // return $payment->order;
        $result = $payment->order->unify($orderData);
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $prepayId = $result['prepay_id'];
            $config = $payment->jssdk->sdkConfig($prepayId);

            return ResultHelper::json(200, '支付参数获取成功', $config);
        } else {
            return ResultHelper::json(401, $result['err_code_des'], $result);
        }
    }

    /**
     * 支付回调.
     */
    public function actionNotify()
    {
        $logPath = Yii::getAlias('@runtime/wechat/notify/'.date('ymd').'.log');
        FileHelper::writeLog($logPath, '开始回调'.json_encode(Yii::$app->wechat->payment));

        $input = file_get_contents('php://input');

        FileHelper::writeLog($logPath, '开始回调回去到的内容'.json_encode($input));

        $response = Yii::$app->wechat->payment->handlePaidNotify(function ($message, $fail) {
            $logPath = Yii::getAlias('@runtime/wechat/notify/'.date('ymd').'.log');

            FileHelper::writeLog($logPath, Json::encode(ArrayHelper::toArray($message)));
            /////////////  建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            // return_code 表示通信状态，不代表支付状态
            if ($message['return_code'] === 'SUCCESS') {
                FileHelper::writeLog($logPath, '成功回调'.$message['out_trade_no']);
                $orderInfo = DdCorePaylog::findOne(['uniontid' => $message['out_trade_no']]);
                FileHelper::writeLog($logPath, '订单信息'.json_encode($orderInfo));

                $module = $orderInfo['module'];

                FileHelper::writeLog($logPath, '下单模块'.$module);

                $notify = Yii::$app->getModule($module)->Notify($message);

                FileHelper::writeLog($logPath, '下单模块处理数据结构'.json_decode($notify));

                if ($notify) {
                    return true;
                }
            }

            return $fail('处理失败，请稍后再通知我');
        });
        FileHelper::writeLog($logPath, '回调完毕'.json_encode($response));

        return $response;
    }
}
