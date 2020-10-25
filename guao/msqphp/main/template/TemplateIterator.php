<?php declare (strict_types = 1);
namespace msqphp\main\template;

interface TemplateIterator extends \Iterator
{
    public function __construct(string $input);
}
