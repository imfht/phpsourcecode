<?php

namespace App\Http\Controllers;

use App\Common\StFetch;
use App\Common\StValidator;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
   public function getAllUsers(Request $request)
   {
       StValidator::make($request->all(),[
           'offset'=>'required|integer',
           'limit'=>'required|integer',
           'search'=>'string',
           'class'=>'array',
           'groupId'=>'array',
           'withFilter'=>'boolean',
       ]);

       $offset = $request['offset'];
       $limit = $request['limit'];
       $teacher = Auth::user();

       $query = DB::table('users')
           ->select('id','stuId','name','email','privilege','class','groupId')
           ->where('teacherId',$teacher['id'])
           ->where('privilege','<',2)
           ->orderBy('id','asc');

       if(isset($request['class'])){
           $query->whereIn('class',$request['class']);
       }

       if(isset($request['groupId'])){
           $query->whereIn('groupId',$request['groupId']);
       }

       $response = ['error' => 0];

       if($request['withFilter']){
           $queryFilter = DB::table('users')
               ->where('teacherId',$teacher['id'])
               ->where('privilege','<',2);

           $response['classFilter'] = $queryFilter->select('class')->distinct()->orderBy('class','asc')->get();

           $response['groupFilter'] = $queryFilter->select('groupId')->distinct()->orderBy('groupId','asc')->get();
       }

       if(isset($request['search']) && strlen($request['search']) > 0){
           $search = $request['search'];
           $query->where(function($q) use($search) {
              $q->orWhere('stuId','like',"%".$search."%")
                  ->orWhere('name','like',"%".$search."%");
           });
       }

       $response['total'] = $query->count();
       $response['rows'] = $query->skip($offset)
           ->take($limit)
           ->get();

       return $response;
   }

    public function getUserDetail(Request $request)
    {
        StValidator::make($request->all(),[
            'stuId' => 'required_without:id|integer',
            'id' => 'required_without:stuId|integer',
        ]);

        $teacher = Auth::user();

        $query = DB::table('users')
            ->select('id','stuId','name','email','privilege','class','groupId')
            ->where('teacherId',$teacher['id']);

        if(isset($request['stuId'])){
            $query->where('stuId',$request['stuId']);
        } else {
            $query->where('id',$request['id']);
        }

        $user = $query->first();

        if(!$user){
            return ['error' => 'not_find_the_user'];
        }

        $tasks = DB::table('taskbank')
            ->leftJoin('taskreport',function($join) use($user){
                $join->on('taskbank.id','=','taskreport.taskId')
                    ->where('taskreport.userId','=',$user->id);
            })
            ->where('groupId',$user->groupId)
            ->select('taskreport.score','taskreport.content','taskreport.created_at as finishTime','taskbank.id',
                'title','week','type','target','deadLine','time','experience','experienceScore')
            ->orderBy('taskbank.startTime','desc')
            ->get();

        return [
            'error' => 0,
            'userInfo' => $user,
            'tasks' => $tasks,
        ];
    }


}
