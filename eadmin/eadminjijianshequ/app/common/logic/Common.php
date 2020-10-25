<?php

namespace app\common\logic;

use app\common\model\ModelBase;

/**
 * 共用逻辑
 */
class Common extends ModelBase
{


    // 初始化
    protected function _initialize()
    {


    }

    public function getuserlistpage($tag)
    {
        $length   = empty($tag['length']) ? 0 : $tag['length'];
        $grades   = empty($tag['grades']) ? false : $tag['grades'];
        $status   = empty($tag['status']) ? 1 : $tag['status'];
        $leaderid = empty($tag['leaderid']) ? false : $tag['leaderid'];
        $inside   = empty($tag['inside']) ? false : $tag['inside'];
        $rz       = empty($tag['rz']) ? false : $tag['rz'];
        $focus    = empty($tag['focus']) ? false : $tag['focus'];
        $isfocus  = empty($tag['isfocus']) ? false : $tag['isfocus'];


        if ($grades) {

            $where1['grades'] = $grades;

        }


        $where1['status'] = $status;


        if ($leaderid) {

            $where1['leader_id'] = $leaderid;
        }
        if ($inside) {

            $where1['inside'] = 1;
        }


        $where['status'] = $status;
        if ($rz) {


            $uidarr       = $this->setname('rzuser')->getDataColumn($where1, 'id');
            $where['uid'] = $uidarr;

            $artlist = $this->setname('rzuser')->getDataList($where, true, 'create_time desc', $length);


        } else {
            $artlist = $this->setname('user')->getDataList($where1, true, 'create_time desc', $length);
        }

        return $artlist['page'];


    }

    public function getuserlist($tag)
    {
        $limit    = empty($tag['limit']) ? false : $tag['limit'];
        $length   = empty($tag['length']) ? 0 : $tag['length'];
        $order    = empty($tag['order']) ? 'create_time desc' : $tag['order'];
        $grades   = empty($tag['grades']) ? false : $tag['grades'];
        $status   = empty($tag['status']) ? false : $tag['status'];
        $leaderid = empty($tag['leaderid']) ? false : $tag['leaderid'];
        $inside   = empty($tag['inside']) ? false : $tag['inside'];
        $rz       = empty($tag['rz']) ? false : $tag['rz'];
        $focus    = empty($tag['focus']) ? false : $tag['focus'];
        $isfocus  = empty($tag['isfocus']) ? false : $tag['isfocus'];


        if ($grades) {

            $where1['grades'] = $grades;

        }


        $where1['status'] = $status;


        if ($leaderid) {

            $where1['leader_id'] = $leaderid;
        }
        if ($inside) {

            $where1['inside'] = 1;
        }


        $where['status'] = $status;
        if ($rz) {


            $uidarr       = $this->setname('rzuser')->getDataColumn($where1, 'id');
            $where['uid'] = $uidarr;


            if ($limit > 0) {
                $artlist = $this->setname('rzuser')->getDataList($where, true, $order, false, '', '', $limit);

                $artlist['data'] = $artlist;
            } else {
                $artlist = $this->setname('rzuser')->getDataList($where, true, $order, $length);
            }

        } else {
            if ($limit > 0) {
                $artlist = $this->setname('user')->getDataList($where1, true, $order, false, '', '', $limit);

                $artlist['data'] = $artlist;
            } else {
                $artlist = $this->setname('user')->getDataList($where1, true, $order, $length);
            }

        }

        if ($rz) {

            foreach ($artlist['data'] as $key => $v) {

                $nowuserinfo = self::$datalogic->setname('user')->getDataInfo(['id' => $v['uid']]);

                $nowuserinfo['statusdes'] = $rzinfo['statusdes'];
                $nowuserinfo['hasrz']     = 1;
                if ($rzinfo['type'] == 1) {

                    $nowuserinfo['icon'] = 'icon-myvip';
                    $nowuserinfo['type'] = '个人认证';
                } else {
                    $nowuserinfo['icon'] = 'icon-myvip i-ve';
                    $nowuserinfo['type'] = '企业认证';
                }

                $artlist['data'][$key] = $nowuserinfo;


            }


        } else {
            foreach ($artlist['data'] as $key => $v) {


                $rzinfo = self::$datalogic->setname('rzuser')->getDataInfo(['uid' => $v['id']]);
                if ($rzinfo) {
                    if ($rzinfo['status'] == 1) {
                        $artlist['data'][$key]['statusdes'] = $rzinfo['statusdes'];
                        $artlist['data'][$key]['hasrz']     = 1;
                        if ($rzinfo['type'] == 1) {

                            $artlist['data'][$key]['icon'] = 'icon-myvip';
                            $artlist['data'][$key]['type'] = '个人认证';
                        } else {
                            $artlist['data'][$key]['icon'] = 'icon-myvip i-ve';
                            $artlist['data'][$key]['type'] = '企业认证';
                        }

                    }

                } else {
                    $artlist['data'][$key]['hasrz'] = 0;
                }


            }

        }


        return $artlist['data'];
    }

