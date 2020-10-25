<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-27 12:34:22
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-24 17:23:41
 */

namespace common\services\common;

use common\helpers\CacheHelper;
use common\helpers\FileHelper;
use common\helpers\ImageHelper;
use common\models\enums\MessageStatus;
use common\models\MessageNoticeLog;
use common\models\UserBloc;
use common\services\BaseService;
use diandi\admin\models\AuthAssignmentGroup;
use diandi\admin\models\Bloc;
use diandi\admin\models\BlocConfBaidu;
use diandi\admin\models\BlocConfEmail;
use diandi\admin\models\BlocConfSms;
use diandi\admin\models\BlocConfWechat;
use diandi\admin\models\BlocConfWechatpay;
use diandi\admin\models\BlocConfWxapp;
use diandi\admin\models\BlocStore;
use Yii;

/**
 * 全局服务
 *
 * @author chunchun <2192138785@qq.com>
 */
class GlobalsService extends BaseService
{
    // 集团id
    private $bloc_id = 1;
    // 子公司id
    private $store_id = 1;

    //模块表示
    private $addons = 'system';

    public function initId($bloc_id, $store_id, $addons)
    {
        $this->setbloc_id($bloc_id);
        $this->setStore_id($store_id);
        $this->setAddons($addons);
    }

    // 全局设置商家id
    public function setbloc_id($id)
    {
        $this->bloc_id = $id;
    }

    // 全局设置商家id
    public function getbloc_id()
    {
        $globalBloc = Yii::$app->cache->get('globalBloc');
        if (isset($globalBloc['bloc_id']) && !empty($globalBloc['bloc_id']) && Yii::$app->id == 'app-backend') {
            return  $globalBloc['bloc_id'];
        }

        return $this->bloc_id;
    }

    // 全局设置子公司id
    public function setStore_id($id)
    {
        $this->store_id = $id;
    }

    // 全局获取子公司id
    public function getStore_id()
    {
        $globalBloc = Yii::$app->cache->get('globalBloc');
        if (isset($globalBloc['store_id']) && !empty($globalBloc['store_id']) && Yii::$app->id == 'app-backend') {
            return  $globalBloc['store_id'];
        }
        return $this->store_id;
    }

    // 全局设置扩展
    public function setAddons($id)
    {
        $this->addons = $id;
    }

    // 全局获取扩展
    public function getAddons()
    {
        return $this->addons;
    }

    public function getBlocAll()
    {
        return  Bloc::find()->where(['status' => 1])->asArray()->all();
    }

