<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

use Dotenv\Dotenv;

header('Content-Type: application/json');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


$pdo = new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

// Ambil total kuota
$stmt = $pdo->prepare("SELECT jumlah FROM kuota WHERE event = 'konser_rock'");
$stmt->execute();
$total_kuota = $stmt->fetchColumn();

// Hitung total reservasi yang sudah ter-booked
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservasi WHERE event = 'konser_rock'");
$stmt->execute();
$total_reserved = $stmt->fetchColumn();

// Koneksi ke RabbitMQ

$connection = new AMQPStreamConnection($_ENV['RABBIT_HOST'], $_ENV['RABBIT_PORT'], $_ENV['RABBIT_USER'], $_ENV['RABBIT_PASSWORD']);
$channel = $connection->channel();

// Dapatkan jumlah pesan di antrean 'reservasi_queue'
$queue_status = $channel->queue_declare('reservasi_queue', true);
$total_queue = $queue_status[1]; // Indeks 1 adalah jumlah pesan yang ada di antrean

// Tutup koneksi RabbitMQ
$channel->close();
$connection->close();

// Kirim data sebagai JSON
echo json_encode([
    'total_kuota' => $total_kuota,
    'total_reserved' => $total_reserved,
    'queue' => $queue_status,
    'total_queue' => $total_queue
]);
