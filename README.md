# Reservasi Tiket dengan RabbitMQ dan PHP

Proyek ini mendemonstrasikan bagaimana cara mengimplementasikan sistem reservasi tiket menggunakan RabbitMQ dan PHP.

##  Fitur Utama

* Antrian pesan asinkron dengan RabbitMQ
* Pemrosesan pesanan yang efisien menggunakan consumer
* Monitoring status pesanan secara real-time melalui web interface

##  Instalasi

1.  **Clone repositori:**

    ```bash
    git clone [https://github.com/username/reservasi-tiket.git](https://github.com/username/reservasi-tiket.git)
    cd reservasi-tiket
    ```

2.  **Install dependencies Composer:**

    ```bash
    composer install
    ```

3.  **Instalasi dan konfigurasi RabbitMQ:**
    * Instal RabbitMQ sesuai dengan sistem operasi Anda.
    * Pastikan PHP dan MySQL sudah terinstall dan berjalan.

4.  **Konfigurasi environment (.env):**

    Buat file `.env` di root direktori proyek dan tambahkan konfigurasi berikut:

    ```ini
    RABBIT_HOST=localhost
    RABBIT_PORT=5672
    RABBIT_USER=faleh
    RABBIT_PASSWORD=faleh

    DB_HOST=localhost
    DB_NAME=reservasi
    DB_USER=root
    DB_PASSWORD=
    ```

5.  **Migrasi database:**

    ```bash
    php migrate.php
    ```

## ️ Cara Penggunaan

1.  **Jalankan producer (menghasilkan 30.000 pesanan):**

    ```bash
    php rabbit_producer.php
    ```

2.  **Jalankan consumer (memproses antrian pesanan):**

    ```bash
    php rabbit_consumer.php
    ```

3.  **Monitor status pesanan:**

    Buka `rabbit_index.php` di browser Anda untuk melihat status pesanan secara real-time.

## ⚠️ Catatan Penting

* Pastikan RabbitMQ berjalan sebelum menjalankan producer atau consumer.
* Periksa kembali konfigurasi di `.env` jika terjadi masalah koneksi database atau RabbitMQ.

##  Selamat Mencoba!

Jangan ragu untuk berkontribusi atau memberikan masukan untuk proyek ini.
