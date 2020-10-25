<?php

namespace App\Controllers;

use App\Logics\RequestListLogic;

class RequestListController extends Controller
{
    private $reqListLogic = null;

    protected function initialize()
    {
        $this->reqListLogic = new RequestListLogic();
    }

    public function request($request)
    {
        $this->reqApp = $request->request('req_app');
        $this->reqUrl = $request->request('req_url');
        $this->reqStartDate = $request->request('req_start_date');
        $this->reqStartTime = $request->request('req_start_time');
        $this->reqEndDate = $request->request('req_end_date');
        $this->reqEndTime = $request->request('req_end_time');
        $this->reqOrder = $request->request('req_order');

        $offset = $request->request('offset');
        $limit = $request->request('limit');
        $this->offset = $offset ? $offset : 0;
        $this->limit = $limit ? $limit: 10;
    }

    public function response()
    {
        return $this->outputJson([
            'list' => $this->requestList,
            'total' => $this->requestTotal,
        ]);
    }

    public function main()
    {
        $startTime = $this->searchTimestampParam($this->reqStartDate, $this->reqStartTime);
        $endTime = $this->searchTimestampParam($this->reqEndDate, $this->reqEndTime);

        $searchParam = [
            'app' => $this->reqApp,
            'url' => $this->reqUrl,
            'order' => $this->reqOrder,
            'starttime' => $startTime,
            'endtime' => $endTime,
        ];
        $this->requestList = $this->reqListLogic->getRequestList($searchParam, $this->offset, $this->limit);
        $this->requestTotal = $this->reqListLogic->getRequestCount($searchParam);

    }

    private function searchTimestampParam($reqDate, $reqTime)
    {
        $timestamp = 0;
        if ($reqDate) {
            $timestamp = strtotime($reqDate);
            if ($reqTime) {
                $tmpReqTime = strtotime($reqTime);
                $timestamp += $tmpReqTime - strtotime(date("Y-m-d", $tmpReqTime));
            }
        }
        return $timestamp;
    }
}

