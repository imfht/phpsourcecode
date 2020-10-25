<?php

namespace App\Controllers;

use App\Logics\RequestDetailLogic;

class RequestDetailController extends Controller
{
    private $respData = [];

    protected function initialize()
    {
        $this->reqDetailLogic= new RequestDetailLogic();
    }

    public function request($request)
    {
        $this->requestId = $request->get('request_id');
        $this->isShowDetail = $request->get('show_detail');
        $this->valid = $this->validator([
            ['request_id', $this->requestId, 'require'],
        ]);
    }

    public function response()
    {
        if (!$this->valid->validate) {
            return $this->outputJson([], -1, $this->valid->firstMessage);
        }

        return $this->outputJson($this->respData);
    }

    public function main()
    {
        if (!$this->valid->validate) {
            return false;
        }
        
        $rawDetail = $this->reqDetailLogic->getRequestRawData($this->requestId, $this->isShowDetail);
        if (!$rawDetail || !$rawDetail['profile']) {
            return false;
        }

        $this->respData['base'] = [
            'request_time' => date('Y-m-d H:i:s', $rawDetail['request_time']),
            'cost_time' => sprintf('%.3f ms', $rawDetail['cost_time']/1000),
            'cpu_time' => sprintf('%.3f ms', $rawDetail['cpu_time']/1000),
            'memory' => sprintf('%.3f MB', $rawDetail['memory']/1024/1024),
            'pmemory' => sprintf('%.3f MB', $rawDetail['pmemory']/1024/1024),
            'request_id' => $rawDetail['request_id'],
            'host' => $rawDetail['host'],
            'url' => $rawDetail['url'],
            'method' => $rawDetail['method'],
            'app' => $rawDetail['app'],
        ];

        foreach (['get', 'post', 'cookie', 'server'] as $key) {
            $this->formatRespArrayData($key, $rawDetail[$key]);
        }

        $baseProf = [
            'cost_time' => $rawDetail['cost_time'],
            'cpu_time' => $rawDetail['cpu_time'],
            'memory' => $rawDetail['memory'],
            'pmemory' => $rawDetail['pmemory'],
        ];

        $parentChildProfile = $this->reqDetailLogic->parentChildProfile($rawDetail['profile']);

        unset($rawDetail);

        $simpleProfile = $this->reqDetailLogic->simpleProfileCallChain($parentChildProfile);
        $normalProfile = $this->reqDetailLogic->normalProfileRate($simpleProfile, $baseProf);
        $exclWallTimeList = $this->reqDetailLogic->profileExclWallTimeList($normalProfile);

        $totCount = count($exclWallTimeList);
        if (!$this->isShowDetail) {
            $totCount = $totCount > 50 ? 50: $totCount;
            $minExclTime = $exclWallTimeList[$totCount - 1];
            $normalProfile = $this->reqDetailLogic->reduceNormalProfile($normalProfile, $minExclTime);
        }
        $normalProfile = array_reverse(array_values($normalProfile));
        $this->respData['profile'] = $normalProfile;

        $this->alertExclTimeThreshold($totCount, $exclWallTimeList);

        $topWallTime = isset($exclWallTimeList[9]) ? $exclWallTimeList[9]: $exclWallTimeList[count($exclWallTimeList) - 1];
        $this->filterMaxExclTimeCalls($normalProfile, $topWallTime);
    }

    private function alertExclTimeThreshold($totCount, $exclWtList)
    {
        $yellowCount = ceil($totCount * 0.1);
        $yellowCount = $yellowCount > 10 ? 10 : $yellowCount;
        $redCount = round($yellowCount * 0.3);
        $yellowWtTime = isset($exclWtList[$yellowCount-1]) ? $exclWtList[$yellowCount-1] : 0;
        $redWtTime = isset($exclWtList[$redCount-1]) ? $exclWtList[$redCount-1] : 0;
        $this->respData['alert_wt_time'] = [
            'yellow' => $yellowWtTime,
            'red' => $redWtTime,    
        ];
    }

    private function filterMaxExclTimeCalls(array $profile, $topWallTime)
    {
        $newProfile = $arrSort = [];
        foreach ($profile as $prof) {
            if ($prof['excl_wt'] >= $topWallTime) {
                $newProfile[] = $prof;
                $arrSort[] = $prof['excl_wt'];
            }
        }
        array_multisort($arrSort, SORT_DESC, $newProfile);
        $this->respData['top_walltime_profile'] = $newProfile;
    }

    private function formatRespArrayData($name, array $arrData)
    {
        $respData = [];
        foreach ($arrData as $key => $val) {
            $respData[] = [
                'name' => $key,
                'value' => $val,    
            ];
        }
        $this->respData[$name] = $respData;
    } 
}

