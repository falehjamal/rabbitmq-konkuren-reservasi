<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$connection = new AMQPStreamConnection($_ENV['RABBIT_HOST'], $_ENV['RABBIT_PORT'], $_ENV['RABBIT_USER'], $_ENV['RABBIT_PASSWORD']);
$channel = $connection->channel();

$channel->queue_declare('reservasi_queue', false, true, false, false);

// Prefetch 1 agar pesan diproses satu per satu (menghindari race condition)
$channel->basic_qos(null, 1, null);

echo "Waiting for messages...\n";

$callback = function ($msg) {
    $data = json_decode($msg->body, true);
    $user_id = $data['user_id'];
    $event = $data['event'];

    $pdo = new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    try {
        $pdo->beginTransaction();

        // Ambil stok dengan LOCK agar tidak terjadi race condition
        $stmt = $pdo->prepare("SELECT jumlah FROM kuota WHERE event = ? FOR UPDATE");
        $stmt->execute([$event]);
        $kuota = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kuota && $kuota['jumlah'] > 0) {
            // Kurangi kuota
            $stmt = $pdo->prepare("UPDATE kuota SET jumlah = jumlah - 1 WHERE event = ?");
            $stmt->execute([$event]);

            // Simpan reservasi
            $stmt = $pdo->prepare("INSERT INTO reservasi (user_id, event) VALUES (?, ?)");
            $stmt->execute([$user_id, $event]);

            echo "Reservasi berhasil untuk User ID: $user_id\n";
            $pdo->commit();
        } else {
            echo "Kuota habis untuk User ID: $user_id\n";
            $pdo->rollBack();
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage() . "\n";
    }

    $msg->ack(); // Konfirmasi pesan berhasil diproses
};

$channel->basic_consume('reservasi_queue', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
