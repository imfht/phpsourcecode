<?php

namespace Freyo\Xinge\Client;

class Message
{
    const TYPE_NOTIFICATION = 1;
    const TYPE_MESSAGE = 2;
    const MAX_LOOP_TASK_DAYS = 15;

    private $m_title;
    private $m_content;
    private $m_expireTime;
    private $m_sendTime;
    private $m_acceptTimes;
    private $m_type;
    private $m_multiPkg;
    private $m_style;
    private $m_action;
    private $m_custom;
    private $m_raw;
    private $m_loopInterval;
    private $m_loopTimes;

    public function __construct()
    {
        $this->m_acceptTimes = [];
        $this->m_multiPkg = 0;
        $this->m_raw = '';
        $this->m_style = new Style(0);
        $this->m_action = new ClickAction();
    }

    public function __destruct()
    {
    }

    public function setTitle($title)
    {
        $this->m_title = $title;
    }

    public function setContent($content)
    {
        $this->m_content = $content;
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

    /**
     * 消息类型.
     *
     * @param int $type 1：通知 2：透传消息
     */
    public function setType($type)
    {
        $this->m_type = $type;
    }

    public function getType()
    {
        return $this->m_type;
    }

    public function setMultiPkg($multiPkg)
    {
        $this->m_multiPkg = $multiPkg;
    }

    public function getMultiPkg()
    {
        return $this->m_multiPkg;
    }

    public function setStyle($style)
    {
        $this->m_style = $style;
    }

    public function setAction($action)
    {
        $this->m_action = $action;
    }

    public function setCustom($custom)
    {
        $this->m_custom = $custom;
    }

    public function setRaw($raw)
    {
        $this->m_raw = $raw;
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
        $ret = [];
        if ($this->m_type == self::TYPE_NOTIFICATION) {
            $ret['title'] = $this->m_title;
            $ret['content'] = $this->m_content;
            $ret['accept_time'] = $this->acceptTimeToJson();
            $ret['builder_id'] = $this->m_style->getBuilderId();
            $ret['ring'] = $this->m_style->getRing();
            $ret['vibrate'] = $this->m_style->getVibrate();
            $ret['clearable'] = $this->m_style->getClearable();
            $ret['n_id'] = $this->m_style->getNId();

            if (!is_null($this->m_style->getRingRaw())) {
                $ret['ring_raw'] = $this->m_style->getRingRaw();
            }
            $ret['lights'] = $this->m_style->getLights();
            $ret['icon_type'] = $this->m_style->getIconType();
            if (!is_null($this->m_style->getIconRes())) {
                $ret['icon_res'] = $this->m_style->getIconRes();
            }
            $ret['style_id'] = $this->m_style->getStyleId();
            if (!is_null($this->m_style->getSmallIcon())) {
                $ret['small_icon'] = $this->m_style->getSmallIcon();
            }

            $ret['action'] = $this->m_action->toJson();
        } elseif ($this->m_type == self::TYPE_MESSAGE) {
            $ret['title'] = $this->m_title;
            $ret['content'] = $this->m_content;
            $ret['accept_time'] = $this->acceptTimeToJson();
        }
        $ret['custom_content'] = $this->m_custom;

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
        if (is_string($this->m_raw) && !empty($this->raw)) {
            return true;
        }
        if (!isset($this->m_title)) {
            $this->m_title = '';
        } elseif (!is_string($this->m_title) || empty($this->m_title)) {
            return false;
        }
        if (!isset($this->m_content)) {
            $this->m_content = '';
        } elseif (!is_string($this->m_content) || empty($this->m_content)) {
            return false;
        }
        if (!is_int($this->m_type) || $this->m_type < self::TYPE_NOTIFICATION || $this->m_type > self::TYPE_MESSAGE) {
            return false;
        }
        if (!is_int($this->m_multiPkg) || $this->m_multiPkg < 0 || $this->m_multiPkg > 1) {
            return false;
        }
        if ($this->m_type == self::TYPE_NOTIFICATION) {
            if (!($this->m_style instanceof Style) || !($this->m_action instanceof ClickAction)) {
                return false;
            }
            if (!$this->m_style->isValid() || !$this->m_action->isValid()) {
                return false;
            }
        }
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
            $this->m_sendTime = '2013-12-19 17:49:00';
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
