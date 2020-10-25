<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Helper;


class DateRangeHelper
{

    /** @var DateHelper */
    protected $reference = null;

    /** @var DateHelper */
    protected $start = null;

    /** @var DateHelper */
    protected $end = null;

    public function __construct($start = null, $end = null, DateHelper $ref = null)
    {
        if (isset($ref))
            $this->setReference($ref);
        $this->setRange($start, $end);
    }

    public function setRange($start = null, $end = null)
    {
        if (!empty($start)) {
            if (!($start instanceof DateHelper)) {
                $start = new DateHelper($start);
                if (isset($this->reference))
                    $start->ref($this->reference);
            }
            $this->start = $start;
        }
        if (!empty($end)) {
            if (!($end instanceof DateHelper)) {
                $end = new DateHelper($end);
                if (isset($this->reference))
                    $end->ref($this->reference);
            }
            $this->end = $end;
        }
        if (isset($this->end) && isset($this->start)) {
            $startDate = $this->start->date();
            $endDate = $this->end->date();

            if ($endDate < $startDate) {
                $end = $this->end;
                $this->end = $this->start;
                $this->start = $end;
                // $this->start = $this->end;
                // $this->end = $end;
            }
            /*else if ($endDate === $startDate) {
                $this->end = $this->end->modify('1 days');
            }*/
        }
        return $this;
    }

    public function setReference(DateHelper $date)
    {
        $this->reference = $date;
        return $this;
    }

    public function start()
    {
        return $this->start;
    }

    public function end()
    {
        return $this->end;
    }

    public function limitDays(int $day)
    {
        $diff = ($this->end->date() - $this->start->date()) / 86400;
        if ($diff > $day) {
            $this->end = $this->start->modify("+{$day} days");
        }
        return $this;
    }

    public function limitEndInDays(int $days)
    {
        $days = abs($days);
        $diff = ($this->end->date() - $this->start->date()) / 86400;
        if ($diff > $days) {
            $this->start = $this->end->modify("-{$days} days");
        }
        return $this;
    }

    public function inRange(DateRangeHelper $range)
    {
        return $range->start->date() === $this->start->date() && $range->end->date() === $this->end->date();
    }

    public function toQuery(array $query = [])
    {
        $base = [
            'start' => $this->start->string(),
            'end' => $this->end->string(),
        ];
        return $base + $query;
    }

    public function cloneRange($start, $end)
    {
        $clone = clone $this;
        return $clone->setRange($start, $end);
    }

    public function isTheSameStartAndEnd()
    {
        return $this->start()->date() === $this->end()->date();
    }

    public function key()
    {
        return $this->start()->date() . '-' . $this->end()->date();
    }

    public function getDays()
    {
        $i = $this->start()->date();
        $max = $this->end()->date();
        $days = [];
        for (; $i <= $max; $i += 86400) {
            $days[] = $i;
        }
        return $days;
    }
}