<?php
namespace Kernel;

use InvalidArgumentException;

class Response
{
    protected $content;
    public function content($content)
    {
        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([
            $content,
            '__toString',
        ])){
            throw new InvalidArgumentException(sprintf('variable type error： %s', gettype($content)));
        }

        $this->content = (string) $content;
        return $this;
    }
    public function send()
    {
        echo $this->content;

        return true;
    }
}