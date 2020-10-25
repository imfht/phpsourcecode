<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Log;

if (interface_exists('\\Psr\\Log\\LoggerInterface', true)) {
    //class_alias()同样适用于interface和trait，但它不能用户系统定义的类或接口
    class_alias('\\Psr\\Log\\LoggerInterface', '\\Cute\\Log\\LoggerInterface', false);
} else {
    /**
     * Describes a logger instance.
     *
     * The message MUST be a string or object implementing __toString().
     *
     * The message MAY contain placeholders in the form: {foo} where foo
     * will be replaced by the context data in key "foo".
     *
     * The context array can contain arbitrary data. The only assumption that
     * can be made by implementors is that if an Exception instance is given
     * to produce a stack trace, it MUST be in a key named "exception".
     *
     * See https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
     * for the full interface specification.
     */
    interface LoggerInterface
    {
        /**
         * System is unusable.
         *
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function emergency($message, array $context = []);

        /**
         * Action must be taken immediately.
         *
         * Example: Entire website down, database unavailable, etc. This should
         * trigger the SMS alerts and wake you up.
         *
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function alert($message, array $context = []);

        /**
         * Critical conditions.
         *
         * Example: Application component unavailable, unexpected exception.
         *
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function critical($message, array $context = []);

        /**
         * Runtime errors that do not require immediate action but should typically
         * be logged and monitored.
         *
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function error($message, array $context = []);

        /**
         * Exceptional occurrences that are not errors.
         *
         * Example: Use of deprecated APIs, poor use of an API, undesirable things
         * that are not necessarily wrong.
         *
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function warning($message, array $context = []);

        /**
         * Normal but significant events.
         *
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function notice($message, array $context = []);

        /**
         * Interesting events.
         *
         * Example: User logs in, SQL logs.
         *
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function info($message, array $context = []);

        /**
         * Detailed debug information.
         *
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function debug($message, array $context = []);

        /**
         * Logs with an arbitrary level.
         *
         * @param mixed $level
         * @param string $message
         * @param array $context
         *
         * @return null
         */
        public function log($level, $message, array $context = []);
    }
}