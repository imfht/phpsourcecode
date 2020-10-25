<?php

namespace App\Http\Controllers;

use App\Model\Project;
use App\Model\Category;
use Redirect;
use Theme;

class ProjectController extends Controller
{
  public function index()
  {
    $projects = Project::where('is_show','>',0)->sortByDesc('id')->paginate(20);
    return Theme::view('project.index',compact('projects'));
  }

  public function getType($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $type = Category::find($id);
    if(empty($type)) return Redirect::to('/');

    $keywords = $type->keywords;
    $description = $type->description;

    $projects = Project::where('category_id',$id)->where('is_show','>',0)->sortByDesc('id')->paginate(20);
    return Theme::view('project.index',compact('projects','type','keywords','description'));
  }

  public function show($id = 0)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $project = Project::where('id',$id)->where('is_show','>',0)->first();
    if(empty($project)) return Redirect::to('/');

    $type = Category::find($project->category_id);
    if(empty($type)) return Redirect::to('/');

    ++$project->views;
    $project->save();

    $keywords = $project->keywords;
    $description = $project->description;

    if($project->url != '') return Redirect::to($project->url);

    return Theme::view('project.show',compact('project','type','keywords','description'));
  }
}
