<?php

function iconvEcho($content = '')
{
    echo iconv("GB2312", "UTF-8", $content);
}