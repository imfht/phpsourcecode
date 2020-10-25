<?php
/**
 * Created by PhpStorm.
 * User: targaryen
 * Date: 2016/11/18
 * Time: 下午10:59.
 */

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Eloquent
{
    use SoftDeletes;

    protected $fillable = [
        'parents_id',
        'grandparents_id',
        'importance',
        'content',
        'status',
        'begin_at',
        'end_at',
    ];

    protected $dates = ['deleted_at'];

    /**
     * get todo-list for control pannel.
     * @param array $params
     * @return array
     */
    public static function getTodoListForControlPannel($params = ['page' => 1, 'status' => 'all'])
    {
        $total = self::all()->count();
        $todosCount = self::where('status', 0)->count();
        $doingCount = self::where('status', 1)->count();
        $doneCount = self::where('status', 2)->count();
        $page = $params['page'];
        $pageSize = 10;
        $skip = ($page - 1) * $pageSize;

        $pageCount = ceil(self::all()->count() / $pageSize);
        $prev = $page - 1 > 0 ? $page - 1 : 0;
        $next = $page + 1 <= $pageCount ? $page + 1 : $pageCount;

        $status = self::handleStatus(isset($params['status']) ? $params['status'] : null);

        if ($status !== null) {
            $todos = self::where('status', $status)
                ->orderByDesc('created_at')
                ->skip($skip)
                ->take($pageSize)
                ->get();
        } else {
            $todos = self::orderByDesc('created_at')
                ->skip($skip)
                ->take($pageSize)
                ->get();
        }

        foreach ($todos as &$todo) {
            $todo->begin_at = Carbon::parse($todo->begin_at)->format('Y-m-d');
            $todo->end_at = Carbon::parse($todo->end_at)->format('Y-m-d');
            $description = $todo->withDescription()->first();
            $todo->description = $description ? $description->content : null;
            $todo->importanceStyle = ['info', 'primary', 'warning', 'danger'][$todo->importance];
            $todo->statusText = ['active', 'exception', 'normal', 'success'][$todo->status];
        }

        return compact(
            'todos',
            'prev',
            'next',
            'pageCount',
            'pageSize',
            'total',
            'todosCount',
            'doingCount',
            'doneCount'
        );
    }

    private static function handleStatus($statusStr)
    {
        $status = null;

        switch ($statusStr) {
            case 'waiting':
                $status = 0;
                break;
            case 'progress':
                $status = 1;
                break;
            case 'done':
                $status = 2;
                break;
            default:
                break;
        }

        return $status;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function withDescription()
    {
        return $this->hasOne('App\TodoDescription');
    }
}
