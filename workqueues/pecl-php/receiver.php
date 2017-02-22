<?php

$connection_params = require_once '../../config/config.php';

$connection = new AMQPConnection();
$connection->connect();

$channel = new AMQPChannel($connection);

$exchange = new AMQPExchange($channel);
$exchange->setName('ex_acknowledgment');
$exchange->setType(AMQP_EX_TYPE_FANOUT);
$exchange->setFlags(AMQP_AUTODELETE);
$exchange->declare();

$queue = new AMQPQueue($channel);
$queue->setName('acknowledgment');
$queue->setFlags(AMQP_IFUNUSED | AMQP_AUTODELETE | AMQP_DURABLE);
$queue->declare();
$queue->bind($exchange->getName(), '');

while (true) {
    if ($envelope = $queue->get()) {
        $message = json_decode($envelope->getBody());
        echo "delivery tag: " . $envelope->getDeliveryTag() . PHP_EOL;
        if (doWork($message)) {
            $queue->ack($envelope->getDeliveryTag());
        } else {
            $queue->nack($envelope->getDelivaryTag(), AMQP_REQUEUE);
        }
    }
}

$connection->disconnect();

function doWork($message) {
    $sleep_interval = rand(1, 5);
    sleep($sleep_interval);
    print($message.str_repeat('.', $sleep_interval) . PHP_EOL);
    return true;
}