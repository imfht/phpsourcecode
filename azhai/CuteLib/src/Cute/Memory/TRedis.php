<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Memory;

/**
 * Redis
 */
trait TRedis
{

    public function incre($key, $step = 1)
    {
        $step = abs($step);
        if ($step === 1) {
            return parent::incre($key);
        } else {
            return $this->increBy($key, $step);
        }
    }

    public function decre($key, $step = 1)
    {
        $step = abs($step);
        if ($step === 1) {
            return parent::incre($key);
        } else {
            return $this->increBy($key, $step);
        }
    }

}
