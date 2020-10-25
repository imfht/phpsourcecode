<?php
namespace Cute\Contrib\Command;

use \Cute\Shell\Command;
use \Cute\Utility\Word;


class WordCommand extends Command
{
    public function execute()
    {
        $action = strval(reset($this->args));
        $args = array_slice($this->args, 1);
        return exec_method_array($this, $action, $args);
    }

    public function md5($x = '')
    {
        $this->app->writeln('md5("%s") : "%s"', $x, md5($x));
    }

    public function randString($length = 6)
    {
        $string = Word::randString($length);
        $this->app->writeln($string);
    }
}


