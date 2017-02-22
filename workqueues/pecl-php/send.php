<?php

$connection_params = require_once '../../config/config.php';

$connection = new AMQPConnection();
$connection->connect();

$channel = new AMQPChannel($connection);

$exchange = new AMQPExchange($channel);
$exchange->setName('ex_acknowledgment');
$exchange->setType(AMQP_EX_TYPE_FANOUT);
$exchange->setFlags(AMQP_IFUNUSED | AMQP_AUTODELETE);
$exchange->declare();

$queue = new AMQPQueue($channel);
$queue->setName('acknowledgment');
$queue->setFlags(AMQP_IFUNUSED | AMQP_AUTODELETE | AMQP_DURABLE);
$queue->declare();
$queue->bind($exchange->getName(), '');

$result = $exchange->publish(json_encode("acknowledgment!"), '');

if ($result)
    echo 'sent'.PHP_EOL;
else
    echo 'error'.PHP_EOL;

$connection->disconnect();