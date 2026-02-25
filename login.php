<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Login - RBY Tech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* Gambar Background sama dengan report.php untuk konsistensi */
            background: url('https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=1740&q=80') no-repeat center center fixed;
            background-size: cover;
            box-shadow: inset 0 0 0 2000px rgba(0, 0, 0, 0.8);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 4px 10px rgba(0,0,0,0.5);
            margin-bottom: 5px;
        }
        .brand-subtitle {
            color: #0dcaf0; /* Cyan */
            letter-spacing: 2px;
            font-weight: 300;
            margin-bottom: 40px;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.1); /* Glassmorphism (Kaca Gelap) */
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            height: 50px;
            border-radius: 8px;
        }

        .btn-login {
            background: #0dcaf0;
            color: #000;
            font-weight: 700;
            height: 50px;
            border-radius: 8px;
            border: none;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #0aa2c0;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="text-center">
        <div class="brand-title">RBY Tech Sdn Bhd</div>
        <div class="brand-subtitle">INTERNAL IT SYSTEM</div>
    </div>

    <div class="login-card">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center p-2 mb-3">
                <small><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></small>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <div class="mb-3">
                <label class="form-label text-white-50">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Admin ID" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-white-50">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-login w-100">ACCESS DASHBOARD</button>
        </form>
    </div>

    <div class="mt-4 text-white-50">
        <small>Restricted Area. Authorized Personnel Only.</small>
    </div>

</body>
</html>