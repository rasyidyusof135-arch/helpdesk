<?php
include 'db_connect.php';

// Dynamically generate the base URL to support both localhost and Railway
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$dir = dirname($_SERVER['PHP_SELF']);
$dir = ($dir === '\\' || $dir === '/') ? '' : $dir;

$base_url = $protocol . "://" . $host . $dir . "/report.php?pc_id=";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print QR Codes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS untuk paparan Grid Sticker */
        .qr-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 sticker sebaris */
            gap: 20px;
            margin-top: 20px;
        }
        .qr-card {
            border: 2px dashed #333;
            padding: 15px;
            text-align: center;
            page-break-inside: avoid; /* Jangan potong sticker bila print */
        }
        .qr-img {
            width: 150px;
            height: 150px;
        }
        
        /* Bila tekan Print, sembunyikan butang, tunjuk sticker je */
        @media print {
            .no-print { display: none; }
            .qr-card { border: 1px solid #000; }
        }
    </style>
</head>
<body class="bg-light">

<div class="container mb-5">
    
    <div class="d-flex justify-content-between align-items-center mt-4 no-print">
        <h3>Asset QR Stickers</h3>
        <div>
            <a href="dashboard.php" class="btn btn-secondary me-2">Back to Dashboard</a>
            <button onclick="window.print()" class="btn btn-primary">Print Stickers</button>
        </div>
    </div>

    <div class="alert alert-info mt-3 no-print">
    
    <div class="qr-container">
        <?php
        $sql = "SELECT * FROM assets";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $pc_name = $row['pc_name'];
                $pc_code = $row['qr_code_string'];
                
                // Ini link sebenar yang akan masuk dalam QR
                $full_url = $base_url . $pc_code;
                
                // Kita guna API 'qrserver.com' untuk generate image secara live
                $api_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($full_url);

                echo "
                <div class='qr-card bg-white'>
                    <h5 class='mb-2'>$pc_name</h5>
                    <img src='$api_url' class='qr-img' alt='QR Code'>
                    <p class='mt-2 mb-0 text-muted small'>Scan to Report Issue</p>
                    <small class='fw-bold'>$pc_code</small>
                </div>
                ";
            }
        } else {
            echo "<p>No assets found in database.</p>";
        }
        ?>
    </div>

</div>

</body>
</html>