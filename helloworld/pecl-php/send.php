<?php

$connection_params = require_once '../../config/config.php';

$connection = new AMQPConnection($connection_params);
$connection->connect();

$channel = new AMQPChannel($connection);

// Создание обменника
$exchange = new AMQPExchange($channel);
$exchange->setName('ex_hello');
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setFlags(AMQP_DURABLE);
$exchange->declare();

// Создание очереди
$queue = new AMQPQueue($channel);
$queue->setName('hello');
$queue->setFlags(AMQP_IFUNUSED | AMQP_AUTODELETE);
$queue->declare();

$result = $exchange->publish(json_encode("Hello world!"), "foo_key");

echo "Отправлено!\r\n";

$connection->disconnect();


