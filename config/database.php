<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => "-----BEGIN CERTIFICATE-----
MIIEQTCCAqmgAwIBAgIUZvbtaua+YmhyyLqAGjLnqdh2j4IwDQYJKoZIhvcNAQEM
BQAwOjE4MDYGA1UEAwwvNzUxMDQ2OTctNTdlZS00MDI2LWIwODAtMTFhYjIyNzhi
NjU4IFByb2plY3QgQ0EwHhcNMjQwNzEzMDUzNzU2WhcNMzQwNzExMDUzNzU2WjA6
MTgwNgYDVQQDDC83NTEwNDY5Ny01N2VlLTQwMjYtYjA4MC0xMWFiMjI3OGI2NTgg
UHJvamVjdCBDQTCCAaIwDQYJKoZIhvcNAQEBBQADggGPADCCAYoCggGBANWGE7zV
4vdKVWih0fwCOycZB5ZrXvD1Omr5ixGY32rFNtSFTcRBbl7TqfmxEO5JS5WHw1Ni
UWxyP/n807SNNAIf+VGiu8TOG+VQJ0qD3UcMp2PmjG93bcrAB5mGv/Q/EB3HeTH3
BuqKkgSEz2LdAYKDHjRbZ5VZsYkt+49AyANqG1bZDEM+K3Xw0oJanxKjDNfvHP93
X39dQmu2+OnohM2anHBKsJg8e3G6YPmZ9d59cM5CLufZpDjLoYbY5JwFkMS0Bu43
o/aRo4rNvLFrEARToBpIS4acmTGyhVsQXvwYyhdbndqtRVfgyIpLizvxNzfXTXjh
Qm8jgwVNfkPICzKrcV+7T/xyBhzPAI6QqRI0BO1cVy3RSRsN5tCtxneKCNQ/94M9
65vDCGwy4PTcp7Z7WRSmGyHKmpuSgnMBHWwb+F4NLz+s1A4hl2dAmnWCQ5jFVNOU
tEt9aHFCgmv8/4TmB4/OKZxNKE5Z1z+VwwTAgtDSQL7Z7Td7SR4xWtr7BQIDAQAB
oz8wPTAdBgNVHQ4EFgQUBmBGU9ljyONUKsUIxHdlS8w1lkgwDwYDVR0TBAgwBgEB
/wIBADALBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQEMBQADggGBAMptLWZ/8G5y2ys4
Iih7l9BEvNCtcjpuRN3AO9OtydTNjSUaV3Vtgl31HiZBRPAMqC+s5MIBzi/ZuvZo
+rMswxxkM/V8F0orIPy+DCN3df4swzjsxpAFaCATdUEg/gXN6wujxgath7QTEUeF
7qyd0PhCahMAGT8zdTtSyaoMyJSveTWWIYKc3oetEaKKmvp40DATuHY7OKNQAaqI
eao/yC2VV2UqqcftyTBBB9HwxqxltbbpTm5ir4pd6qyBnxTHpa24jY2/7akW9Vuu
p7U4r2ky5djKO7H3PUh0Wkpba87nNzTutL3lXaj9fsOYWLFLfO3NaIoHIr6rXazu
koVLwND08DHbnB6wHntkKo/xzUPkDz9guc3kUUjtBogJ5KqH+ZLUnpYwXc/KA8g6
AkDEeC5UPaj7fMHKTah9mjT3btZ6R+CgE+MWv36QJwaqYud0RISReKExICM9S9Mg
iYLE3jD5tk2ARcDM8X0FgExIbz0KHztjnRQZFytyN3l31dZVAA==
-----END CERTIFICATE-----",
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => parse_url(env('REDIS_URL'), PHP_URL_HOST),
            'port' => parse_url(env('REDIS_URL'), PHP_URL_PORT),
            'password' => parse_url(env('REDIS_URL'), PHP_URL_PASS),
            'scheme' => parse_url(env('REDIS_URL'), PHP_URL_SCHEME),
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
