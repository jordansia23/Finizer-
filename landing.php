<?php
/* $host = "localhost";
$user = "root";
$pass = ""; // default blank sa XAMPP
$dbname = "finizer";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
    $username = $conn->real_escape_string($_POST["username"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usertable (username, email, password) VALUES ('$username', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Sign up successful! You can now log in.');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $conn->real_escape_string($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM usertable WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            session_start();
            $_SESSION["uid"] = $row["uid"];
            $_SESSION["username"] = $row["username"];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email.');</script>";
    }
} */
?> 
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finizer - Smart Personal Finance Assistant</title>
  <link rel="icon" type="image/png" href="tabicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="landing.css">
</head>

<body>
  <header>
    <div class="brand">
      <img src="name.png" alt="Finizer Logo">
    </div>
    <nav>
      <a href="#">Features</a>
      <a href="#">About</a>
      <a href="#">Contact</a>
    </nav>
  </header>

  <section class="hero fade-in">
    <h1>Welcome to Finizer</h1>
    <p>A smart personal finance assistant that helps you manage, plan, and grow your money smarter with AI-driven insights.</p>
    <a href="#" class="btn open-modal">Get Started</a>
  </section>

  <section class="features">
    <div class="feature fade-in">
      <h3>💡 Smart Insights</h3>
      <p>Get personalized tips to improve your financial habits.</p>
    </div>
    <div class="feature fade-in">
      <h3>📊 Expense Tracking</h3>
      <p>Track your spending in real-time with easy visualization.</p>
    </div>
    <div class="feature fade-in">
      <h3>🎯 Goal Setting</h3>
      <p>Set financial goals and achieve them with guided planning.</p>
    </div>
    <div class="feature fade-in">
      <h3>🔒 Secure & Private</h3>
      <p>Your financial data is always safe and protected.</p>
    </div>
  </section>

  <div class="bg-modal">
    <div class="modal-content">
      <div class="close">&times;</div>
      <img src="finizer name.png" alt="Finizer Logo">
      <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <p class="signup-text">
          Don't have an account yet?
          <span class="signup-link" onclick="openSignupModal()">Sign up</span>
        </p>
        <button type="submit" name="login" class="button">Login</button>
      </form>
    </div>
  </div>

  <div class="bg-modal-signup">
    <div class="modal-content">
      <div class="close">&times;</div>
      <img src="finizer name.png" alt="Finizer Logo">
      <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <label class="terms-label">
          <input type="checkbox" name="terms" required>
          <span>I agree with the <span class="terms-link" onclick="openTermsModal()">Terms and Conditions</span></span>
        </label>

        <p class="signup-text">
          Already have an account?
          <span class="signup-link" onclick="backToLogin()">Log in</span>
        </p>
        <button type="submit" name="signup" class="button">Sign Up</button>
      </form>
    </div>
  </div>

  <div class="bg-modal-terms">
    <div class="modal-content terms-modal">
      <div class="close">&times;</div>
      <h2>Terms and Conditions</h2>
      <p>
        By using Finizer, you agree to our terms.  
        Your data will be stored securely and used only for providing financial insights.  
        Do not share your account credentials.  
        We reserve the right to update these terms anytime.
      </p>
    </div>
  </div>

  <footer>
    © 2025 Finizer. All rights reserved.
  </footer>

  <script src="script.js"></script>
</body>

</html>
