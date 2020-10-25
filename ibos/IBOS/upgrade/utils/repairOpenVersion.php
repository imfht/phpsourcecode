<?php
function repairOpenVersion()
{
    updateVersion("4.2.2 open");
}

$version = strtolower(VERSION . " " .VERSION_TYPE);

if ($version == "4.4.2 open") {
    repairOpenVersion();
}