    public function gethtpage($tag)
    {
        $length          = empty($tag['length']) ? 0 : $tag['length'];
        $cover           = empty($tag['cover']) ? false : $tag['cover'];
        $choice          = empty($tag['choice']) ? false : $tag['choice'];
        $cateid          = empty($tag['cateid']) ? false : $tag['cateid'];
        $uid             = empty($tag['uid']) ? false : $tag['uid'];
        $focus           = empty($tag['focus']) ? false : $tag['focus'];
        $where['status'] = 1;
        if ($cateid) {

            $where['pid'] = $cateid;

        }
        if ($uid) {

            $where['uid'] = $uid;
        }

        if ($choice) {

            $where['choice'] = 1;
        }
        if ($cover) {

            $where['cover_id|>'] = 0;
        }

        $artlist = $this->setname('group')->getDataList($where, true, 'create_time desc', $length);


        return $artlist['page'];


    }

    public function gethtlist($tag)
    {
        $limit           = empty($tag['limit']) ? false : $tag['limit'];
        $length          = empty($tag['length']) ? 0 : $tag['length'];
        $order           = empty($tag['order']) ? 'create_time desc' : $tag['order'];
        $cover           = empty($tag['cover']) ? false : $tag['cover'];
        $choice          = empty($tag['choice']) ? false : $tag['choice'];
        $cateid          = empty($tag['cateid']) ? false : $tag['cateid'];
        $uid             = empty($tag['uid']) ? false : $tag['uid'];
        $focus           = empty($tag['focus']) ? false : $tag['focus'];
        $where['status'] = 1;
        if ($cateid) {


            $where['pid'] = $map[5];

        }
        if ($uid) {

            $where['uid'] = $uid;
        }

        if ($choice) {

            $where['choice'] = 1;
        }
        if ($cover) {

            $where['cover_id|>'] = 0;
        }


        if ($limit > 0) {
            $artlist         = $this->setname('group')->getDataList($where, true, $order, false, '', '', $limit);
            $artlist['data'] = $artlist;
        } else {
            $artlist = $this->setname('group')->getDataList($where, true, $order, $length);
        }

        return $artlist['data'];
    }

