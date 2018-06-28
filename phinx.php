<?php
return [
    "paths" => [
        "migrations" => "./Applications/app/Db/migrations",
        "seeds" => "./Applications/app/Db/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database" => "production",
        "production" => [
            "adapter" => "mysql",
            "host" => isset($_SERVER['PHINX_DBHOST']) ? $_SERVER['PHINX_DBHOST'] : '172.17.0.1',
            "name" => isset($_SERVER['PHINX_DBNAME']) ? $_SERVER['PHINX_DBNAME'] : 'shop_ws',
            "user" => isset($_SERVER['PHINX_DBUSER']) ? $_SERVER['PHINX_DBUSER'] :'root',
            "pass" => isset($_SERVER['PHINX_DBPASS']) ? $_SERVER['PHINX_DBPASS'] :'123456',
            "port" => isset($_SERVER['PHINX_DBPORT']) ? $_SERVER['PHINX_DBPORT'] : 3306
        ]
    ]
];
