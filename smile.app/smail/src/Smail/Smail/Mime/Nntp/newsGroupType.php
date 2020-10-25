<?php
namespace Smail\Mime\Nntp;

class newsGroupType
{

    private $name;

    private $description;

    private $count;

    private $text;

    public function __construct()
    {}

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_description($description)
    {
        $this->description = $description;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function set_count($count)
    {
        $this->count = $count;
    }

    public function get_count()
    {
        return $this->count;
    }

    public function set_text($text)
    {
        $this->text = $text;
    }

    public function get_text()
    {
        return $this->text;
    }
}