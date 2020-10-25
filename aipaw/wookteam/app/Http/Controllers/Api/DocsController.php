<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Module\Base;
use App\Module\Docs;
use App\Module\Users;
use App\Tasks\PushTask;
use Cache;
use DB;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Request;

/**
 * @apiDefine docs
 *
 * 知识库
 */
class DocsController extends Controller
{
    public function __invoke($method, $action = '')
    {
        $app = $method ? $method : 'main';
        if ($action) {
            $app .= "__" . $action;
        }
        return (method_exists($this, $app)) ? $this->$app() : Base::ajaxError("404 not found (" . str_replace("__", "/", $app) . ").");
    }

    /**
     * 知识库列表
     *
     * @apiParam {Number} [page]                当前页，默认:1
     * @apiParam {Number} [pagesize]            每页显示数量，默认:20，最大:100
     */
    public function book__lists()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $lists = DB::table('docs_book')
            ->where('username', $user['username'])
            ->orWhere('role_edit', 'reg')
            ->orWhere('role_look', 'reg')
            ->orWhere(function ($query) use ($user) {
                $query->where('role_edit', 'private')->where('username', $user['username']);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('role_edit', 'member')->whereIn('id', function ($query2) use ($user) {
                    $query2->select('bookid')
                        ->from('docs_users')
                        ->where('username', $user['username'])
                        ->whereRaw(env('DB_PREFIX') . 'docs_book.id = bookid');
                });
            })
            ->orderByDesc('id')
            ->paginate(Base::getPaginate(100, 20));
        $lists = Base::getPageList($lists);
        if ($lists['total'] == 0) {
            return Base::retError('暂无知识库', $lists);
        }
        return Base::retSuccess('success', $lists);
    }

    /**
     * 添加/修改知识库
     *
     * @apiParam {Number} id                知识库数据ID
     * @apiParam {String} title             知识库名称
     */
    public function book__add()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Request::input('id'));
        $title = trim(Request::input('title'));
        if ($id > 0) {
            $role = Docs::checkRole($id, 'edit');
            if (Base::isError($role)) {
                return $role;
            }
        }
        if (mb_strlen($title) < 2 || mb_strlen($title) > 100) {
            return Base::retError('标题限制2-100个字！');
        }
        if ($id > 0) {
            // 修改
            $row = Base::DBC2A(DB::table('docs_book')->where('id', $id)->first());
            if (empty($row)) {
                return Base::retError('知识库不存在或已被删除！');
            }
            $data = [
                'title' => $title,
            ];
            DB::table('docs_book')->where('id', $id)->update($data);
            return Base::retSuccess('修改成功！', $data);
        } else {
            // 添加
            $data = [
                'username' => $user['username'],
                'title' => $title,
                'indate' => Base::time(),
            ];
            $id = DB::table('docs_book')->insertGetId($data);
            if (empty($id)) {
                return Base::retError('系统繁忙，请稍后再试！');
            }
            $data['id'] = $id;
            return Base::retSuccess('添加成功！', $data);
        }
    }

    /**
     * 设置知识库
     *
     * @apiParam {Number} id                知识库数据ID
     * @apiParam {String} role_edit
     * @apiParam {String} role_view
     */
    public function book__setting()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Request::input('id'));
        $type = trim(Request::input('type'));
        $role = Docs::checkRole($id, 'edit');
        if (Base::isError($role) && $role['data'] < 0) {
            return $role;
        }
        $row = Base::DBC2A(DB::table('docs_book')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('知识库不存在或已被删除！');
        }
        $setting = Base::string2array($row['setting']);
        if ($type == 'save') {
            if (Base::isError($role)) {
                return $role;
            }
            foreach (Request::input() AS $key => $value) {
                if (in_array($key, ['role_edit', 'role_look', 'role_view'])) {
                    $setting[$key] = $value;
                }
            }
            DB::table('docs_book')->where('id', $id)->update([
                'role_edit' => $setting['role_edit'],
                'role_look' => $setting['role_look'],
                'role_view' => $setting['role_view'],
                'setting' => Base::array2string($setting),
            ]);
        }
        return Base::retSuccess($type == 'save' ? '修改成功！' : 'success', $setting ?: json_decode('{}'));
    }

    /**
     * 删除知识库
     *
     * @apiParam {Number} id                知识库数据ID
     */
    public function book__delete()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Request::input('id'));
        $row = Base::DBC2A(DB::table('docs_book')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('知识库不存在或已被删除！');
        }
        if ($row['username'] != $user['username']) {
            return Base::retError('此操作仅限知识库负责人！');
        }
        DB::table('docs_book')->where('id', $id)->delete();
        DB::table('docs_section')->where('bookid', $id)->delete();
        DB::table('docs_content')->where('bookid', $id)->delete();
        return Base::retSuccess('删除成功！');
    }

    /**
     * 成员-列表
     *
     * @apiParam {Number} id            知识库数据ID
     * @apiParam {Number} [page]        当前页，默认:1
     * @apiParam {Number} [pagesize]    每页显示数量，默认:20，最大:100
     */
    public function users__lists()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Request::input('id'));
        $role = Docs::checkRole($id, 'edit');
        if (Base::isError($role)) {
            return $role;
        }
        $row = Base::DBC2A(DB::table('docs_book')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('知识库不存在或已被删除！');
        }
        //
        $lists = DB::table('docs_book')
            ->join('docs_users', 'docs_book.id', '=', 'docs_users.bookid')
            ->select(['docs_book.title', 'docs_users.*'])
            ->where([
                ['docs_book.id', $id],
            ])
            ->orderByDesc('docs_users.id')->paginate(Base::getPaginate(100, 20));
        $lists = Base::getPageList($lists);
        if ($lists['total'] == 0) {
            return Base::retError('未找到任何相关的成员');
        }
        foreach ($lists['lists'] AS $key => $item) {
            $userInfo = Users::username2basic($item['username']);
            $lists['lists'][$key]['userimg'] = $userInfo['userimg'];
            $lists['lists'][$key]['nickname'] = $userInfo['nickname'];
            $lists['lists'][$key]['profession'] = $userInfo['profession'];
        }
        return Base::retSuccess('success', $lists);
    }

    /**
     * 成员-添加、删除
     *
     * @apiParam {String} act
     * - delete: 删除成员
     * - else: 添加成员
     * @apiParam {Number} id                    知识库数据ID
     * @apiParam {Array|String} username        用户名（或用户名组）
     */
    public function users__join()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Request::input('id'));
        $role = Docs::checkRole($id, 'edit');
        if (Base::isError($role)) {
            return $role;
        }
        $row = Base::DBC2A(DB::table('docs_book')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('知识库不存在或已被删除！');
        }
        //
        $usernames = Request::input('username');
        if (empty($usernames)) {
            return Base::retError('参数错误！');
        }
        if (!is_array($usernames)) {
            if (Base::strExists($usernames, ',')) {
                $usernames = explode(',', $usernames);
            } else {
                $usernames = [$usernames];
            }
        }
        //
        foreach ($usernames AS $username) {
            $inRow = Base::DBC2A(DB::table('docs_users')->where(['bookid' => $id, 'username' => $username])->first());
            switch (Request::input('act')) {
                case 'delete': {
                    if ($inRow) {
                        DB::table('docs_users')->where([
                            'bookid' => $id,
                            'username' => $username
                        ])->delete();
                    }
                    break;
                }
                default: {
                    if (!$inRow && $username != $user['username']) {
                        DB::table('docs_users')->insert([
                            'bookid' => $id,
                            'username' => $username,
                            'indate' => Base::time()
                        ]);
                    }
                    break;
                }
            }
        }
        return Base::retSuccess('操作完成！');
    }

    /**
     * 章节列表
     *
     * @apiParam {String} act                   请求方式，用于判断权限
     * - edit: 管理页请求
     * - view: 阅读页请求
     * @apiParam {Number} bookid                知识库数据ID
     */
    public function section__lists()
    {
        $bookid = intval(Request::input('bookid'));
        $role = Docs::checkRole($bookid, Request::input('act'));
        if (Base::isError($role) && $role['data'] < 0) {
            return $role;
        }
        $lists = Base::DBC2A(DB::table('docs_section')
            ->where('bookid', $bookid)
            ->orderByDesc('inorder')
            ->orderByDesc('id')
            ->take(500)
            ->get());
        if (empty($lists)) {
            return Base::retError('暂无章节');
        }
        foreach ($lists AS $key => $item) {
            $lists[$key]['icon'] = Base::fillUrl('images/files/' . $item['type'] . '.png');
        }
        $bookDetail = Base::DBC2A(DB::table('docs_book')->select(['title'])->where('id', $bookid)->first());
        return Base::retSuccess('success', [
            'book' => $bookDetail ?: json_decode('{}'),
            'tree' => Base::list2Tree($lists, 'id', 'parentid')
        ]);
    }

    /**
     * 添加/修改章节
     *
     * @apiParam {Number} bookid                知识库数据ID
     * @apiParam {String} title                 章节名称
     * @apiParam {String} type                  章节类型
     */
    public function section__add()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $bookid = intval(Request::input('bookid'));
        $role = Docs::checkRole($bookid, 'edit');
        if (Base::isError($role)) {
            return $role;
        }
        $bookRow = Base::DBC2A(DB::table('docs_book')->where('id', $bookid)->first());
        if (empty($bookRow)) {
            return Base::retError('知识库不存在或已被删除！');
        }
        $count = DB::table('docs_section')->where('bookid', $bookid)->count();
        if ($count >= 500) {
            return Base::retError(['知识库章节已经超过最大限制（%）！', 500]);
        }
        //
        $id = intval(Request::input('id'));
        $title = trim(Request::input('title'));
        $type = trim(Request::input('type'));
        if (mb_strlen($title) < 2 || mb_strlen($title) > 100) {
            return Base::retError('标题限制2-100个字！');
        }
        if ($id > 0) {
            // 修改
            $row = Base::DBC2A(DB::table('docs_section')->where('id', $id)->first());
            if (empty($row)) {
                return Base::retError('知识库不存在或已被删除！');
            }
            $data = [
                'title' => $title,
            ];
            DB::table('docs_section')->where('id', $id)->update($data);
            return Base::retSuccess('修改成功！', $data);
        } else {
            // 添加
            if (!in_array($type, ['document', 'mind', 'sheet', 'flow', 'folder'])) {
                return Base::retError('参数错误！');
            }
            $parentid = 0;
            if ($id < 0) {
                $count = Base::DBC2A(DB::table('docs_section')->where('id', abs($id))->where('bookid', $bookid)->count());
                if ($count > 0) {
                    $parentid = abs($id);
                }
            }
            $data = [
                'bookid' => $bookid,
                'parentid' => $parentid,
                'username' => $user['username'],
                'title' => $title,
                'type' => $type,
                'inorder' => intval(DB::table('docs_section')->select(['inorder'])->where('bookid', $bookid)->orderByDesc('inorder')->value('inorder')) + 1,
                'indate' => Base::time(),
            ];
            $id = DB::table('docs_section')->insertGetId($data);
            if (empty($id)) {
                return Base::retError('系统繁忙，请稍后再试！');
            }
            $data['id'] = $id;
            return Base::retSuccess('添加成功！', $data);
        }
    }

    /**
     * 排序章节
     *
     * @apiParam {Number} bookid                知识库数据ID
     * @apiParam {String} oldsort               旧排序数据
     * @apiParam {String} newsort               新排序数据
     */
    public function section__sort()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $bookid = intval(Request::input('bookid'));
        $role = Docs::checkRole($bookid, 'edit');
        if (Base::isError($role)) {
            return $role;
        }
        $bookRow = Base::DBC2A(DB::table('docs_book')->where('id', $bookid)->first());
        if (empty($bookRow)) {
            return Base::retError('知识库不存在或已被删除！');
        }
        //
        $newSort = explode(";", Request::input('newsort'));
        if (count($newSort) == 0) {
            return Base::retError('参数错误！');
        }
        //
        $count = count($newSort);
        foreach ($newSort AS $sort => $item) {
            list($newId, $newParentid) = explode(':', $item);
            DB::table('docs_section')->where([
                'id' => $newId,
                'bookid' => $bookid
            ])->update([
                'inorder' => $count - intval($sort),
                'parentid' => $newParentid
            ]);
        }
        return Base::retSuccess('保存成功！');
    }

    /**
     * 删除章节
     *
     * @apiParam {Number} id                章节数据ID
     */
    public function section__delete()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Request::input('id'));
        $row = Base::DBC2A(DB::table('docs_section')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('文档不存在或已被删除！');
        }
        $role = Docs::checkRole($row['bookid'], 'edit');
        if (Base::isError($role)) {
            return $role;
        }
        DB::table('docs_section')->where('parentid', $id)->update([ 'parentid' => $row['parentid'] ]);
        DB::table('docs_section')->where('id', $id)->delete();
        DB::table('docs_content')->where('sid', $id)->delete();
        return Base::retSuccess('删除成功！');
    }

    /**
     * 获取章节内容
     *
     * @apiParam {String} act                   请求方式，用于判断权限
     * - edit: 管理页请求
     * - view: 阅读页请求
     * @apiParam {Number|String} id             章节数据ID（或：章节数据ID-历史数据ID）
     */
    public function section__content()
    {
        $id = Request::input('id');
        $hid = 0;
        if (Base::strExists($id, '-')) {
            list($id, $hid) = explode("-", $id);
        }
        $id = intval($id);
        $hid = intval($hid);
        $row = Base::DBC2A(DB::table('docs_section')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('文档不存在或已被删除！');
        }
        $role = Docs::checkRole($row['bookid'], Request::input('act'));
        if (Base::isError($role) && $role['data'] < 0) {
            return $role;
        }
        $whereArray = [];
        if ($hid > 0) {
            $whereArray[] = ['id', '=', $hid];
        }
        $whereArray[] = ['sid', '=', $id];
        $cRow = Base::DBC2A(DB::table('docs_content')->select(['id AS hid', 'content'])->where($whereArray)->orderByDesc('id')->first());
        if (empty($cRow)) {
            $cRow = [ 'hid' => 0, 'content' => '' ];
        }
        return Base::retSuccess('success', array_merge($row, $cRow));
    }

    /**
     * 获取章节历史内容
     *
     * @apiParam {Number} id                章节数据ID
     */
    public function section__history()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Request::input('id'));
        $row = Base::DBC2A(DB::table('docs_section')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('文档不存在或已被删除！');
        }
        $role = Docs::checkRole($row['bookid'], 'edit');
        if (Base::isError($role) && $role['data'] < 0) {
            return $role;
        }
        //
        $lists = Base::DBC2A(DB::table('docs_content')
            ->where('sid', $id)
            ->orderByDesc('id')
            ->take(50)
            ->get());
        if (count($lists) <= 1) {
            return Base::retError('暂无历史数据');
        }
        return Base::retSuccess('success', $lists);
    }

    /**
     * {post} 保存章节内容
     *
     * @apiParam {Number} id                章节数据ID
     * @apiParam {Object} [D]               Request Payload 提交
     * - content: 内容
     */
    public function section__save()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Base::getPostValue('id'));
        $row = Base::DBC2A(DB::table('docs_section')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('文档不存在或已被删除！');
        }
        $role = Docs::checkRole($row['bookid'], 'edit');
        if (Base::isError($role)) {
            return $role;
        }
        if ($row['lockdate'] + 60 > Base::time() && $row['lockname'] != $user['username']) {
            return Base::retError(['已被会员【%】锁定！', Users::nickname($row['lockname'])]);
        }
        $content = Base::getPostValue('content');
        $text = '';
        if ($row['type'] == 'document') {
            $data = Base::json2array($content);
            $isRep = false;
            preg_match_all("/<img\s*src=\"data:image\/(png|jpg|jpeg);base64,(.*?)\"/s", $data['content'], $matchs);
            foreach ($matchs[2] as $key => $text) {
                $p = "uploads/docs/document/" . $id . "/";
                Base::makeDir(public_path($p));
                $p.= md5($text) . "." . $matchs[1][$key];
                $r = file_put_contents(public_path($p), base64_decode($text));
                if ($r) {
                    $data['content'] = str_replace($matchs[0][$key], '<img src="' . Base::fillUrl($p) . '"', $data['content']);
                    $isRep = true;
                }
            }
            $text = strip_tags($data['content']);
            if ($isRep == true) {
                $content = Base::array2json($data);
            }
        }
        DB::table('docs_content')->where('sid', $id)->update(['text' => '']);
        DB::table('docs_content')->insert([
            'bookid' => $row['bookid'],
            'sid' => $id,
            'content' => $content,
            'text' => $text,
            'username' => $user['username'],
            'indate' => Base::time()
        ]);
        Docs::notice($id, [ 'type' => 'update' ]);
        //
        return Base::retSuccess('保存成功！', [
            'sid' => $id,
            'content' => Base::json2array($content),
        ]);
    }

    /**
     * 锁定章节内容
     *
     * @apiParam {String} act
     * - lock: 锁定
     * - unlock: 解锁
     * @apiParam {Number} id                章节数据ID
     */
    public function section__lock()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $id = intval(Request::input('id'));
        $act = trim(Request::input('act'));
        $row = Base::DBC2A(DB::table('docs_section')->where('id', $id)->first());
        if (empty($row)) {
            return Base::retError('文档不存在或已被删除！');
        }
        $role = Docs::checkRole($row['bookid'], 'edit');
        if (Base::isError($role)) {
            return $role;
        }
        if ($row['lockdate'] + 60 > Base::time() && $row['lockname'] != $user['username']) {
            return Base::retError(['已被会员【%】锁定！', Users::nickname($row['lockname'])]);
        }
        if ($act == 'lock') {
            $upArray = [
                'lockname' => $user['username'],
                'lockdate' => Base::time(),
            ];
        } else {
            $upArray = [
                'lockname' => '',
                'lockdate' => 0,
            ];
        }
        DB::table('docs_section')->where('id', $id)->update($upArray);
        $upArray['type'] = $act;
        Docs::notice($id, $upArray);
        //
        return Base::retSuccess($act == 'lock' ? '锁定成功' : '已解除锁定', $upArray);
    }
}
