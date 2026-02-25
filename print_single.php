<?php
session_start();
include 'db_connect.php';

// GATEKEEPER
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Dapatkan Info PC
$sql_asset = "SELECT * FROM assets WHERE id = '$id'";
$res_asset = $conn->query($sql_asset);
$pc = $res_asset->fetch_assoc();

if(!$pc) { die("Asset not found."); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report: <?php echo $pc['pc_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #fff; color: #000; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-weight: 700; text-transform: uppercase; }
        .info-table th { width: 150px; background-color: #f0f0f0; }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4 no-print">
        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Laporan Ini</button>
    </div>

    <div class="header">
        <h1>RBY TECH SDN BHD</h1>
        <p class="mb-0">Laporan Kerosakan Aset Individu (Individual Asset Report)</p>
        <small>Tarikh Cetakan: <?php echo date("d F Y"); ?></small>
    </div>

    <h4 class="fw-bold mb-3">Butiran Aset</h4>
    <table class="table table-bordered mb-5">
        <tr>
            <th>Nama Aset</th>
            <td><?php echo $pc['pc_name']; ?></td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td><?php echo $pc['location']; ?></td>
        </tr>
        <tr>
            <th>ID Aset (QR)</th>
            <td><?php echo $pc['qr_code_string']; ?></td>
        </tr>
    </table>

    <h4 class="fw-bold mb-3">Rekod Sejarah Kerosakan</h4>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Tarikh Aduan</th>
                <th>Pelapor</th>
                <th>Masalah</th>
                <th>Status</th>
                <th>Catatan Juruteknik</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_hist = "SELECT * FROM tickets WHERE asset_id = '$id' ORDER BY created_at DESC";
            $res_hist = $conn->query($sql_hist);

            if ($res_hist->num_rows > 0) {
                while($row = $res_hist->fetch_assoc()) {
                    $date = date("d/m/Y", strtotime($row['created_at']));
                    echo "<tr>
                            <td>$date</td>
                            <td>{$row['reporter_name']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['admin_remarks']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Tiada rekod kerosakan. Aset dalam keadaan baik.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="mt-5 pt-5 text-end">
        <p>Disahkan Oleh:</p>
        <br><br>
        <p>_______________________<br>Ketua Jabatan IT</p>
    </div>

</div>

</body>
</html>