<?php

namespace Input;

interface InputBase
{
    public function getOne($item_name);

    public function getMany();

    public function getAll();

    public function setParams($params);
}