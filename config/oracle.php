<?php

return [
    'oracle' => [
        'driver'         => 'oracle',
        'tns'            => env('DB_TNS', ''),
        'host'           => env('DB_HOST', '172.23.50.95'),
        'port'           => env('DB_PORT', '1521'),
        'database'       => env('DB_DATABASE', 'CATGIS'),
        'username'       => env('DB_USERNAME', 'SGMUNI'),
        'password'       => env('DB_PASSWORD', 'sgadmin'),
        'NLS_DATE_FORMAT' => 'DD/MM/YYYY HH24:MI:SS',
        'NLS_TIME_FORMAT' => 'HH24:MI:SS', 
        'NLS_TIMESTAMP_FORMAT' => 'DD/MM/YYYY HH24:MI:SS'
    ],
];
