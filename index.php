<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Wilderness Role Select</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
                  url('bggg.avif');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .role-container {
      background: rgba(255, 255, 255, 0.92);
      padding: 2.5rem;
      border-radius: 16px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
      text-align: center;
    }

    .role-container h1 {
      font-size: 2rem;
      font-weight: 600;
      color: #2e7d32;
      margin-bottom: 1.5rem;
    }

    .btn-hiking {
      background: linear-gradient(135deg, #43a047, #2e7d32);
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 0.75rem;
      font-weight: 600;
      transition: 0.3s ease;
    }

    .btn-hiking:hover {
      background: linear-gradient(135deg, #388e3c, #1b5e20);
      transform: scale(1.03);
    }

    .footer-text {
      font-size: 0.9rem;
      color: #444;
      margin-top: 1.5rem;
    }

    .footer-text span {
      color: #2e7d32;
      font-weight: 500;
    }

    @media (max-width: 576px) {
      .role-container {
        padding: 2rem;
      }
    }
  </style>
</head>

<body>
  <main class="role-container" role="main" aria-label="Select Role">
    <h1>Explore WilderPath</h1>

    <div class="mb-3">
      <a href="src/adminlogin.html" class="btn btn-hiking w-100">Login as Trail Admin</a>
    </div>
    <div class="mb-3">
      <a href="src/login.html" class="btn btn-hiking w-100">Login as Hiker</a>
    </div>

    <p class="footer-text">
      Powered by <span>Wilderness Trail System</span>
    </p>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
</body>
</html>
