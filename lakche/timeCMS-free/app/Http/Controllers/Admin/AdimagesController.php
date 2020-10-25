<?php

namespace App\Http\Controllers\Admin;

use App\Model\Adimage;
use App\Model\Adspace;
use App\Model\Attachment;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdimageRequest;
use Redirect;
use Hash;
use Cache;
use Theme;
use Logs;

class AdimagesController extends Controller
{
  public function index()
  {
    $adimages = Adimage::sortByDesc('id')->paginate(20);
    return Theme::view('adimages.index',compact('adimages'));
  }

  public function show($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $adspace = Adspace::find($id);
    if(!$adspace) return Redirect::to(route('admin.adimages.index'));

    $adimages = Adimage::where('adspace_id',$id)->sortByDesc('id')->paginate(20);
    return Theme::view('adimages.show',compact('adimages','adspace'));
  }

  public function create()
  {
    $adimage = new Adimage;
    $adimage->id = 0;
    $adimage->views = 0;
    $adimage->sort = 0;
    $adimage->is_open = 1;
    $adimage->adspace_id = 1;
    $adimage->hash = Hash::make(time() . rand(1000, 9999));
    return Theme::view('adimages.create',compact('adimage'));
  }

  public function edit($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $adimage = Adimage::find($id);
    if(!$adimage) return Redirect::to(route('admin.adimages'));

    if ($adimage->hash == '') {
      $adimage->hash = Hash::make(time() . rand(1000, 9999));
    }

    return Theme::view('adimages.edit',compact('adimage'));
  }

  public function store(AdimageRequest $request)
  {
    $adimage = Adimage::create([
        'name' => $request->get('name'),
        'adspace_id' => $request->get('adspace_id'),
        'url' => $request->get('url'),
        'views' => $request->get('views'),
        'sort' => $request->get('sort'),
        'is_open' => $request->get('is_open'),
        'cover' => $request->get('cover'),
        'thumb' => $request->get('thumb'),
        'hash' => $request->get('hash'),
    ]);

    if ($adimage) {
      Logs::save('adimage',$adimage->id,'store','添加广告');
      Cache::store('ads')->flush();
      Attachment::where(['hash' => $adimage->hash, 'project_id' => 0])->update(['project_id' => $adimage->id]);
      $message = '广告添加成功，请选择操作！';
      $url = [];
      $url['返回广告列表'] = ['url' => route('admin.adimages.index')];
      $url['继续添加'] = ['url' => route('admin.adimages.create')];
      $url['继续编辑'] = ['url' => route('admin.adimages.edit', $adimage->id)];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function update(AdimageRequest $request, $id = 0)
  {
    $adimage = Adimage::findOrFail($id);
    $adimage->update([
        'name' => $request->get('name'),
        'adspace_id' => $request->get('adspace_id'),
        'url' => $request->get('url'),
        'views' => $request->get('views'),
        'sort' => $request->get('sort'),
        'is_open' => $request->get('is_open'),
        'cover' => $request->get('cover'),
        'thumb' => $request->get('thumb'),
        'hash' => $request->get('hash'),
    ]);

    if ($adimage) {
      Logs::save('adimage',$adimage->id,'update','修改广告');
      Cache::store('ads')->flush();
      Attachment::where(['hash' => $adimage->hash, 'project_id' => 0])->update(['project_id' => $adimage->id]);
      $message = '广告修改成功，请选择操作！';
      $url = [];
      $url['返回广告列表'] = ['url' => route('admin.adimages.index')];
      $url['继续添加'] = ['url' => route('admin.adimages.create')];
      $url['继续编辑'] = ['url' => route('admin.adimages.edit', $adimage->id)];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function destroy($id)
  {
    Adimage::destroy($id);
    Cache::store('ads')->flush();
    Logs::save('adimage',$id,'destroy','删除广告');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