    public function gettopiclist($tag)
    {

        $limit  = empty($tag['limit']) ? false : $tag['limit'];
        $length = empty($tag['length']) ? 0 : $tag['length'];
        $order  = empty($tag['order']) ? 'settop desc,create_time desc' : $tag['order'];
        $settop = empty($tag['settop']) ? false : $tag['settop'];
        $choice = empty($tag['choice']) ? false : $tag['choice'];
        $cateid = empty($tag['cateid']) ? false : $tag['cateid'];
        $uid    = empty($tag['uid']) ? false : $tag['uid'];
        $focus  = empty($tag['focus']) ? false : $tag['focus'];


        $where['status'] = 1;
        if ($cateid) {

            $htinfo             = db('group')->where(['id' => $cateid])->getRow();
            $where['gidtext|~'] = $htinfo['name'];

        }
        if ($uid) {

            $where['uid'] = $uid;
        }

        if ($choice) {

            $where['choice'] = 1;
        }
        if ($settop) {

            $where['settop'] = 1;
        }


        if ($limit > 0) {
            $artlist         = $this->setname('topic')->getDataList($where, true, $order, false, '', '', $limit);
            $artlist['data'] = $artlist;
        } else {
            $artlist = $this->setname('topic')->getDataList($where, true, $order, $length);
        }


        foreach ($artlist['data'] as $key => $vo) {
            $rzuserinfo                        = db('rzuser')->where(['uid' => $vo['uid']])->getRow();
            $userinfo                          = db('user')->where(['id' => $vo['uid']])->getRow();
            $artlist['data'][$key]['userhead'] = $userinfo['userhead'];
            $artlist['data'][$key]['nickname'] = $userinfo['nickname'];

            if ($rzuserinfo['status'] && $rzuserinfo['status'] == 1) {

                if ($rzuserinfo['type'] == 1) {
                    $artlist['data'][$key]['rzicon'] = 'icon-myvip';
                } else {
                    $artlist['data'][$key]['rzicon'] = 'icon-myvip i-ve';
                }

            }

            $focuscount = db('user_focus')->where(['sid' => $vo['id'], 'type' => 1])->count();

            $cinfo = db('comment')->where(['fid' => $vo['id'], 'pid' => 0])->order('create_time desc')->limit(1)->getList();


            if ($cinfo) {

                $lsnickname = getusernamebyid($cinfo[0]['uid']);

                $artlist['data'][$key]['replystr']  = '回复&nbsp;' . friendlyDate($cinfo[0]['create_time']) . '&nbsp;(' . $focuscount . '人关注)';
                $artlist['data'][$key]['replyuser'] = $lsnickname;
                $artlist['data'][$key]['replyuid']  = $cinfo[0]['uid'];
            } else {
                $artlist['data'][$key]['replystr'] = '发布&nbsp;' . friendlyDate($vo['create_time']) . '&nbsp;(' . $focuscount . '人关注)';
            }

            if ($vo['gidtext']) {

                $artlist['data'][$key]['htlist'] = explode(',', $vo['gidtext']);

            }


            if (!$vo['description']) {

                $artlist['data'][$key]['description'] = msubstr(clearHtml(htmlspecialchars_decode($vo['content'])), 0, 60);
            }
            $arr                                  = getcontentimage(htmlspecialchars_decode($vo['content']), false)[1];
            $artlist['data'][$key]['imagescount'] = count($arr);

            if (count($arr) > 3) {
                $arr = array_slice($arr, 0, 3);
            }
            $artlist['data'][$key]['imagesarr'] = $arr;

        }

        return $artlist['data'];
    }

    public function gettopicpage($tag)
    {
        $length = empty($tag['length']) ? 0 : $tag['length'];
        $settop = empty($tag['settop']) ? false : $tag['settop'];
        $choice = empty($tag['choice']) ? false : $tag['choice'];
        $cateid = empty($tag['cateid']) ? false : $tag['cateid'];
        $uid    = empty($tag['uid']) ? false : $tag['uid'];
        $focus  = empty($tag['focus']) ? false : $tag['focus'];


        $where['status'] = 1;
        if ($cateid) {

            $htinfo             = db('group')->where(['id' => $cateid])->getRow();
            $where['gidtext|~'] = $htinfo['name'];

        }
        if ($uid) {

            $where['uid'] = $uid;
        }

        if ($choice) {

            $where['choice'] = 1;
        }
        if ($settop) {

            $where['settop'] = 1;
        }


        $artlist = $this->setname('topic')->getDataList($where, true, 'create_time desc', $length);

        return $artlist['page'];


    }


}