    /**
     * 获取全局配置信息.
     *
     * @param int|null post
     *
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public function getConf($bloc_id = 0)
    {
        $logPath = Yii::getAlias('@runtime/config/getConf/'.date('ymd').'.log');

        $cacheKey = 'conf_'.$bloc_id;
        if (Yii::$app->cache->get($cacheKey)) {
            Yii::$app->params['conf'] = Yii::$app->cache->get($cacheKey);
                
            return Yii::$app->cache->get($cacheKey);
        }

        FileHelper::writeLog($logPath, '配置获取'.$bloc_id);

        if ($bloc_id) {
            // 微信支付配置
            $conf['wechatpay'] = BlocConfWechatpay::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            // 邮件配置
            $conf['email'] = BlocConfEmail::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            // 小程序配置
            $conf['wxapp'] = BlocConfWxapp::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            FileHelper::writeLog($logPath, '小程序配置sql'.Yii::$app->db->createCommand()->getRawSql());
            // 公众号配置
            $conf['wechat'] = BlocConfWechat::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            FileHelper::writeLog($logPath, '公众号配置sql'.Yii::$app->db->createCommand()->getRawSql());

            // 短信配置
            $conf['sms'] = BlocConfSms::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            // 百度ai-sdk
            $conf['baidu'] = BlocConfBaidu::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            FileHelper::writeLog($logPath, '配置内容'.$bloc_id.json_encode($conf));
        } else {
            // 获取默认的公司
            $bloc_id = Yii::$app->settings->get('Website', 'bloc_id');
            // 微信支付配置
            $conf['wechatpay'] = BlocConfWechatpay::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            // 邮件配置
            $conf['email'] = BlocConfEmail::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            // 小程序配置
            $conf['wxapp'] = BlocConfWxapp::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            // 短信配置
            $conf['sms'] = BlocConfSms::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            // 百度ai-sdk
            $conf['baidu'] = BlocConfBaidu::find()->where(['bloc_id' => $bloc_id])->asArray()->one();
            // 公众号配置
            $conf['wechat'] = BlocConfWechat::find()->where(['bloc_id' => $bloc_id])->asArray()->one();

        }

        // 都为空就使用系统默认的
        if (empty($conf['baidu'])) {
            $conf['baidu'] = Yii::$app->settings->getAllBySection('Baidu');
        }

        if (empty($conf['wechatpay'])) {
            $conf['wechatpay'] = Yii::$app->settings->getAllBySection('Wechatpay');
        }

        if (empty($conf['sms'])) {
            $conf['sms'] = Yii::$app->settings->getAllBySection('Sms');
        }

        if (empty($conf['wxapp'])) {
            $conf['wxapp'] = Yii::$app->settings->getAllBySection('Wxapp');
        }

        if (empty($conf['wechat'])) {
            $conf['wechat'] = Yii::$app->settings->getAllBySection('Wechat');
        }

        if (empty($conf['email'])) {
            $conf['email'] = Yii::$app->settings->getAllBySection('Email');
        }

        
        $cacheClass = new CacheHelper();
        $cacheClass->set($cacheKey, $conf);

        Yii::$app->params['conf'] = $conf;

        return $conf;
    }

    /**
     * 获取一个用户所有得公司.
     */
    public function getBlocByuserId($user_id)
    {
        $Bloc = new Bloc();
        $key = $user_id.'_blocs';
        if (Yii::$app->cache->get($key)) {
            Yii::$app->params['userBloc'] = Yii::$app->cache->get($key);

            return Yii::$app->cache->get($key);
        }

        $group = AuthAssignmentGroup::findAll(['user_id' => $user_id]);
        $where = [];
        $list = [];
        Yii::$app->params['userBloc'] = [];
        if (!in_array('总管理员', array_column($group, 'item_name'))) {
            $where = ['user_id' => $user_id];
            $UserBloc = new UserBloc();
            $bloc_ids = $UserBloc->find()->where($where)->with(['bloc', 'store'])->select(['bloc_id', 'store_id'])->asArray()->all();
            foreach ($bloc_ids as $key => $value) {
                $value['bloc']['store'][] = $value['store'];
                $list[$value['bloc_id']] = $value['bloc'];
            }
            Yii::$app->params['userBloc'] = array_values($list);
        } else {
            $list = $Bloc->find()
            ->with(['store'])
            ->asArray()
            ->all();
            Yii::$app->params['userBloc'] = $list;
        }
        
        $cacheClass = new CacheHelper();
        $cacheClass->set($key, $list);

        return $list;
    }

    /**
     * 获取公司与商户详细信息.
     *
     * @param int|null post
     *
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public function getStoreDetail($store_id)
    {
        $key = 'StoreDetail_'.$store_id;
        if (Yii::$app->cache->get($key)) {
            return Yii::$app->cache->get($key);
        } else {
            $BlocStore = new BlocStore();
            $store = $BlocStore->find()->where(['store_id' => $store_id])->with(['bloc'])->asArray()->one();
            $info = [];
            if ($store) {
                $store['logo'] = ImageHelper::tomedia($store['logo']);
                $extra = unserialize($store['extra']);
                $extra = $extra ? $extra : [];
                $info = array_merge($store, $extra);
            }
            $cacheClass = new CacheHelper();
            $cacheClass->set($key, $info);

            return $info;
        }
    }
    
    // 获取一个公司的所有子公司
    public function getBlocChild($bloc_id)
    {
        return Bloc::find()->where(['pid'=>$bloc_id])->asArray()->all();
    }

    /**
     * 获取全局系统消息.
     */
    public function getMessage($bloc_id = 0)
    {
        $cacheKey = 'message_'.$bloc_id;
        if (Yii::$app->cache->get($cacheKey)) {
            Yii::$app->params['message'] = Yii::$app->cache->get($cacheKey);

            return;
        }
        $MessageNoticeLog = new MessageNoticeLog();
        $list = $MessageNoticeLog->find()->asArray()->all();
        $status = MessageStatus::listData();
        foreach ($list as $key => &$value) {
            $value['type'] = $status[$value['type']];
        }

        $message = [
            'list' => $list,
            'total' => count($list),
        ];
        $cacheClass = new CacheHelper();
        $cacheClass->set($cacheKey, $message);

        return $message;
    }
}
