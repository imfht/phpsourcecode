<?php

namespace Kernel\Core\IComponent;



interface IConnectionPool
{
        const CONNECTION_STATUS_BUSY = 1000;
        const CONNECTION_STATUS_FREE = 1001;
        public function init():IConnectionPool;
        public function addConnection(IConnection $connection);
        public function getConnection() : IConnection;
}