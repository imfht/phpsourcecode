<?php

namespace plugins;

class Session
{
    public function beforeDispatch()
    {
        session_start(['cookie_lifetime' => 86400]);
    }
}