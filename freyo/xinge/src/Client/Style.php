<?php

namespace Freyo\Xinge\Client;

class Style
{
    private $m_builderId;
    private $m_ring;
    private $m_vibrate;
    private $m_clearable;
    private $m_nId;
    private $m_ringRaw;
    private $m_lights;
    private $m_iconType;
    private $m_iconRes;
    private $m_styleId;
    private $m_smallIcon;

    public function __construct($builderId, $ring = 0, $vibrate = 0, $clearable = 1, $nId = 0, $lights = 1, $iconType = 0, $styleId = 1)
    {
        $this->m_builderId = $builderId;
        $this->m_ring = $ring;
        $this->m_vibrate = $vibrate;
        $this->m_clearable = $clearable;
        $this->m_nId = $nId;
        $this->m_lights = $lights;
        $this->m_iconType = $iconType;
        $this->m_styleId = $styleId;
    }

    public function __destruct()
    {
    }

    public function getBuilderId()
    {
        return $this->m_builderId;
    }

    public function getRing()
    {
        return $this->m_ring;
    }

    public function getVibrate()
    {
        return $this->m_vibrate;
    }

    public function getClearable()
    {
        return $this->m_clearable;
    }

    public function getNId()
    {
        return $this->m_nId;
    }

    public function getLights()
    {
        return $this->m_lights;
    }

    public function getIconType()
    {
        return $this->m_iconType;
    }

    public function getStyleId()
    {
        return $this->m_styleId;
    }

    public function setRingRaw($ringRaw)
    {
        return $this->m_ringRaw = $ringRaw;
    }

    public function getRingRaw()
    {
        return $this->m_ringRaw;
    }

    public function setIconRes($iconRes)
    {
        return $this->m_iconRes = $iconRes;
    }

    public function getIconRes()
    {
        return $this->m_iconRes;
    }

    public function setSmallIcon($smallIcon)
    {
        return $this->m_smallIcon = $smallIcon;
    }

    public function getSmallIcon()
    {
        return $this->m_smallIcon;
    }

    public function isValid()
    {
        if (!is_int($this->m_builderId) || !is_int($this->m_ring) ||
            !is_int($this->m_vibrate) || !is_int($this->m_clearable) ||
            !is_int($this->m_lights) || !is_int($this->m_iconType) ||
            !is_int($this->m_styleId)
        ) {
            return false;
        }
        if ($this->m_ring < 0 || $this->m_ring > 1) {
            return false;
        }
        if ($this->m_vibrate < 0 || $this->m_vibrate > 1) {
            return false;
        }
        if ($this->m_clearable < 0 || $this->m_clearable > 1) {
            return false;
        }
        if ($this->m_lights < 0 || $this->m_lights > 1) {
            return false;
        }
        if ($this->m_iconType < 0 || $this->m_iconType > 1) {
            return false;
        }
        if ($this->m_styleId < 0 || $this->m_styleId > 1) {
            return false;
        }

        return true;
    }
}
