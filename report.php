<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Issue - RBY Tech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=1740&q=80') no-repeat center center fixed;
            background-size: cover;
            box-shadow: inset 0 0 0 2000px rgba(0, 0, 0, 0.85);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .report-card {
            background-color: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            color: #333;
            width: 100%;
            max-width: 500px;
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #000000, #1a1a1a);
            color: #fff;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #0dcaf0;
        }
        .form-control {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            padding: 12px;
            border-radius: 10px;
        }
        .btn-submit {
            background-color: #0d6efd;
            border: none;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
        }
        .contact-info-section {
            background-color: #e3f2fd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="report-card">
    <div class="card-header-custom">
        <h4 style="font-weight:700;">RBY Tech Sdn Bhd</h4>
        <span style="color:#0dcaf0; font-size:0.85rem;">IT Helpdesk Reporting System</span>
    </div>
    
    <div class="card-body p-4">
        
        <?php
        $pc_code = isset($_GET['pc_id']) ? $_GET['pc_id'] : '';
        $sql = "SELECT * FROM assets WHERE qr_code_string = '$pc_code'";
        $result = $conn->query($sql);
        $pc_info = $result->fetch_assoc();

        if ($pc_info) {
            ?>
            <div class="alert alert-primary d-flex align-items-center mb-3">
                <i class="bi bi-pc-display me-2 fs-4"></i>
                <div>
                    <strong><?php echo $pc_info['pc_name']; ?></strong><br>
                    <small><?php echo $pc_info['location']; ?></small>
                </div>
            </div>

            <form action="submit_action.php" method="POST">
                <input type="hidden" name="asset_id" value="<?php echo $pc_info['id']; ?>">
                
                <div class="mb-3">
                    <label class="fw-bold">Your Name</label>
                    <input type="text" name="reporter_name" class="form-control" placeholder="e.g. Ali (HR)" required>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="fw-bold">Phone No.</label>
                        <input type="text" name="reporter_phone" class="form-control" placeholder="012-345xxxx" required>
                    </div>
                    <div class="col-6">
                        <label class="fw-bold">Email</label>
                        <input type="email" name="reporter_email" class="form-control" placeholder="ali@gmail.com">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="fw-bold">Describe the Issue</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Explain what happened..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-submit">Submit Report</button>
            </form>

            <hr class="my-4">
            
            <div class="contact-info-section text-center">
                <p class="mb-1 fw-bold text-dark">Need Urgent Help?</p>
                <p class="mb-0">📞 IT Hotline: <a href="tel:03-78788787" class="text-decoration-none">03-7878 8787</a></p>
                <p class="mb-0">📧 Support: <a href="mailto:supportrbyt@gmail.com" class="text-decoration-none">supportrbyt@gmail.com</a></p>
            </div>

            <?php
        } else {
            echo "<div class='text-center py-5 text-danger'><h4>⚠️ Invalid QR Code</h4></div>";
        }
        ?>

    </div>
</div>

</body>
</html>