<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

// Membuat koneksi
$conn = new mysqli($servername, $username, $password);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Membuat database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database berhasil dibuat atau sudah ada.<br>";
} else {
    echo "Error membuat database: " . $conn->error;
}

// Pilih database
$conn->select_db($dbname);

// Membuat tabel kuota
$sql = "CREATE TABLE IF NOT EXISTS kuota (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(50) NOT NULL,
    jumlah INT(11) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabel kuota berhasil dibuat atau sudah ada.<br>";
} else {
    echo "Error membuat tabel kuota: " . $conn->error;
}

// Menambahkan data awal ke tabel kuota
$sql = "INSERT INTO kuota (id, event, jumlah) VALUES (1, 'konser_rock', 50000) ON DUPLICATE KEY UPDATE event=event";
if ($conn->query($sql) === TRUE) {
    echo "Data awal kuota berhasil dimasukkan atau sudah ada.<br>";
} else {
    echo "Error memasukkan data ke kuota: " . $conn->error;
}

// Membuat tabel reservasi
$sql = "CREATE TABLE IF NOT EXISTS reservasi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    event VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabel reservasi berhasil dibuat atau sudah ada.<br>";
} else {
    echo "Error membuat tabel reservasi: " . $conn->error;
}

// Menutup koneksi
$conn->close();
