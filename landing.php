<?php
session_start();
ob_start(); // Keeping this as your professor recommended

// Database connection - CHOOSE ONE OPTION:

// Option 1: If using default MySQL port (3306)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "finizer";

// Option 2: If you need port 3307 (uncomment below and comment above)
// $host = "127.0.0.1:3307";
// $user = "root";
// $pass = "";
// $dbname = "finizer";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?> 
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finizer - Landing page</title>
  <link rel="icon" type="image/png" href="tabicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="landing.css">
</head>

<body>
  <header>
    <div class="brand">
      <img src="name.png" alt="Finizer Logo">
    </div>
    <nav>
      <a href="#features">Features</a>
      <a href="#about">About Us</a>
      <a href="#contact">Contact</a>
    </nav>
  </header>

  <section class="hero fade-in">
    <h1>Welcome to Finizer</h1>
    <p>A smart personal finance assistant that helps you manage, plan, and grow your money smarter with AI-driven insights.</p>
    <p></p>
    <a href="#" class="btn open-modal">Get Started</a>
  </section>

  <h1 id="features">Features</h1>
  <section class="features">
    <div class="feature fade-in">
      <h3>💡 Smart Insights</h3>
      <p>Get personalized tips to improve your financial habits.</p>
      <img class="Fimg" src="Ft1.png">
    </div>
    <div class="feature fade-in">
      <h3>📊 Expense Tracking</h3>
      <p>Track your spending in real-time with easy visualization.</p>
      <img class="Fimg" src="Ft2.png">
    </div>
    <div class="feature fade-in">
      <h3>🎯 Goal Setting</h3>
      <p>Set financial goals and achieve them with guided planning.</p>
      <img class="Fimg" src="Ft3.png">
    </div>
    <div class="feature fade-in">
      <h3>🔒 Secure & Private</h3>
      <p>Your financial data is always secured and protected.</p>
      <img class="Fimg" src="Ft4.png">
    </div>
  </section>

  <section class="hero fade-in" id="about">
    <h1>About Us</h1>
    <p>Finizer is a personal finance assistant designed to help users organize their money with ease. It offers smart reminders, savings and bill tracking, and a clean dashboard that shows financial status at a glance. Whether you're a student, employee, or entrepreneur, Finizer simplifies budgeting and reduces financial stress through intuitive tools and secure cloud storage.</p>
    <p></p>
  </section>

  <h1>Meet the Creators</h1>
  <section class="creators">
    <div class="creator fade-in">
      <img class="Crtri" src="nash.png">
      <h2>Nash Jermaine Clemente</h2>
      <p>Responsible for coding system design and interface and leads the document formatting.</p>
    </div>
    <div class="creator fade-in">
      <img class="Crtri" src="nhia.png">
      <h2>Nhia Leecole Solis</h2>
      <p>Serves as the project overseer, coordinating team activities and assigning tasks to ensure balanced workload distribution.</p>
    </div>
    <div class="creator fade-in">
      <img class="Crtri" src="jordan.png">
      <h2>Jordan Alcade Sia</h2>
      <p class="descr">Leads the main database coding efforts and assists in drafting technical documentation.</p>
    </div>
  </section>

  <h1 id="contact">Contact Us</h1>
  <section class="features">
    <div class="feature fade-in">
      <h3>✉️ Email Support</h3>
      <p>Reach us directly for inquiries:</p>
        <a href="mailto:finizer.team@gmail.com">finizer.team@gmail.com</a>
    </div>
    <div class="feature fade-in">
      <h3>📱 Social Media</h3>
      <p>Follow us here:</p>
      <a href="https://www.facebook.com/jermaynehhh/" target="_blank"><i class="fab fa-facebook"></i></a>
      <a href="https://www.instagram.com/jermaynehhh/" target="_blank"><i class="fab fa-instagram"></i></a>
      <a href="https://x.com/jermaynehhh" target="_blank"><i class="fab fa-x-twitter"></i></a>
    </div>
    <div class="feature fade-in">
      <h3>📍 Location</h3>
      <p>Colegio San Agustin-Biñan, Laguna, Philippines</p>
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
      <?php 
      if (isset($_POST["login"])) {
    $email = $conn->real_escape_string($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM usertable WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
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
}
      ?>
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
      <?php 
      if (isset($_POST["signup"])) {
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
      ?>
    </div>
  </div>

  <!-- TERMS MODAL -->
  <div class="bg-modal-terms" id="termsModal">
    <div class="modal-content terms-modal">
      <div class="close" id="closeTerms">&times;</div>
      <h2>Terms and Conditions</h2>
      <div class="terms-text" style="text-align:left; margin-top:15px;">
        <p><strong>Effective Date: </strong>October 4, 2025</p>

        <h3>1. Acceptance of Terms</h3>
        <p>
          <i>By creating an account or using Finizer, you agree to be bound by these Terms and Conditions, as well as our Privacy Policy. If you do not agree, you must discontinue use immediately.</i>
        </p>

        <h3>2. Use of Services</h3>
        <p>
          • Finizer provides tools for financial tracking and insights.<br>
          • You agree to use the service only for lawful purposes.<br>
          • You are responsible for maintaining the confidentiality of your account credentials.<br>
          • You may not use Finizer to engage in fraudulent, unlawful, or abusive activities.
        </p>

        <h3>3. Data and Privacy</h3>
        <p>
          • Finizer collects and stores data you provide for the purpose of delivering financial insights.<br>
          • Your data will be stored securely and will not be sold to third parties.<br>
          • We may analyze anonymized data to improve our services.<br>
          • For details, see our Privacy Policy.
        </p>

        <h3>4. Account Responsibilities</h3>
        <p>
          • You must provide accurate information when creating an account.<br>
          • You are responsible for all activity that occurs under your account.<br>
          • Notify us immediately if you suspect unauthorized use of your account.
        </p>

        <h3>5. Updates and Modifications</h3>
        <p>
          We reserve the right to update, change, or modify these Terms at any time. Continued use of Finizer after changes means you accept the updated Terms.
        </p>

        <h3>6. Limitation of Liability</h3>
        <p>
          Finizer provides financial insights and tools but does not guarantee accuracy, completeness, or fitness for any specific purpose. We are not responsible for losses, damages, or decisions made based on the use of our services.
        </p>

        <h3>7. Termination</h3>
        <p>
          We may suspend or terminate your account if you violate these Terms. You may discontinue use at any time by deleting your account.
        </p>

        <h3>8. Governing Law</h3>
        <p>
          These Terms shall be governed by and interpreted according to the laws of [Insert Country/Region].
        </p>

        <h3>9. Contact Us</h3>
        <p>
          For questions or concerns about these Terms, contact us at: <strong>Email:</strong> support@finizer.com
        </p>
      </div>
    </div>
  </div>

  <footer class="footer">
    © 2025 Finizer. All rights reserved.
  </footer>

<script src="landing.js"></script>
</body>

</html>