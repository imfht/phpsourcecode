<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Todo;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    public function getList(Request $request)
    {
        $todos = Todo::getTodoListForControlPannel($request->all());

        return response($todos);
    }

    public function updateOrCreate(CreateTodoRequest $request)
    {
        $todo = Todo::updateOrCreate(['id' => $request->get('id')], $request->all());

        if (empty($todo)) {
            return response(['message' => '创建失败'], 400);
        }

        $description = $request->get('description');

        if (! empty($description)) {
            $desRes = $todo->withDescription()->updateOrCreate(
                ['todo_id' => $todo->getKey()],
                ['content' => $description]
            );

            if (empty($desRes)) {
                Todo::destroy($todo->id);

                return response(['message' => '任务创建失败'], 400);
            }
        }

        $todos = Todo::getTodoListForControlPannel();

        return response($todos);
    }

    public function updateTodo(UpdateTodoRequest $request)
    {
        $status = null;

        switch ($request->get('event')) {
            case 'redo':
            case 'start':
                $status = 1;
                break;
            case 'check':
                $status = 2;
                break;
            default:
                break;
        }

        if ($status === null) {
            return response(['message' => '未获取到状态'], 400);
        }

        Todo::find($request->get('id'))->update(['status' => $status]);

        $todos = Todo::getTodoListForControlPannel();

        return response($todos);
    }

    public function deleteTodo($id)
    {
        $res = Todo::destroy($id);
        if (empty($res)) {
            return response(['message' => '删除失败'], 400);
        }

        $todos = Todo::getTodoListForControlPannel();

        return response($todos);
    }
}
