<?php

namespace Freyo\Xinge\Client;

class ClickAction
{
    const TYPE_ACTIVITY = 1;
    const TYPE_URL = 2;
    const TYPE_INTENT = 3;

    private $m_actionType;
    private $m_url;
    private $m_confirmOnUrl;
    private $m_activity;
    private $m_intent;
    private $m_atyAttrIntentFlag;
    private $m_atyAttrPendingIntentFlag;
    private $m_packageDownloadUrl;
    private $m_confirmOnPackageDownloadUrl;
    private $m_packageName;

    /**
     * 动作类型.
     *
     * @param int $actionType 1打开activity或app本身，2打开url，3打开Intent
     */
    public function __construct()
    {
        $this->m_atyAttrIntentFlag = 0;
        $this->m_atyAttrPendingIntentFlag = 0;
        $this->m_confirmOnPackageDownloadUrl = 1;
    }

    public function setActionType($actionType)
    {
        $this->m_actionType = $actionType;
    }

    public function setUrl($url)
    {
        $this->m_url = $url;
    }

    public function setComfirmOnUrl($comfirmOnUrl)
    {
        $this->m_confirmOnUrl = $comfirmOnUrl;
    }

    public function setActivity($activity)
    {
        $this->m_activity = $activity;
    }

    public function setIntent($intent)
    {
        $this->m_intent = $intent;
    }

    public function setAtyAttrIntentFlag($atyAttrIntentFlag)
    {
        $this->m_atyAttrIntentFlag = $atyAttrIntentFlag;
    }

    public function setAtyAttrPendingIntentFlag($atyAttrPendingIntentFlag)
    {
        $this->m_atyAttrPendingIntentFlag = $atyAttrPendingIntentFlag;
    }

    public function setPackageDownloadUrl($packageDownloadUrl)
    {
        $this->m_packageDownloadUrl = $packageDownloadUrl;
    }

    public function setConfirmOnPackageDownloadUrl($confirmOnPackageDownloadUrl)
    {
        $this->m_confirmOnPackageDownloadUrl = $confirmOnPackageDownloadUrl;
    }

    public function setPackageName($packageName)
    {
        $this->m_packageName = $packageName;
    }

    public function toJson()
    {
        $ret = [];
        $ret['action_type'] = $this->m_actionType;
        $ret['browser'] = ['url' => $this->m_url, 'confirm' => $this->m_confirmOnUrl];
        $ret['activity'] = $this->m_activity;
        $ret['intent'] = $this->m_intent;

        $aty_attr = [];
        if (isset($this->m_atyAttrIntentFlag)) {
            $aty_attr['if'] = $this->m_atyAttrIntentFlag;
        }
        if (isset($this->m_atyAttrPendingIntentFlag)) {
            $aty_attr['pf'] = $this->m_atyAttrPendingIntentFlag;
        }
        $ret['aty_attr'] = $aty_attr;

        return $ret;
    }

    public function isValid()
    {
        if (!isset($this->m_actionType)) {
            $this->m_actionType = self::TYPE_ACTIVITY;
        }
        if (!is_int($this->m_actionType)) {
            return false;
        }
        if ($this->m_actionType < self::TYPE_ACTIVITY || $this->m_actionType > self::TYPE_INTENT) {
            return false;
        }

        if ($this->m_actionType == self::TYPE_ACTIVITY) {
            if (!isset($this->m_activity)) {
                $this->m_activity = '';

                return true;
            }
            if (isset($this->m_atyAttrIntentFlag)) {
                if (!is_int($this->m_atyAttrIntentFlag)) {
                    return false;
                }
            }
            if (isset($this->m_atyAttrPendingIntentFlag)) {
                if (!is_int($this->m_atyAttrPendingIntentFlag)) {
                    return false;
                }
            }

            if (is_string($this->m_activity) && !empty($this->m_activity)) {
                return true;
            }

            return false;
        }

        if ($this->m_actionType == self::TYPE_URL) {
            if (is_string($this->m_url) && !empty($this->m_url) &&
                is_int($this->m_confirmOnUrl) &&
                $this->m_confirmOnUrl >= 0 && $this->m_confirmOnUrl <= 1
            ) {
                return true;
            }

            return false;
        }

        if ($this->m_actionType == self::TYPE_INTENT) {
            if (is_string($this->m_intent) && !empty($this->m_intent)) {
                return true;
            }

            return false;
        }
    }
}
