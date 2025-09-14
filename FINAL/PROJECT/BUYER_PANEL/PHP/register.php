<?php
// register.php
require_once "config.php";
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

  if ($name === '' || $email === '' || $password === '') {
    $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } else {
        // check existence
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
            $ins->bind_param("sss", $name, $email, $hash);
            if ($ins->execute()) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $message = "Registration failed: " . $ins->error;
            }
        }
        $stmt->close();
    }
}
include "main.php";
?>

<div class="card form-card">
  <h2>Create Account</h2>
  <?php if ($message): ?>
  <div class="mb-4 text-red-600"><?=htmlspecialchars($message)?></div>
  <?php endif; ?>
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
