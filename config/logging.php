<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
        'stdout' => [
            'driver' => 'single',
            'path' => 'php://stdout',
            'level' => 'debug',
        ],

        'discord' => [
            'error' => [
                'driver' => 'custom',
                'via'    => MarvinLabs\DiscordLogger\Logger::class,
                'level'  => 'debug',
                'url'    => env('LOG_DISCORD_WEBHOOK_ERROR_URL', 'https://discord.com/api/webhooks/1306116665158664212/hBTGUU0HcbaxZgaJ5bXJfI0vidD9KDtWQfSeVhQuCgpqR0JPylvemAG3OFXVV75CQid7'),
            ],
//            'confirm_order' => [
//                'driver' => 'custom',
//                'via'    => MarvinLabs\DiscordLogger\Logger::class,
//                'level'  => 'info',
//                'url'    => env('LOG_DISCORD_WEBHOOK_CONFIRM_ORDER_URL', 'https://discord.com/api/webhooks/1306116876614635551/UGv_JwNIluT9lQ0k1n0Rs0AtuhBTZcI8VWQykzt_jc9hzUPKkWmRWxsnxk83aPWv3RfS'),
//            ],
//            'new_order' => [
//                'driver' => 'custom',
//                'via'    => MarvinLabs\DiscordLogger\Logger::class,
//                'level'  => 'info',
//                'url'    => env('LOG_DISCORD_WEBHOOK_NEW_ORDER_URL', 'https://discord.com/api/webhooks/1306116742308958260/wIhgEjaaLPefykH37MANerz0wY36ihNDwH0YRZM-Y05xdlo1PnnpubVtUVBmNWsCPSJY'),
//            ],
//            'release_ticket' => [
//                'driver' => 'custom',
//                'via'    => MarvinLabs\DiscordLogger\Logger::class,
//                'level'  => 'info',
//                'url'    => env('LOG_DISCORD_WEBHOOK_RELEASE_TICKET_URL', 'https://discord.com/api/webhooks/1306116935779614731/iylCy9BGlKIRzJ1mY1t-dMAZRJa9ZOO6FETaMk6s2TP_MaPL7xjNygbPHF4J0rLC93TI'),
//            ],
//            'seat_error' => [
//                'driver' => 'custom',
//                'via'    => MarvinLabs\DiscordLogger\Logger::class,
//                'level'  => 'info',
//                'url'    => env('LOG_DISCORD_WEBHOOK_SEAT_URL', 'https://discord.com/api/webhooks/1309750247035109467/PGnEjHnhbfo-uiDhMpNKj8N20YzZ-4qtPvkcozXNvxudFGe684fDbHcy3Q-K9mKNLGxa'),
//            ],
        ],
    ],

];
