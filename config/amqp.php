<?php

return [
    'use' => 'default',
    'properties' => [
        'default' => [
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'username' => env('RABBITMQ_USERNAME', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),
            'vhost' => env('RABBITMQ_VHOST', '/'),
            'consumer_tag' => 'consumer',
            'ssl_options' => [], // See https://secure.php.net/manual/en/context.ssl.php
            'connect_options' => [], // See https://github.com/php-amqplib/php-amqplib/blob/master/PhpAmqpLib/Connection/AMQPSSLConnection.php
            'queue_properties' => [],
            'exchange_properties' => [],
            'timeout' => 0
        ],
    ],
];
