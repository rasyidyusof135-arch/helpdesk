<?php 
session_start();
include 'db_connect.php';

// GATEKEEPER
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Dapatkan Data Tiket
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$sql = "SELECT tickets.*, assets.pc_name, assets.location 
        FROM tickets 
        JOIN assets ON tickets.asset_id = assets.id 
        WHERE tickets.id = $id";
$result = $conn->query($sql);
$ticket = $result->fetch_assoc();

if (!$ticket) {
    echo "Ticket not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Ticket #<?php echo $ticket['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .card { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: none; border-radius: 15px; }
        .header-blue { 
            background: linear-gradient(135deg, #0d6efd, #0a58ca); 
            color: white; 
            padding: 20px; 
            border-radius: 15px 15px 0 0; 
        }
        @media (max-width: 576px) {
            .container { padding-left: 10px; padding-right: 10px; }
            .header-blue { padding: 15px; }
            .header-blue h4 { font-size: 1.1rem; }
            .card-body { padding: 1.25rem !important; }
        }
    </style>
</head>
<body>

<div class="container mt-3 mt-md-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            
            <div class="card">
                <div class="header-blue d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">Update Ticket #<?php echo $ticket['id']; ?></h4>
                        <small>RBY Tech IT Helpdesk System</small>
                    </div>
                    <a href="dashboard.php" class="btn btn-sm btn-light text-primary fw-bold px-3">Back</a>
                </div>
                
                <div class="card-body p-4">
                    <div class="alert alert-secondary border-0 bg-light p-3 rounded-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Asset Name:</strong> <?php echo $ticket['pc_name']; ?></p>
                                <p class="mb-1"><strong>Location:</strong> <?php echo $ticket['location']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Reporter:</strong> <?php echo $ticket['reporter_name']; ?></p>
                                <p class="mb-1"><strong>Current Status:</strong> <span class="badge bg-dark"><?php echo $ticket['status']; ?></span></p>
                            </div>
                        </div>
                        <hr>
                        <p class="mb-0 text-muted"><strong>Issue Description:</strong><br> "<?php echo $ticket['description']; ?>"</p>
                    </div>

                    <form action="update_action.php" method="POST">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Change Status</label>
                            <select name="status" id="statusSelect" class="form-select form-select-lg" onchange="checkStatusRequirement()">
                                <option value="Open" <?php if($ticket['status']=='Open') echo 'selected'; ?>>Open (Baru)</option>
                                
                                <option disabled>--- ACTIONS ---</option>
                                <option value="In Progress" <?php if($ticket['status']=='In Progress') echo 'selected'; ?>>In Progress (Sedang Dibaiki)</option>

                                <option disabled>--- ESCALATION (TIER SUPPORT) ---</option>
                                <option value="Level 2 (Technical Support)" <?php if($ticket['status']=='Level 2 (Technical Support)') echo 'selected'; ?>>Level 2 (Technical Support)</option>
                                <option value="Level 3 (Infrastructure/System Admin)" <?php if($ticket['status']=='Level 3 (Infrastructure/System Admin)') echo 'selected'; ?>>Level 3 (Infrastructure/System Admin)</option>

                                <option disabled>--- EXTERNAL / PENDING ---</option>
                                <option value="Under Warranty Claim" <?php if($ticket['status']=='Under Warranty Claim') echo 'selected'; ?>>Under Warranty Claim (Vendor/RMA)</option>
                                <option value="Pending Parts" <?php if($ticket['status']=='Pending Parts') echo 'selected'; ?>>Pending Parts (Tunggu Barang)</option>
                                
                                <option disabled>--- COMPLETION ---</option>
                                <option value="Closed" <?php if($ticket['status']=='Closed') echo 'selected'; ?>>Closed (Selesai)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold" id="remarksLabel">Technician Remarks</label>
                            
                            <textarea name="admin_remarks" id="remarksField" class="form-control" rows="5"><?php echo $ticket['admin_remarks']; ?></textarea>
                            
                            <div class="form-text mt-2" id="remarksHelp">
                                <i class="bi bi-info-circle"></i> Sila masukkan catatan progres kerja.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm fw-bold">Update Ticket Status</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() { checkStatusRequirement(); };

    function checkStatusRequirement() {
        var status = document.getElementById("statusSelect").value;
        var label = document.getElementById("remarksLabel");
        var field = document.getElementById("remarksField");
        var help = document.getElementById("remarksHelp");

        // Reset Default Style
        field.style.borderColor = "#ced4da"; 
        field.required = false;

        if (status === "Level 2 (Technical Support)") {
            label.innerHTML = "🛠️ Basic Troubleshooting Log (Wajib Isi)";
            label.className = "form-label fw-bold text-primary";
            field.placeholder = "Nyatakan langkah troubleshooting asas yang telah anda cuba (contoh: Restart, Check Cable, Update Driver) sebelum menyerahkan kepada Level 2.";
            field.required = true;
            help.innerHTML = "L2 Engineer memerlukan info ini untuk mengelakkan kerja berulang.";
            
        } else if (status === "Level 3 (Infrastructure/System Admin)") {
            label.innerHTML = "🚨 Server/Network Error Logs (Wajib Isi)";
            label.className = "form-label fw-bold text-danger";
            field.placeholder = "Adakah isu ini kritikal? Sila sertakan Error Code, IP Address yang terlibat, atau log firewall.";
            field.required = true;
            help.innerHTML = "Escalation ke L3 hanya untuk isu kritikal infrastruktur.";

        } else if (status === "Under Warranty Claim") {
            label.innerHTML = "📦 RMA Info / Courier Tracking";
            label.className = "form-label fw-bold text-warning";
            field.placeholder = "Contoh: Sent to Dell Service Center. RMA No: MY-888999. Courier: GDEX.";
            field.required = true;
            help.innerHTML = "Masukkan butiran penghantaran aset.";

        } else if (status === "Pending Parts") {
            label.innerHTML = "📋 Senarai Barang Yang Perlu Dipesan";
            label.className = "form-label fw-bold text-dark";
            field.placeholder = "Contoh: 1. SSD 500GB Samsung EVO - 1 Unit\n2. RAM 8GB DDR4 - 1 Unit";
            field.required = true;
            help.innerHTML = "Senarai ini akan digunakan untuk Procurement.";

        } else if (status === "Closed") {
            label.innerHTML = "✅ Solution / Tindakan Penyelesaian";
            label.className = "form-label fw-bold text-success";
            field.placeholder = "Contoh: Replaced HDD with SSD. Windows reinstalled. Issue resolved.";
            field.required = true;
            help.innerHTML = "Rekod penyelesaian penting untuk rujukan masa depan.";

        } else {
            // Default (Open / In Progress)
            label.innerHTML = "Technician Remarks / Progress Notes";
            label.className = "form-label fw-bold";
            field.placeholder = "Catatan semasa...";
            help.innerHTML = "Catatan tambahan untuk rujukan.";
        }
    }
</script>

</body>
</html>