<?php
session_start();
include 'db_connect.php';

// GATEKEEPER: Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Dapatkan ID PC dari URL
$asset_id = isset($_GET['pc_id']) ? $_GET['pc_id'] : 0;

// 1. Dapatkan maklumat PC (Nama & Lokasi)
$sql_asset = "SELECT * FROM assets WHERE id = '$asset_id'";
$res_asset = $conn->query($sql_asset);
$pc_info = $res_asset->fetch_assoc();

// Jika PC tak wujud, tendang balik
if (!$pc_info) {
    echo "Asset not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History: <?php echo $pc_info['pc_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media screen and (max-width: 767px) {
            .table-responsive { border: none !important; }
            table thead { display: none; }
            table tbody tr {
                display: flex;
                flex-direction: column;
                background-color: #fff;
                margin-bottom: 15px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                padding: 15px;
                border: 1px solid #eaeaea;
            }
            table tbody td {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                border: none !important;
                padding: 10px 0 !important;
                border-bottom: 1px solid #f8f9fa !important;
                text-align: left;
                word-break: break-word;
            }
            table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #333;
                margin-bottom: 5px;
                text-transform: uppercase;
                font-size: 0.85em;
                letter-spacing: 0.5px;
            }
            table tbody td:last-child {
                border-bottom: none !important;
            }
            .h2-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-3 mt-md-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h2 class="h2-title mb-0">
            <span class="text-muted">History Log:</span> 
            <?php echo $pc_info['pc_name']; ?>
        </h2>
        <a href="dashboard.php" class="btn btn-secondary shadow-sm">Back</a>
    </div>

    <div class="card mb-4 border-info">
        <div class="card-body bg-white">
            <h5 class="card-title">Asset Details</h5>
            <p class="mb-0"><strong>Location:</strong> <?php echo $pc_info['location']; ?></p>
            <p class="mb-0"><strong>QR Code String:</strong> <?php echo $pc_info['qr_code_string']; ?></p>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            Past Issues & Solutions
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date Reported</th>
                        <th>Reporter</th>
                        <th>Issue Description</th>
                        <th>Technician Remarks (Solution)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 2. Dapatkan semua tiket berkaitan PC ini (Susun dari baru ke lama)
                    $sql_hist = "SELECT * FROM tickets WHERE asset_id = '$asset_id' ORDER BY created_at DESC";
                    $res_hist = $conn->query($sql_hist);

                    if ($res_hist->num_rows > 0) {
                        while($row = $res_hist->fetch_assoc()) {
                            // Format Tarikh supaya cantik (Hari-Bulan-Tahun)
                            $date = date("d M Y, h:i A", strtotime($row['created_at']));
                            
                            // Warna Status
                            $status_class = ($row['status'] == 'Closed') ? 'bg-success' : 'bg-warning text-dark';

                            echo "<tr>
                                    <td data-label='Date Reported'>$date</td>
                                    <td data-label='Reporter'>{$row['reporter_name']}</td>
                                    <td data-label='Issue'>{$row['description']}</td>
                                    <td data-label='Technician Remarks'><em class='text-muted'>{$row['admin_remarks']}</em></td>
                                    <td data-label='Status'><span class='badge $status_class'>{$row['status']}</span></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No history records found for this PC. It's a good machine!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>