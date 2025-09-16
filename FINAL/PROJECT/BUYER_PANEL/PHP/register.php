<?php
require_once "config.php";
session_start();

$invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($name)) {
    echo "<p class='form-error'>Username is required!</p>";
    $invalid = true;
  }
  if (empty($email)) {
    echo "<p class='form-error'>Email is required!</p>";
    $invalid = true;
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<p class='form-error'>Invalid email address!</p>";
    $invalid = true;
  }
  if (empty($password)) {
    echo "<p class='form-error'>Password is required!</p>";
    $invalid = true;
  }

  if (!$invalid) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      echo "<p class='form-error'>Email already registered!</p>"; $invalid = true;
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $ins = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
      $ins->bind_param("sss", $name, $email, $hash);
      if ($ins->execute()) {
        header("Location: login.php?registered=1");
        exit;
      } else {
        echo "<p class='form-error'>Registration failed.</p>";
        $invalid = true;
      }
    }
    $stmt->close();
  }
}
include "main.php";
?>

<div class="card form-card">
  <h2>Create Account</h2>

  <form method="post" autocomplete="off">
    <input name="username" placeholder="Username" class="form-input">
    <input name="email" placeholder="Email" type="email" class="form-input">
    <input name="password" placeholder="Password" type="password" class="form-input">
    <div class="form-actions"><button class="btn btn-primary btn-block">Register</button></div>
  </form>
  <p class="mt-3 text-sm">Already have account? <a href="login.php" class="text-blue-600">Login</a></p>
</div>

</main>

<?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>
