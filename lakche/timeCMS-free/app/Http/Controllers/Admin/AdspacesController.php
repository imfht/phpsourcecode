<?php

namespace App\Http\Controllers\Admin;

use App\Model\Adspace;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdspaceRequest;
use Redirect;
use Hash;
use Cache;
use Theme;
use Logs;

class AdspacesController extends Controller
{
  public function index()
  {
    $adspaces = Adspace::sortByDesc('id')->paginate(20);
    return Theme::view('adspaces.index',compact('adspaces'));
  }

  public function create()
  {
    $adspace = new Adspace;
    $adspace->id = 0;
    $adspace->is_open = 1;
    $adspace->hash = Hash::make(time() . rand(1000, 9999));
    return Theme::view('adspaces.create',compact('adspace'));
  }

  public function edit($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $adspace = Adspace::find($id);
    if(!$adspace) return Redirect::to(route('admin.adspaces'));

    if ($adspace->hash == '') {
      $adspace->hash = Hash::make(time() . rand(1000, 9999));
    }

    return Theme::view('adspaces.edit',compact('adspace'));
  }

  public function store(AdspaceRequest $request)
  {
    $adspace = Adspace::create([
        'name' => $request->get('name'),
        'is_open' => $request->get('is_open'),
        'hash' => $request->get('hash'),
    ]);

    if ($adspace) {
      Logs::save('adspace',$adspace->id,'store','添加广告位');
      Cache::store('adspaces')->flush();
      $message = '广告位添加成功，请选择操作！';
      $url = [];
      $url['返回广告位列表'] = ['url' => route('admin.adspaces.index')];
      $url['继续添加'] = ['url' => route('admin.adspaces.create')];
      $url['继续编辑'] = ['url' => route('admin.adspaces.edit', $adspace->id)];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function update(AdspaceRequest $request, $id = 0)
  {
    $adspace = Adspace::findOrFail($id);
    $adspace->update([
      'name' => $request->get('name'),
      'is_open' => $request->get('is_open'),
      'hash' => $request->get('hash'),
    ]);

    if ($adspace) {
      Logs::save('adspace',$adspace->id,'update','修改广告位');
      Cache::store('adspaces')->flush();
      $message = '广告位修改成功，请选择操作！';
      $url = [];
      $url['返回广告位列表'] = ['url' => route('admin.adspaces.index')];
      $url['继续添加'] = ['url' => route('admin.adspaces.create')];
      $url['继续编辑'] = ['url' => route('admin.adspaces.edit', $adspace->id)];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function destroy($id)
  {
    Adspace::destroy($id);
    Cache::store('adspaces')->flush();
    Logs::save('adspace',$id,'destroy','删除广告位');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
