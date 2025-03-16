<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$connection = new AMQPStreamConnection($_ENV['RABBIT_HOST'], $_ENV['RABBIT_PORT'], $_ENV['RABBIT_USER'], $_ENV['RABBIT_PASSWORD']);
$channel = $connection->channel();

$channel->queue_declare('reservasi_queue', false, true, false, false);

for ($i = 1; $i <= 30000; $i++) {
    $data = json_encode([
        'user_id' => rand(1, 30000), // Simulasi user ID
        'event' => 'konser_rock'
    ]);

    $msg = new AMQPMessage($data, ['delivery_mode' => 2]); // Pesan persisten
    $channel->basic_publish($msg, '', 'reservasi_queue');

    echo "Sent order #$i\n";
}

$channel->close();
$connection->close();
