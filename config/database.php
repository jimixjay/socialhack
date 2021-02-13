<?php

$DATABASE_URL = parse_url(getenv("DATABASE_URL"));

$psql = [
    'driver' => 'pgsql',
    'host' => $DATABASE_URL["host"],
    'port' => $DATABASE_URL["port"],
    'database' => ltrim($DATABASE_URL["path"], "/"),
    'username' => $DATABASE_URL["user"],
    'password' => $DATABASE_URL["pass"],
    'charset' => 'utf8',
    'prefix' => '',
    'schema' => 'public'
];

if (getenv("DATABASE_SSL")) {
    $psql['sslmode'] = 'require';
}

return [
    'default' => env('DB_CONNECTION', 'pgsql'),
    'connections' => [
        'pgsql' => $psql,

    ],
    'migrations' => 'migrations'
];