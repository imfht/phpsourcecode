<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Log;

use \Cute\Log\LoggerInterface;


if (trait_exists('\\Psr\\Log\\LoggerAwareTrait', true)) {
    class_alias('\\Psr\\Log\\LoggerAwareTrait', '\\Cute\\Log\\LoggerAwareTrait', false);
} else {
    /**
     * Basic Implementation of LoggerAwareInterface.
     */
    trait LoggerAwareTrait
    {
        /**
         * The logger instance.
         *
         * @var LoggerInterface
         */
        protected $logger;

        /**
         * Sets a logger.
         *
         * @param LoggerInterface $logger
         */
        public function setLogger(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }
    }
}
