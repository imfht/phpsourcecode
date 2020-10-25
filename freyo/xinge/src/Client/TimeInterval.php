<?php

namespace Freyo\Xinge\Client;

class TimeInterval
{
    private $m_startHour;
    private $m_startMin;
    private $m_endHour;
    private $m_endMin;

    public function __construct($startHour, $startMin, $endHour, $endMin)
    {
        $this->m_startHour = $startHour;
        $this->m_startMin = $startMin;
        $this->m_endHour = $endHour;
        $this->m_endMin = $endMin;
    }

    public function __destruct()
    {
    }

    public function toArray()
    {
        return [
            'start' => ['hour' => strval($this->m_startHour), 'min' => strval($this->m_startMin)],
            'end'   => ['hour' => strval($this->m_endHour), 'min' => strval($this->m_endMin)],
        ];
    }

    public function isValid()
    {
        if (!is_int($this->m_startHour) || !is_int($this->m_startMin) ||
            !is_int($this->m_endHour) || !is_int($this->m_endMin)
        ) {
            return false;
        }

        if ($this->m_startHour >= 0 && $this->m_startHour <= 23 &&
            $this->m_startMin >= 0 && $this->m_startMin <= 59 &&
            $this->m_endHour >= 0 && $this->m_endHour <= 23 &&
            $this->m_endMin >= 0 && $this->m_endMin <= 59
        ) {
            return true;
        } else {
            return false;
        }
    }
}
