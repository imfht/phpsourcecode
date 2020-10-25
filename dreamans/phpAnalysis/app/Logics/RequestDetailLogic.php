<?php

namespace App\Logics;

use App\Models\Request;

class RequestDetailLogic
{
    public function getRequestRawData($requestId)
    {
        $rawData = Request::where('request_id', $requestId)->first();
        if (!$rawData) {
            return false;
        }
        return [
            'request_id' => $rawData['request_id'],
            'url' => $rawData['url'],
            'method' => $rawData['method'],
            'request_time' => $rawData['request_time'],
            'cost_time' => $rawData['cost_time'],
            'host' => $rawData['host'],
            'memory' => $rawData['memory'],
            'pmemory' => $rawData['pmemory'],
            'cpu_time' => $rawData['cpu_time'],
            'cookie' => json_decode($rawData['cookie'], true),
            'get' => json_decode($rawData['get'], true),
            'post' => json_decode($rawData['post'], true),
            'server' => json_decode($rawData['server'], true),
            'app' => $rawData['app'],
            'profile' => json_decode(gzuncompress($rawData['profile']), true),
        ];
    }

    public function parentChildProfile(array $profile)
    {
        $callChainRelate = [];
        foreach ($profile as $key => $value) {
            $callChain = array_filter(explode("==>", $key));
            $count = count($callChain);
            if (!$count || $count > 2) {
                continue;
            }
            $child = $callChain ? array_pop($callChain): null;
            $parent = $callChain ? array_pop($callChain) : null;
            $callChainRelate[] = [
                'parent' => $parent,
                'child' => $child,
                'profile' => $value,
            ];
        }
        return $callChainRelate;
    }

    public function simpleProfileCallChain(array $profile)
    {
        $callChildProf = $callParentProf = [];
        $field = ['ct', 'wt', 'cpu', 'mu', 'pmu'];
        foreach ($profile as $val) {
            $prof = $val['profile'];
            if (!isset($callChildProf[$val['child']])) {
                foreach ($field as $f) {
                    $callChildProf[$val['child']][$f] = $prof[$f];
                }
            } else {
                foreach ($field as $f) {
                    $callChildProf[$val['child']][$f] += $prof[$f];
                }
            }

            if (!isset($callParentProf[$val['parent']])) {
                foreach ($field as $f) {
                    $callParentProf[$val['parent']][$f] = $prof[$f];
                }
            } else {
                foreach ($field as $f) {
                    $callParentProf[$val['parent']][$f] += $prof[$f];
                }
            }
        }
        $callProf = [];
        foreach ($callChildProf as $call => $prof) {
            foreach ($field as $f) {
                $callProf[$call]['call'] = $call;
                $callProf[$call][$f] = $prof[$f];
                if ($f != 'ct') {
                    $nf = sprintf('excl_%s', $f);
                    $callProf[$call][$nf] = isset($callParentProf[$call][$f]) ? $prof[$f] - $callParentProf[$call][$f]: $prof[$f];
                }
            }
        }
        return array_values($callProf);
    }

    public function normalProfileRate(array $profile, array $baseProf)
    {
        $field = [
            'wt_rate' => ['wt', 'cost_time'],
            'excl_wt_rate' => ['excl_wt', 'cost_time'],
            'cpu_rate' => ['cpu', 'cpu_time'],
            'excl_cpu_rate' => ['excl_cpu', 'cpu_time'],
            'mu_rate' => ['mu', 'memory'],
            'excl_mu_rate' => ['excl_mu', 'memory'],
            'pmu_rate' => ['pmu', 'pmemory'],
            'excl_pmu_rate' => ['excl_pmu', 'pmemory'],
        ];
        foreach ($profile as $key => $prof) {
            foreach ($field as $k => $f) {
                if ($baseProf[$f[1]]) {
                    $profile[$key][$k] = sprintf("%.2f", $prof[$f[0]]/$baseProf[$f[1]] * 100);
                } else {
                    $profile[$key][$k] = '0.00';
                }
            }
        }
        return $profile;
    }

    public function profileExclWallTimeList(array $profile)
    {
        $exclWtList = [];
        foreach ($profile as $key => $prof) {
            $exclWtList[] = $prof['excl_wt'];
        }
        arsort($exclWtList);
        $exclWtList = array_values($exclWtList);
        
        return $exclWtList;
    }

    public function reduceNormalProfile(array $profile, $minExclTime)
    {
        foreach ($profile as $key => $prof) {
            if ($prof['excl_wt'] < $minExclTime) {
                unset($profile[$key]);
                continue;
            }
        }
        return $profile;
    }
}

