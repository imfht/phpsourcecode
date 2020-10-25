<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActionLog;
use App\Services\ActionLogsService;

class ActionLogsController extends BaseController
{
    protected $actionLogsService;

    /**
     * ActionLogsController constructor.
     * @param $actionLogsService
     */
    public function __construct(ActionLogsService $actionLogsService)
    {
        $this->actionLogsService = $actionLogsService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $actions = $this->actionLogsService->getActionLogs();

        return $this->view(null,compact('actions'));
    }

    public function destroy(ActionLog $actionLog)
    {
        $actionLog->delete();

        flash('删除日志成功')->success()->important();

        return redirect()->route('actions.index');
    }
}
