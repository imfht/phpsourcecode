<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Log;

if (class_exists('\\Psr\\Log\\LogLevel', true)) {
    class_alias('\\Psr\\Log\\LogLevel', '\\Cute\\Log\\LogLevel', false);
} else {
    /**
     * Describes log levels.
     */
    class LogLevel
    {
        const EMERGENCY = 'emergency';
        const ALERT     = 'alert';
        const CRITICAL  = 'critical';
        const ERROR     = 'error';
        const WARNING   = 'warning';
        const NOTICE    = 'notice';
        const INFO      = 'info';
        const DEBUG     = 'debug';
    }
}