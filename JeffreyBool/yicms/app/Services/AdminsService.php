<?php
/**
 * YICMS
 * ============================================================================
 * 版权所有 2014-2017 YICMS，并保留所有权利。
 * 网站地址: http://www.yicms.vip
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Created by PhpStorm.
 * Author: kenuo
 * Date: 2017/11/13
 * Time: 上午9:50
 */

namespace App\Services;

use Auth;
use App\Handlers\ImageUploadHandler;
use App\Repositories\AdminsRepository;
use Illuminate\Support\Facades\Hash;

class AdminsService
{
    protected $uploader;

    protected $adminsRepository;

    protected $actionLogsService;

    /**
     * AdminsService constructor.
     * @param AdminsRepository $adminsRepository
     * @param ImageUploadHandler $imageUploadHandler
     * @param ActionLogsService $actionLogsService
     */
    public function __construct(AdminsRepository $adminsRepository, ImageUploadHandler $imageUploadHandler,ActionLogsService $actionLogsService)
    {
        $this->uploader = $imageUploadHandler;
        $this->adminsRepository = $adminsRepository;
        $this->actionLogsService = $actionLogsService;
    }

    /**
     * 创建管理员数据
     * @param $request
     * @return mixed
     */
    public function create($request)
    {
        $datas = $request->all();

        //上传头像
        if ($request->avatr) {
            $result = $this->uploader->save($request->avatr, 'avatrs');
            if ($result) {
                $datas['avatr'] = $result['path'];
            }
        }

        $datas['password'] = Hash::make($request->password);
        $datas['create_ip'] = $request->ip();
        $datas['last_login_ip'] = $request->ip();

        $admin = $this->adminsRepository->create($datas);

        //插入模型关联数据
        $admin->roles()->attach($request->role_id);

        return $admin;
    }

    /**
     * 更新管理员资料
     * @param $request
     * @param $id
     * @return mixed
     */
    public function update($request, $id)
    {
        $datas = $request->all();

        $admin = $this->adminsRepository->ById($id);

        //上传头像
        if ($request->avatr) {
            $result = $this->uploader->save($request->avatr, 'avatrs');
            if ($result) {
                $datas['avatr'] = $result['path'];
            }
        }

        if (isset($datas['password'])) {
            $datas['password'] = Hash::make($request->password);
        } else {
            unset($datas['password']);
        }

        $admin->update($datas);

        //更新关联表数据
        $admin->roles()->sync($request->role_id);

        return $admin;
    }

    /**
     * 获取管理员的详细资料
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return $this->adminsRepository->ById($id);
    }

    /**
     * 获取管理员列表 with ('roles')
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminsWithRoles()
    {
        return $this->adminsRepository->getAdminsWithRoles();
    }


    /**
     * 登录管理员
     * @param $request
     * @return bool
     */
    public function login($request)
    {
        if(!Auth::guard('admin')->attempt([
            'name'     => $request->name,
            'password' => $request->password,
            'status'   => 1,
        ])){
            //记录登录操作记录
            $this->actionLogsService->loginActionLogCreate($request,false);
            return false;
        }

        //增加登录次数.
        $admin = Auth::guard('admin')->user();

        $admin->increment('login_count');

        //记录登录操作记录
        $this->actionLogsService->loginActionLogCreate($request,true);

        return true;
    }

    /**
     * 退出登录
     * @return mixed
     */
    public function logout()
    {
        return Auth::guard('admin')->logout();
    }
}