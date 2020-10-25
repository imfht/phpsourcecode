<?php

namespace App\Traits;

use App\Models\SystemConfig;

trait SystemConfigTrait
{
    public function getSystemConfigFunction(array $flag)
    {
        $m_systemconfig = SystemConfig::whereIn('flag', $flag)->enableSearch('T')->select('id', 'flag', 'title', 'value')->get();
        foreach ($flag as $v) {
            $return[$v] = '';
        }
        if ($m_systemconfig) {
            foreach ($m_systemconfig as $k => $v) {
                $return[$v->flag] = $this->parseRule($v->flag, $v->value);
            }
        }
        return $return;
    }

    protected function parseRule($flag, $value)
    {
        switch ($flag) {
            case 'user_ids':
                $new_value = explode(',', $value);
                break;
            default:
                $new_value = $value;
                break;
        }
        return $new_value;
    }
}
