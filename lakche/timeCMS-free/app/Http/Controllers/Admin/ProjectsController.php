<?php

namespace App\Http\Controllers\Admin;

use App\Model\Project;
use App\Model\Category;
use App\Model\Attachment;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use Redirect;
use Hash;
use Cache;
use Theme;
use Logs;

class ProjectsController extends Controller
{
  public function index()
  {
    $projects = Project::sortByDesc('id')->paginate(20);
    return Theme::view('projects.index',compact('projects'));
  }

  public function show($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $type = Category::find($id);
    if(!$type) return Redirect::to(route('admin.projects.index'));

    $projects = Project::where('category_id',$id)->sortByDesc('id')->paginate(20);
    return Theme::view('projects.show',compact('projects','type'));
  }

  public function create()
  {
    $project = new Project;
    $project->id = 0;
    $project->is_show = 1;
    $project->sort = 0;
    $project->views = 0;
    $project->cost = 0;
    $project->period = 0;
    $project->category_id = 1;
    $project->tag = '';
    $project->person_id = json_encode([]);
    $project->hash = Hash::make(time());
    return Theme::view('projects.create',compact('project'));
  }

  public function edit($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $project = Project::find($id);
    if(!$project) return Redirect::to(route('admin.projects.index'));

    if ($project->hash == '') {
      $project->hash = Hash::make(time() . rand(1000, 9999));
    }

    return Theme::view('projects.edit',compact('project'));
  }

  public function store(ProjectRequest $request)
  {
    $speed = [];
    $time = $request->get('time');
    $event = $request->get('event');
    foreach ($time as $key => $value) {
      if ($time[$key] != '') {
        $speed[] = ['time' => strip_tags($time[$key]), 'event' => strip_tags($event[$key])];
      }
    }
    $speed = array_sort($speed, function ($value) {
      return $value['time'];
    });
    $speed = json_encode($speed);

    $project = Project::create([
        'title' => $request->get('title'),
        'category_id' => $request->get('category_id'),
        'sort' => $request->get('sort'),
        'views' => $request->get('views'),
        'tag' => $request->get('tag'),
        'is_recommend' => $request->get('is_recommend'),
        'is_show' => $request->get('is_show'),
        'cover' => $request->get('cover'),
        'thumb' => $request->get('thumb'),
        'cost' => $request->get('cost'),
        'period' => $request->get('period'),
        'person_id' => $request->get('person_id'),
        'info' => $request->get('info'),
        'url' => $request->get('url'),
        'keywords' => $request->get('keywords'),
        'description' => $request->get('description'),
        'text' => $request->get('text'),
        'speed' => $speed,
        'hash' => $request->get('hash'),
    ]);

    if ($project) {
      Logs::save('project',$project->id,'store','添加项目');
      Cache::store('project')->flush();
      Attachment::where(['hash' => $project->hash, 'project_id' => 0])->update(['project_id' => $project->id]);
      $message = '项目添加成功，请选择操作！';
      $url = [];
      $url['返回项目列表'] = ['url'=>route('admin.projects.index')];
      if($project->category_id > 0) $url['返回栏目项目列表'] = ['url'=>route('admin.projects.show',$project->category_id)];
      $url['继续添加'] = ['url'=>route('admin.projects.create')];
      $url['继续编辑'] = ['url'=>route('admin.projects.edit',$project->id)];
      $url['查看项目'] = ['url'=>route('project.show',$project->id),'target'=>'_blank'];
      return Theme::view('message.show',compact('message','url'));
    }
  }

  public function update(ProjectRequest $request, $id = 0)
  {

    $speed = [];
    $time = $request->get('time');
    $event = $request->get('event');
    foreach ($time as $key => $value) {
      if ($time[$key] != '') {
        $speed[] = ['time' => strip_tags($time[$key]), 'event' => strip_tags($event[$key])];
      }
    }
    $speed = array_sort($speed, function ($value) {
      return $value['time'];
    });
    $speed = json_encode($speed);

    $project = Project::findOrFail($id);
    $project->update([
        'title' => $request->get('title'),
        'category_id' => $request->get('category_id'),
        'sort' => $request->get('sort'),
        'views' => $request->get('views'),
        'tag' => $request->get('tag'),
        'is_recommend' => $request->get('is_recommend'),
        'is_show' => $request->get('is_show'),
        'cover' => $request->get('cover'),
        'thumb' => $request->get('thumb'),
        'cost' => $request->get('cost'),
        'period' => $request->get('period'),
        'person_id' => $request->get('person_id'),
        'info' => $request->get('info'),
        'url' => $request->get('url'),
        'keywords' => $request->get('keywords'),
        'description' => $request->get('description'),
        'text' => $request->get('text'),
        'speed' => $speed,
        'hash' => $request->get('hash'),
    ]);

    if ($project) {
      Logs::save('project',$project->id,'update','修改项目');
      Cache::store('project')->flush();
      Attachment::where(['hash' => $project->hash, 'project_id' => 0])->update(['project_id' => $project->id]);
      $message = '项目修改成功，请选择操作！';
      $url = [];
      $url['返回项目列表'] = ['url'=>route('admin.projects.index')];
      if($project->category_id > 0) $url['返回栏目项目列表'] = ['url'=>route('admin.projects.show',$project->category_id)];
      $url['继续添加'] = ['url'=>route('admin.projects.create')];
      $url['继续编辑'] = ['url'=>route('admin.projects.edit',$project->id)];
      $url['查看项目'] = ['url'=>route('project.show',$project->id),'target'=>'_blank'];
      return Theme::view('message.show',compact('message','url'));
    }
  }

  public function destroy($id)
  {
    Project::destroy($id);
    Cache::store('project')->flush();
    Logs::save('project',$id,'destroy','删除项目');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
