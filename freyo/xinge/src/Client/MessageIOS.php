<?php

namespace Freyo\Xinge\Client;

class MessageIOS
{
    const TYPE_APNS_NOTIFICATION = 11;
    const TYPE_REMOTE_NOTIFICATION = 12;
    const MAX_LOOP_TASK_DAYS = 15;

    private $m_expireTime;
    private $m_sendTime;
    private $m_acceptTimes;
    private $m_custom;
    private $m_raw;
    private $m_type;
    private $m_alert;
    private $m_badge;
    private $m_sound;
    private $m_category;
    private $m_loopInterval;
    private $m_loopTimes;

    public function __construct()
    {
        $this->m_acceptTimes = [];
        $this->m_type = self::TYPE_APNS_NOTIFICATION;
    }

    public function __destruct()
    {
    }

    public function setExpireTime($expireTime)
    {
        $this->m_expireTime = $expireTime;
    }

    public function getExpireTime()
    {
        return $this->m_expireTime;
    }

    public function setSendTime($sendTime)
    {
        $this->m_sendTime = $sendTime;
    }

    public function getSendTime()
    {
        return $this->m_sendTime;
    }

    public function addAcceptTime($acceptTime)
    {
        $this->m_acceptTimes[] = $acceptTime;
    }

    public function setCustom($custom)
    {
        $this->m_custom = $custom;
    }

    public function setRaw($raw)
    {
        $this->m_raw = $raw;
    }

    public function setAlert($alert)
    {
        $this->m_alert = $alert;
    }

    public function setBadge($badge)
    {
        $this->m_badge = $badge;
    }

    public function setSound($sound)
    {
        $this->m_sound = $sound;
    }

    /**
     * 消息类型.
     *
     * @param int $type 1：通知 2：静默通知
     */
    public function setType($type)
    {
        $this->m_type = $type;
    }

    public function getType()
    {
        return $this->m_type;
    }

    public function getCategory()
    {
        return $this->m_category;
    }

    public function setCategory($category)
    {
        $this->m_category = $category;
    }

    public function getLoopInterval()
    {
        return $this->m_loopInterval;
    }

    public function setLoopInterval($loopInterval)
    {
        $this->m_loopInterval = $loopInterval;
    }

    public function getLoopTimes()
    {
        return $this->m_loopTimes;
    }

    public function setLoopTimes($loopTimes)
    {
        $this->m_loopTimes = $loopTimes;
    }

    public function toJson()
    {
        if (!empty($this->m_raw)) {
            return $this->m_raw;
        }
        $ret = $this->m_custom;
        $ret['accept_time'] = $this->acceptTimeToJson();

        $aps = [];
        if ($this->m_type == self::TYPE_APNS_NOTIFICATION) {
            $aps['alert'] = $this->m_alert;
            if (isset($this->m_badge)) {
                $aps['badge'] = $this->m_badge;
            }
            if (isset($this->m_sound)) {
                $aps['sound'] = $this->m_sound;
            }
            if (isset($this->m_category)) {
                $aps['category'] = $this->m_category;
            }
        } elseif ($this->m_type == self::TYPE_REMOTE_NOTIFICATION) {
            $aps['content-available'] = 1;
        }
        $ret['aps'] = $aps;

        return json_encode($ret);
    }

    public function acceptTimeToJson()
    {
        $ret = [];
        foreach ($this->m_acceptTimes as $acceptTime) {
            $ret[] = $acceptTime->toArray();
        }

        return $ret;
    }

    public function isValid()
    {
        if (isset($this->m_expireTime)) {
            if (!is_int($this->m_expireTime) || $this->m_expireTime > 3 * 24 * 60 * 60) {
                return false;
            }
        } else {
            $this->m_expireTime = 0;
        }

        if (isset($this->m_sendTime)) {
            if (strtotime($this->m_sendTime) === false) {
                return false;
            }
        } else {
            $this->m_sendTime = '2014-03-13 12:00:00';
        }

        if (!empty($this->m_raw)) {
            if (is_string($this->m_raw)) {
                return true;
            } else {
                return false;
            }
        }
        if (!is_int($this->m_type) || $this->m_type < self::TYPE_APNS_NOTIFICATION || $this->m_type > self::TYPE_REMOTE_NOTIFICATION) {
            return false;
        }

        foreach ($this->m_acceptTimes as $value) {
            if (!($value instanceof TimeInterval) || !$value->isValid()) {
                return false;
            }
        }

        if (isset($this->m_custom)) {
            if (!is_array($this->m_custom)) {
                return false;
            }
        } else {
            $this->m_custom = [];
        }
        if ($this->m_type == self::TYPE_APNS_NOTIFICATION) {
            if (!isset($this->m_alert)) {
                return false;
            }
            if (!is_string($this->m_alert) && !is_array($this->m_alert)) {
                return false;
            }
        }
        if (isset($this->m_badge)) {
            if (!is_int($this->m_badge)) {
                return false;
            }
        }
        if (isset($this->m_sound)) {
            if (!is_string($this->m_sound)) {
                return false;
            }
        }
        if (isset($this->m_loopInterval)) {
            if (!(is_int($this->m_loopInterval) && $this->m_loopInterval > 0)) {
                return false;
            }
        }
        if (isset($this->m_loopTimes)) {
            if (!(is_int($this->m_loopTimes) && $this->m_loopTimes > 0)) {
                return false;
            }
        }
        if (isset($this->m_loopInterval) && isset($this->m_loopTimes)) {
            if (($this->m_loopTimes - 1) * $this->m_loopInterval + 1 > self::MAX_LOOP_TASK_DAYS) {
                return false;
            }
        }

        return true;
    }
}
