<?php

namespace App\Http\Controllers\Admin;

use App\Model\FriendLink;
use App\Model\Attachment;
use App\Http\Controllers\Controller;
use App\Http\Requests\FriendLinkRequest;
use Redirect;
use Hash;
use Theme;
use Logs;

class FriendLinksController extends Controller
{
  public function index()
  {
    $friendLinks = FriendLink::sortByDesc('id')->paginate(20);
    return Theme::view('friendLinks.index',compact('friendLinks'));
  }

  public function create()
  {
    $friendLink = new FriendLink;
    $friendLink->id = 0;
    $friendLink->views = 0;
    $friendLink->sort = 0;
    $friendLink->is_open = 1;
    $friendLink->hash = Hash::make(time() . rand(1000, 9999));
    return Theme::view('friendLinks.create',compact('friendLink'));
  }

  public function edit($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $friendLink = FriendLink::find($id);
    if(!$friendLink) return Redirect::to(route('admin.pages'));

    if ($friendLink->hash == '') {
      $friendLink->hash = Hash::make(time() . rand(1000, 9999));
    }

    return Theme::view('friendLinks.edit',compact('friendLink'));
  }

  public function store(FriendLinkRequest $request)
  {
    $friendLink = FriendLink::create([
        'name' => $request->get('name'),
        'url' => $request->get('url'),
        'views' => $request->get('views'),
        'sort' => $request->get('sort'),
        'is_open' => $request->get('is_open'),
        'cover' => $request->get('cover'),
        'thumb' => $request->get('thumb'),
        'hash' => $request->get('hash'),
    ]);

    if ($friendLink) {
      Logs::save('friendLink',$friendLink->id,'store','添加友情链接');
      Attachment::where(['hash' => $friendLink->hash, 'project_id' => 0])->update(['project_id' => $friendLink->id]);
      $message = '友情链接添加成功，请选择操作！';
      $url = [];
      $url['返回友情链接列表'] = ['url' => route('admin.friendLinks.index')];
      $url['继续添加'] = ['url' => route('admin.friendLinks.create')];
      $url['继续编辑'] = ['url' => route('admin.friendLinks.edit', $friendLink->id)];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function update(FriendLinkRequest $request, $id = 0)
  {
    $friendLink = FriendLink::findOrFail($id);
    $friendLink->update([
        'name' => $request->get('name'),
        'url' => $request->get('url'),
        'views' => $request->get('views'),
        'sort' => $request->get('sort'),
        'is_open' => $request->get('is_open'),
        'cover' => $request->get('cover'),
        'thumb' => $request->get('thumb'),
        'hash' => $request->get('hash'),
    ]);

    if ($friendLink) {
      Logs::save('friendLink',$friendLink->id,'update','修改友情链接');
      Attachment::where(['hash' => $friendLink->hash, 'project_id' => 0])->update(['project_id' => $friendLink->id]);
      $message = '友情链接修改成功，请选择操作！';
      $url = [];
      $url['返回友情链接列表'] = ['url' => route('admin.friendLinks.index')];
      $url['继续添加'] = ['url' => route('admin.friendLinks.create')];
      $url['继续编辑'] = ['url' => route('admin.friendLinks.edit', $friendLink->id)];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function destroy($id)
  {
    FriendLink::destroy($id);
    Logs::save('friendLink',$id,'destroy','删除友情链接');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
