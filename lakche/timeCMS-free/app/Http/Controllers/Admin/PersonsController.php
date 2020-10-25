<?php

namespace App\Http\Controllers\Admin;

use App\Model\Person;
use App\Model\Attachment;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use Redirect;
use Hash;
use Cache;
use Theme;
use Logs;

class PersonsController extends Controller
{
  public function index()
  {
    $persons = Person::sortByDesc('point')->paginate(20);
    return Theme::view('persons.index',compact('persons'));
  }

  public function create()
  {
    $person = new Person;
    $person->id = 0;
    $person->is_show = 1;
    $person->sort = 0;
    $person->sex = 0;
    $person->age = 0;
    $person->point = 0;
    $person->is_recommend = 0;
    $person->tag = '';
    $person->hash = Hash::make(time());
    return Theme::view('persons.create',compact('person'));
  }

  public function edit($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $person = Person::find($id);
    if(!$person) return Redirect::to(route('admin.persons.index'));

    if ($person->hash == '') {
      $person->hash = Hash::make(time() . rand(1000, 9999));
    }

    return Theme::view('persons.edit',compact('person'));
  }

  public function store(PersonRequest $request)
  {
    $person = Person::create([
        'name' => $request->get('name'),
        'title' => $request->get('title'),
        'sex' => $request->get('sex'),
        'sort' => $request->get('sort'),
        'point' => $request->get('point'),
        'age' => $request->get('age'),
        'tag' => $request->get('tag'),
        'is_recommend' => $request->get('is_recommend'),
        'is_show' => $request->get('is_show'),
        'head' => $request->get('head'),
        'head_thumbnail' => $request->get('head_thumbnail'),
        'url' => $request->get('url'),
        'keywords' => $request->get('keywords'),
        'description' => $request->get('description'),
        'info' => $request->get('info'),
        'text' => $request->get('text'),
        'hash' => $request->get('hash'),
    ]);

    if ($person) {
      Logs::save('person',$person->id,'store','添加人物');
      Cache::store('person')->flush();
      Attachment::where(['hash' => $person->hash, 'project_id' => 0])->update(['project_id' => $person->id]);
      $message = '人物发布成功，请选择操作！';
      $url = [];
      $url['返回人物列表'] = ['url' => route('admin.persons.index')];
      $url['继续添加'] = ['url' => route('admin.persons.create')];
      $url['继续编辑'] = ['url' => route('admin.persons.edit', $person->id)];
      $url['查看人物'] = ['url' => route('person.show', $person->id), 'target' => '_blank'];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function update(PersonRequest $request, $id = 0)
  {
    $person = Person::findOrFail($id);
    $person->update([
        'name' => $request->get('name'),
        'title' => $request->get('title'),
        'sex' => $request->get('sex'),
        'sort' => $request->get('sort'),
        'point' => $request->get('point'),
        'age' => $request->get('age'),
        'tag' => $request->get('tag'),
        'is_recommend' => $request->get('is_recommend'),
        'is_show' => $request->get('is_show'),
        'head' => $request->get('head'),
        'head_thumbnail' => $request->get('head_thumbnail'),
        'url' => $request->get('url'),
        'keywords' => $request->get('keywords'),
        'description' => $request->get('description'),
        'info' => $request->get('info'),
        'text' => $request->get('text'),
        'hash' => $request->get('hash'),
    ]);

    if ($person) {
      Logs::save('person',$person->id,'update','修改人物');
      Cache::store('person')->flush();
      Attachment::where(['hash' => $person->hash, 'project_id' => 0])->update(['project_id' => $person->id]);
      $message = '人物发布成功，请选择操作！';
      $url = [];
      $url['返回人物列表'] = ['url' => route('admin.persons.index')];
      $url['继续添加'] = ['url' => route('admin.persons.create')];
      $url['继续编辑'] = ['url' => route('admin.persons.edit', $person->id)];
      $url['查看人物'] = ['url' => route('person.show', $person->id), 'target' => '_blank'];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function destroy($id)
  {
    Person::destroy($id);
    Cache::store('person')->flush();
    Logs::save('person',$id,'destroy','删除人物');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
