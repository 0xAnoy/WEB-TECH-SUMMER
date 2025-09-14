<?php

require_once "config.php";
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = "Email and password required.";
    } else {
  $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
    if ($stmt->num_rows === 1) {
      $stmt->bind_result($id, $name, $hash);
      $stmt->fetch();
      if (password_verify($password, $hash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $name;
        $remember = !empty($_POST['remember']);
        if ($remember) {
            // Simple 30-day cookies
            setcookie('remember_user_id', $id, time()+86400*30, '/');
            setcookie('remember_username', $name, time()+86400*30, '/');
            setcookie('remember_email', $email, time()+86400*30, '/');
        } else {
            // Clear if previously set
            setcookie('remember_user_id', '', time()-3600, '/');
            setcookie('remember_username', '', time()-3600, '/');
            setcookie('remember_email', '', time()-3600, '/');
        }
        header("Location: profile.php");
        exit;
      } else {
        $message = "Invalid credentials.";
      }
        } else {
            $message = "No account with that email.";
        }
        $stmt->close();
    }
}
include "main.php";
?>

<div class="card form-card">
  <h2>Login</h2>
  <?php if (isset($_GET['registered'])): ?>
  <div class="mb-4 text-green-600">Registration successful. Please login.</div>
  <?php endif; ?>
  <?php if ($message): ?>
  <div class="mb-4 text-red-600"><?=htmlspecialchars($message)?></div>
  <?php endif; ?>
  <form method="post" autocomplete="off">
    <input name="email" placeholder="Email" type="email" class="form-input" value="<?= isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : '' ?>">
    <input name="password" placeholder="Password" type="password" class="form-input">
    <label class="remember-option">
      <input type="checkbox" name="remember" value="1" class="remember-checkbox"> Remember me
    </label>
    <div class="form-actions"><button class="btn btn-primary btn-block">Login</button></div>
  </form>
  <p class="mt-3 text-sm">Don't have an account? <a href="register.php" class="text-blue-600">Register</a></p>
</div>

</main>

<?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>
