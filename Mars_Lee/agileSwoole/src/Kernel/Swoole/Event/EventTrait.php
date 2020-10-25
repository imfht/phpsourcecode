<?php


namespace Kernel\Swoole\Event;


trait EventTrait
{
        protected $callback = null;
        protected $params = [];
        public function setEventCall(\Closure $closure = null, array $params = [])
        {
                $this->callback = $closure;
                $this->params = $params;
                return $this;
        }

        public function doClosure()
        {
                if($this->callback != null) {
                        call_user_func_array($this->callback, $this->params);
                }
                return $this;
        }
}