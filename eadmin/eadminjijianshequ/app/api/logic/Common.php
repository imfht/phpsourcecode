<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\api\error\CodeBase;
use app\api\error\Common as CommonError;
use app\common\logic\User as LogicMember;
use \Firebase\JWT\JWT;
use app\common\logic\Common as LogicCommon;
use Qiniu\json_decode;
use app\common\logic\File as LogicFile;


/**
 * 接口基础逻辑
 */
class Common extends ApiBase
{

    /**
     * 登录接口逻辑
     */
    public static function login($data = [])
    {

        $commonLogic = get_sington_object('commonLogic', LogicCommon::class);


        $result = $commonLogic->getDataInfo('upuserinfo', ['moble' => $data['username']]);

        if (empty($result)) {


            $ret['status'] = 'error';
            $ret['msg']    = '尚未登记该手机号';

            return [$ret];
        }

        unset($data['access_token']);

        if (!empty($data['safe'])) {
            if ($data['safe'] == 1) {

                $data['password'] = $pass = utf8RawUrlDecode(base64_decode($data['password']));

            }
            unset($data['safe']);
        }


        $validate = validate('User');

        $validate_result = $validate->scene('login')->check($data);

        if (!$validate_result) : return CommonError::$usernameOrPasswordEmpty; endif;

        $memberLogic = get_sington_object('memberLogic', LogicMember::class);

        begin:

        $member = $memberLogic->getMemberInfo(['username' => $data['username']]);

        // 若不存在用户则注册
        if (empty($member)) {
            $data['nickname'] = $result['username'];

            $register_result = static::register($data);

            if (!$register_result) : return CommonError::$registerFail; endif;

            goto begin;
        }

        if (md5($data['password'] . $member['salt']) !== $member['password']) : return CommonError::$passwordError; endif;

        $jwt_data = static::tokenSign($member);

        return [$jwt_data];
    }

    public static function register($data)
    {


        $salt             = generate_password(18);
        $data['salt']     = $salt;
        $data['password'] = md5($data['password'] . $salt);
        $data['mobile']   = $data['username'];
        $data['regtime']  = time();
        $data['usermail'] = $salt . '@qq.com';
        // $data['password']  = data_md5_key($data['password']);

        $memberLogic = get_sington_object('memberLogic', LogicMember::class);

        return $memberLogic->setInfo($data);
    }

    public static function tokenSign($member)
    {

        $key = API_KEY . JWT_KEY;

        $jwt_data = ['member_id' => $member['id'], 'nickname' => $member['nickname'], 'username' => $member['username'], 'regtime' => $member['regtime']];

        $token = [
            "iss"  => "EasySNS JWT",         // 签发者
            "iat"  => TIME_NOW,              // 签发时间
            "exp"  => TIME_NOW + 7200,   // 过期时间
            "aud"  => 'EasySNS',             // 接收方
            "sub"  => 'EasySNS',             // 面向的用户
            "data" => $jwt_data
        ];

        $jwt = JWT::encode($token, $key);

        $jwt_data['user_token'] = $jwt;

        return $jwt_data;
    }

    public static function upuserinfo($data)
    {
        $commonLogic = get_sington_object('commonLogic', LogicCommon::class);

        unset($data['access_token']);

        $result = $commonLogic->dataInsert('upuserinfo', $data);

        $ret['status'] = $result;


        $ret['info'] = $data;

        return [$ret];

    }

    public static function getarticle($data)
    {

        $commonLogic = get_sington_object('commonLogic', LogicCommon::class);

        $result = $commonLogic->getDataInfo('article', ['id' => $data['id']]);
        if (empty($result)) {


            $ret['status'] = 'error';
            $ret['msg']    = '尚未登记该手机号';

            return [$ret];
        } else {

            $result['content'] = html_entity_decode($result['content']);

            $ret['status'] = 'success';


            $ret['info'] = $result;

            return [$ret];
        }


    }

    public static function getuserinfo($data)
    {

        $commonLogic = get_sington_object('commonLogic', LogicCommon::class);

        $result = $commonLogic->getDataInfo('user', ['id' => $data['uid']]);
        if (empty($result)) {


            $ret['status'] = 'error';
            $ret['msg']    = 'ID错误';

            return [$ret];
        } else {


            $ret['status'] = 'success';


            $ret['info'] = $result;

            return [$ret];
        }


    }

    public static function uploadfile($data)
    {
        $fileLogic = get_sington_object('fileLogic', LogicFile::class);

        $result = $fileLogic->docfileUpload($data);

        return [$result];


    }

