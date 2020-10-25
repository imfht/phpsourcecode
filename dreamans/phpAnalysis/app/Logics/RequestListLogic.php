<?php

namespace App\Logics;

use App\Models\Request;

class RequestListLogic
{
    public function getRequestList(array $searchParam, $offset, $limit)
    {
        $model = Request::limit($offset, $limit);
        if ($searchParam['starttime']) {
            $model->where('request_time', $searchParam['starttime'], '>');
        }
        if ($searchParam['endtime']) {
            $model->where('request_time', $searchParam['endtime'], '<');
        }
        if ($searchParam['url']) {
            $model->where('url', sprintf('%%%s%%', $searchParam['url']), 'like');
        }
        if ($searchParam['app']) {
            $model->where('app', $searchParam['app']);
        }
        if ($searchParam['order'] && in_array($searchParam['order'], ['cost_time', 'request_time'])) {
            $model->orderBy($searchParam['order'], 'desc');
        }
        $ret = $model->all();
        $arrReqList = [];
        if ($ret) {
            foreach ($ret as $data) {
                $arrReqList[] = [
                    'method' => $data['method'],
                    'url' => $data['url'],
                    'request_id' => $data['request_id'],
                    'host' => $data['host'],
                    'request_time' => date('Y-m-d H:i:s', $data['request_time']),
                    'cost_time' => $data['cost_time'],
                    'cpu_time' => $data['cpu_time'],
                    'memory' => $data['memory'],
                    'pmemory' => $data['pmemory'],
                    'app' => $data['app'],
                ];
            }
        }
        return $arrReqList;
    }

    public function getRequestCount(array $searchParam)
    {
        $model = Request::instance();
        if ($searchParam['starttime']) {
            $model->where('request_time', $searchParam['starttime'], '>');
        }
        if ($searchParam['endtime']) {
            $model->where('request_time', $searchParam['endtime'], '<');
        }
        if ($searchParam['url']) {
            $model->where('url', sprintf('%%%s%%', $searchParam['url']), 'like');
        }
        if ($searchParam['app']) {
            $model->where('app', $searchParam['app']);
        }
        return $model->count();
    }
}

