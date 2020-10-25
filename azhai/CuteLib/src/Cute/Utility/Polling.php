<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Utility;


/**
 * 轮询
 */
class Polling
{
    protected $channels = [];

    /**
     * 构造函数
     */
    public function __construct(array $channels = [])
    {
        $this->channels = $channels;
    }

    /**
     * 随机循环
     */
    public function randRoll()
    {
        static $index = 0;
        if ($index === 0) {
            shuffle($this->channels);
            $index = count($this->channels);
        }
        return $this->channels[--$index];
    }

    /**
     * 权重轮询
     */
    public function roundRobin(&$choice, $score = 1)
    {
        if (count($this->channels) < 2) { //单个不需要轮询
            return current($this->channels);
        }
        $best_choice = null;
        $max_value = 0;
        $round_total = 0;
        foreach ($this->channels as $id => $row) {
            $weight = $row['weight'] * $score;
            $round_total += $weight;
            $row['last_value'] += $weight;
            if (empty($choice) && $row['last_value'] >= $max_value) {
                $best_choice = $id;
                $max_value = $row['last_value'];
            }
        }
        if (!empty($choice)) {
            $this->channels[$choice]['last_value'] -= $round_total;
            return $this->channels[$choice];
        } else if (!is_null($best_choice)) {
            $this->channels[$best_choice]['last_value'] -= $round_total;
            return $this->channels[$best_choice];
        }
    }
}
