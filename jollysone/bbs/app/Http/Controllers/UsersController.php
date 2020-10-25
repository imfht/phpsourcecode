<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    public function show(User $user){
        return view('users.show',compact('user'));
    }

    public function edit(User $user){
        var_dump($user);
        $re = $this->authorize('update', $user);
        var_dump($re);
        return view('users.edit',compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user){
        var_dump($user);
        $re = $this->authorize('update', $user);
        var_dump($re);
        $data = $request->all();

        if ($request->avatar){
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 362);
            if ($result){
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '资料更新成功');
    }
}
