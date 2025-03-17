<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Real-time Antrian Reservasi</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .dashboard {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 400px;
      width: 100%;
    }
    .dashboard h1 {
      font-size: 24px;
      margin-bottom: 20px;
    }
    .stat {
      font-size: 18px;
      margin-bottom: 10px;
    }
    .stat span {
      font-weight: bold;
      color: #007bff;
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <h1>Status Antrian Reservasi</h1>
    <div class="stat">Total Kuota: <span id="total-kuota">0</span></div>
    <div class="stat">Sudah Ter-Booked: <span id="total-reserved">0</span></div>
    <div class="stat">Total Antrian: <span id="total-queue">0</span></div>
  </div>

  <script>
    // Fungsi untuk mengambil data status dari API
    async function fetchQueueStatus() {
      try {
        const response = await fetch('rabbit_status.php');
        const data = await response.json();

        // Update elemen dengan data terbaru
        document.getElementById('total-kuota').textContent = data.total_kuota;
        document.getElementById('total-reserved').textContent = data.total_reserved;
        document.getElementById('total-queue').textContent = data.total_queue;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    }

    // Panggil fungsi setiap 1 detik
    setInterval(fetchQueueStatus, 2000);

    // Panggil sekali untuk memuat data saat halaman dibuka
    fetchQueueStatus();
  </script>
</body>
</html>
