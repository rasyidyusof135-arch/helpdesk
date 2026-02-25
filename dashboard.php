<?php 
session_start(); 
include 'db_connect.php'; 

// GATEKEEPER
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RBY Tech Sdn Bhd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- CSS BIASA --- */
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&auto=format&fit=crop&w=2034&q=80') no-repeat center center fixed;
            background-size: cover;
            box-shadow: inset 0 0 0 2000px rgba(0, 0, 0, 0.85);
            color: #e0e0e0;
            min-height: 100vh;
        }
        .navbar-custom {
            background: linear-gradient(to right, #000000, #1a1a1a) !important;
            border-bottom: 2px solid #0dcaf0;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
            color: #333;
        }
        .print-header { display: none; }

        /* --- RESPONSIVE TABLE (CARD STYLE FOR MOBILE) --- */
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
                justify-content: space-between;
                align-items: center;
                border: none;
                padding: 10px 0;
                border-bottom: 1px solid #f8f9fa;
                text-align: right;
            }
            table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #555;
                margin-right: 15px;
                text-align: left;
                flex-shrink: 0;
            }
            table tbody td:last-child {
                border-bottom: none;
                justify-content: flex-end;
                gap: 10px;
                padding-bottom: 0;
            }
            table tbody td:last-child::before { display: none; }
            .ticket-table-section .card, .analytics-section .card { background: transparent; padding: 0 !important; box-shadow: none !important;}
            .ticket-table-section .card-body, .analytics-section .card-body { padding: 0 !important; }
        }

        /* --- CSS PRINT --- */
        @media print {
            body { background: white !important; color: black !important; box-shadow: none !important; }
            .navbar, .search-section, .ticket-table-section, .btn, footer, .no-print { display: none !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .analytics-section { position: absolute; top: 0; left: 0; width: 100%; }
            .card { box-shadow: none !important; border: 1px solid #000 !important; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 py-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1">RBY <span style="color:#0dcaf0">Tech</span> Sdn Bhd</span>
        <div class="d-flex align-items-center">
            <span class="navbar-text text-light me-3 d-none d-md-block">Hello, <strong><?php echo $_SESSION['username']; ?></strong></span>
            <a href="print_qr.php" class="btn btn-outline-info btn-sm me-2">Print QR</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    
    <div class="row mb-4 align-items-end search-section">
        <div class="col-md-6"><h3 class="text-white">Trouble Tickets Overview</h3></div>
        <div class="col-md-6">
            <form action="" method="GET" class="d-flex shadow-sm rounded">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary px-4">Search</button>
                <?php if(isset($_GET['search'])): ?><a href="dashboard.php" class="btn btn-secondary ms-2">Reset</a><?php endif; ?>
            </form>
        </div>
    </div>

    <div class="row mb-5 ticket-table-section">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>PC Name & Location</th>
                                    <th style="width: 25%;">Issue</th>
                                    <th>Reporter (Contact)</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $search_query = "";
                                if (isset($_GET['search']) && !empty($_GET['search'])) {
                                    $search = $conn->real_escape_string($_GET['search']);
                                    $search_query = " AND (assets.pc_name LIKE '%$search%' OR tickets.description LIKE '%$search%' OR tickets.reporter_name LIKE '%$search%') ";
                                }

                                $sql = "SELECT tickets.id, tickets.asset_id, assets.pc_name, assets.location, tickets.description, 
                                               tickets.reporter_name, tickets.reporter_phone, tickets.reporter_email, tickets.status 
                                        FROM tickets JOIN assets ON tickets.asset_id = assets.id 
                                        WHERE 1=1 $search_query ORDER BY tickets.status ASC, tickets.created_at DESC";
                                
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        
                                        // LOGIC WARNA BADGE (UPDATED)
                                        $s = $row['status'];
                                        $badge = 'bg-secondary';
                                        
                                        if ($s == 'Open') { $badge = 'bg-danger'; }
                                        elseif ($s == 'In Progress') { $badge = 'bg-primary'; }
                                        elseif ($s == 'Level 2 (Technical Support)') { $badge = 'bg-primary border border-info border-2'; } 
                                        elseif ($s == 'Level 3 (Infrastructure/System Admin)') { $badge = 'bg-dark border border-danger border-2'; }
                                        elseif ($s == 'Under Warranty Claim') { $badge = 'bg-warning text-dark'; }
                                        elseif ($s == 'Pending Parts') { $badge = 'bg-secondary border border-dark'; }
                                        elseif ($s == 'Closed') { $badge = 'bg-success'; }

                                        $phone = !empty($row['reporter_phone']) ? $row['reporter_phone'] : '-';
                                        $email = !empty($row['reporter_email']) ? $row['reporter_email'] : '';

                                        echo "<tr>
                                                <td data-label='ID'><div>#{$row['id']}</div></td>
                                                <td data-label='PC Name & Location'>
                                                    <div>
                                                        <strong>{$row['pc_name']}</strong><br>
                                                        <small>{$row['location']}</small>
                                                    </div>
                                                </td>
                                                <td data-label='Issue'><div>{$row['description']}</div></td>
                                                <td data-label='Reporter (Contact)'>
                                                    <div>
                                                        <strong>{$row['reporter_name']}</strong><br>
                                                        <a href='tel:$phone' class='text-decoration-none small'>📞 $phone</a><br>
                                                        <small class='text-muted'>$email</small>
                                                    </div>
                                                </td>
                                                <td data-label='Status'><div><span class='badge rounded-pill $badge'>{$row['status']}</span></div></td>
                                                <td data-label='Actions'>
                                                    <div>
                                                        <a href='update_ticket.php?id={$row['id']}' class='btn btn-sm btn-primary mb-1'>Update</a>
                                                        <a href='history.php?pc_id={$row['asset_id']}' class='btn btn-sm btn-outline-dark mb-1'>History</a>
                                                    </div>
                                                </td>
                                              </tr>";
                                    }
                                } else { echo "<tr><td colspan='6' class='text-center'>No tickets found.</td></tr>"; }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5 analytics-section">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="print-header">
                        <h1 style="margin:0;">RBY TECH SDN BHD</h1>
                        <p>Level 12, Menara IT, Cyberjaya, Selangor | Tel: 03-8888 1234</p>
                        <hr><h3 style="margin-top:20px;">LAPORAN ANALISIS PERALATAN IT</h3><p>Tarikh: <?php echo date("d F Y"); ?></p>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                        <h4 class="text-danger fw-bold">⚠️ Frequent Offenders (Upgrade Analysis)</h4>
                        <button onclick="window.print()" class="btn btn-dark">🖨️ Print Full Report</button>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-light border-bottom">
                            <tr><th>PC Name</th><th>Total Tickets</th><th>Recommendation</th><th class="no-print">Action</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_stats = "SELECT assets.id, assets.pc_name, COUNT(tickets.id) as total_issues FROM tickets JOIN assets ON tickets.asset_id = assets.id GROUP BY assets.id HAVING total_issues > 0 ORDER BY total_issues DESC LIMIT 5";
                            $res_stats = $conn->query($sql_stats);
                            if ($res_stats && $res_stats->num_rows > 0) {
                                while($row = $res_stats->fetch_assoc()) {
                                    $rec = ($row['total_issues'] > 2) ? "High Risk - Consider Upgrade" : "Normal Monitor";
                                    $color = ($row['total_issues'] > 2) ? "text-danger fw-bold" : "text-success";
                                    echo "<tr>
                                            <td class='fw-bold' data-label='PC Name'>{$row['pc_name']}</td><td class='text-center' data-label='Total Tickets'>{$row['total_issues']}</td><td class='$color' data-label='Recommendation'>$rec</td>
                                            <td class='no-print text-center' data-label='Action'><a href='print_single.php?id={$row['id']}' target='_blank' class='btn btn-sm btn-outline-dark'>🖨️ Print</a></td>
                                          </tr>";
                                }
                            } else { echo "<tr><td colspan='4'>No data available.</td></tr>"; }
                            ?>
                        </tbody>
                    </table>
                    
                    <div class="print-header" style="border:none; margin-top:50px; text-align: left;">
                        <p>Disediakan oleh sistem:</p><br><br><p>__________________________<br> (IT Department Manager)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>