<?php
return array(
    "paths" => [
        "migrations" => "./migrations",
        "seeds" => "./seeds",
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database" => "development",
        "development" => [
            "adapter" => "mysql",
            "host" => "localhost",
            "name" => "test",
            "user" => "root",
            "pass" => "",
            "port" => "3306",
            "charset" => "utf8",
        ],
    ]
);