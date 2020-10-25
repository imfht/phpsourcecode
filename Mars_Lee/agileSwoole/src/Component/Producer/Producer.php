<?php
namespace Component\Producer;

use Kernel\AgileCore as Core;

class Producer
{
        const PRODUCER_PROCESS = 'process';
        const PRODUCER_SYNC = 'sync';
        const PRODUCER_TASK = 'task';

        public static function getProducer(string $type) : IProducer
        {
                switch ($type) {
                        case 'process':
                                $producer = Core::getInstance()->get(ProcessProducer::class);
                                break;
                        case 'sync':
                                $producer = Core::getInstance()->get(SyncProducer::class);
                                break;
                        case 'task':
                        default:
                                $producer = Core::getInstance()->get(TaskProducer::class);
                }
                return $producer;
        }
}