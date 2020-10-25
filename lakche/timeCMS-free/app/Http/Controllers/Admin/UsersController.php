<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Theme;
use Logs;

class UsersController extends Controller
{
  public function index()
  {
    $users = User::sortByDesc('id')->paginate(20);
    return Theme::view('users.index', compact('users'));
  }

  public function destroy($id = 0)
  {
    $id = intval($id);
    if($id == 1) {
      return ['error' => 1, 'message' => '初始账号无法删除！'];
    }
    User::destroy($id);
    Logs::save('user',$id,'destroy','删除用户');
    return ['error' => 0, 'message' => '删除成功！'];
  }

  public function update(UserRequest $request, $id)
  {
    $user = User::find($id);
    if(!$user) {
      return ['error' => 1, 'message' => '用户不存在或已被删除！'];
    }

    switch ($request->get('attr'))
    {
      case 'admin':
        if($user->is_admin > 0){
          if($user->id == 1) {
            return ['error' => 1, 'message' => '不能删除默认管理员账号！'];
          }
          $user->is_admin = 0;
          $user->save();
          Logs::save('user',$id,'update','移除用户权限');
          return ['error' => 0, 'message' => '管理员权限移除成功！'];
        } else {
          $user->is_admin = 1;
          $user->save();
          Logs::save('user',$id,'update','添加用户权限');
          return ['error' => 0, 'message' => '管理员权限添加成功！'];
        }
        break;
      default:
        return ['error' => 1, 'message' => '不能修改该属性，请刷新！！'];
    }

  }
}
