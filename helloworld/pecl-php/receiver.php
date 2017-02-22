<?php

$connection_params = require_once '../../config/config.php';

$connection = new AMQPConnection($connection_params);
$connection->connect();

$channel = new AMQPChannel($connection);

$exchange = new AMQPExchange($channel);
$exchange->setName('ex_hello');
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setFlags(AMQP_DURABLE);
$exchange->declare();

$queue = new AMQPQueue($channel);
$queue->setName('hello');
$queue->setFlags(AMQP_IFUNUSED | AMQP_AUTODELETE);
$queue->declare();

$queue->bind($exchange->getName(), 'foo_key');

while(true) {
    if ($envelope = $queue->get(AMQP_AUTOACK)) {
        $message = json_decode($envelope->getBody());
        print($message . "\r\n");
    }
}

$connection->disconnect();