    public static function getipstr($data)
    {
        $type     = $data['limit'];
        $ext      = $data['ext'];
        $filename = $data['filename'];

        $num = $data['num'];

        if ($type == 1) {
            $value['str'] = getipstr($ext, $filename, $num, true, true);
        } else {
            $value['str'] = getipstr($ext, $filename, $num, true, false);
        }


        return [$value];


    }

    public static function checkweburl($data)
    {
        $type = $data['url'];

        $commonLogic = get_sington_object('commonLogic', LogicCommon::class);


        $result = $commonLogic->getDataInfo('domain', ['domain' => $type]);


        if (!empty($result)) {

        } else {
            $result['status']     = 0;
            $result['downstatus'] = 0;

            $commonLogic->dataInsert('domain', ['domain' => $type, 'status' => 0, 'downstatus' => 0], false);


        }


        $result['ver'] = parse_config_array('version_list');


        return [$result];


    }

    public static function wenjuanlist($data)
    {


        $page = $data['page'];

        $info = model('wjcate')->where(['status' => 1])->order("create_time desc")->page($page . ',10')->select();


        if (empty($info)) {


            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';

            return [$ret];
        } else {


            $ret['status'] = 'success';

            foreach ($info as $k => $v) {
                if ($v['cover_id'] == 0) {

                    $img = '/public/images/onimg.png';
                } else {
                    $img = get_picture_url($v['cover_id']);
                }
                $info[$k]['pic']         = 'http://www.imzaker.com/' . getbaseurl() . $img;
                $info[$k]['description'] = html_entity_decode($v['description']);
            }


            $ret['info'] = $info;

            return [$ret];
        }


    }

    public static function mywenjuanlist($data)
    {


        $page = $data['page'];

        $tidarr = model('wenjuantj')->where(['uid' => $data['uid']])->column('tid');

        $info = model('wjcate')->where(['status' => 1, 'id' => ['in', $tidarr]])->order("create_time desc")->page($page . ',10')->select();


        if (empty($info)) {


            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';

            return [$ret];
        } else {


            $ret['status'] = 'success';

            foreach ($info as $k => $v) {
                if ($v['cover_id'] == 0) {

                    $img = '/public/images/onimg.png';
                } else {
                    $img = get_picture_url($v['cover_id']);
                }
                $info[$k]['pic']         = 'http://www.imzaker.com/' . getbaseurl() . $img;
                $info[$k]['description'] = html_entity_decode($v['description']);

                $tmcount = model('wenjuan')->where(['tid' => $v['id']])->count();

                $tjcount = model('wenjuantj')->where(['tid' => $v['id'], 'uid' => $data['uid']])->count();

                if ($tmcount > 0) {
                    $info[$k]['bfb'] = $tjcount * 100 / $tmcount;
                } else {
                    $info[$k]['bfb'] = 0;
                }
                if ($tmcount == $tjcount) {
                    $info[$k]['jindu'] = 1;
                } else {
                    $info[$k]['jindu'] = 0;
                }

            }


            $ret['info'] = $info;

            return [$ret];
        }


    }

    public static function wjcontent($data)
    {


        $sidarr = model('wenjuantj')->where(['uid' => $data['uid'], 'tid' => $data['id']])->column('sid');


        $info = model('wenjuan')->where(['status' => 1, 'id' => ['not in', $sidarr], 'tid' => $data['id']])->order("sort desc")->limit(1)->select();


        if (empty($info)) {


            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';

            return [$ret];
        } else {


            $ret['status'] = 'success';


            $ret['info'] = $info[0];

            return [$ret];
        }


    }

    public static function tjwenjuan($data)
    {


        $commonLogic = get_sington_object('commonLogic', LogicCommon::class);

        $insertdata['sid']          = $data['sid'];
        $insertdata['status']       = 1;
        $insertdata['request_data'] = $data['request_data'];
        $insertdata['uid']          = $data['uid'];
        $insertdata['tid']          = $data['tid'];
        $commonLogic->dataInsert('wenjuantj', $insertdata, false);//第一步先入库

        $sidarr = model('wenjuantj')->where(['uid' => $data['uid'], 'tid' => $data['tid']])->column('sid');

        $info = model('wenjuan')->where(['status' => 1, 'id' => ['not in', $sidarr], 'tid' => $data['tid']])->order("sort desc")->limit(1)->select();

        if (empty($info)) {


            $ret['status'] = 'error';

            $ret['msg'] = '暂无数据';

            return [$ret];

        } else {

            $ret['status'] = 'success';

            $ret['info'] = $info[0];

            return [$ret];
        }


        return [$result];


    }
}
