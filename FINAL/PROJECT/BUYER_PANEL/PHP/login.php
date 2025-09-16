<?php

require_once "config.php";
session_start();

$invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email)) {
    echo "<p class='form-error'>Email is required!</p>";
    $invalid = true;
  }
  if (empty($password)) {
    echo "<p class='form-error'>Password is required!</p>";
    $invalid = true;
  }

  if (!$invalid) {
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
          setcookie('remember_user_id', $id, time()+86400*30, '/');
          setcookie('remember_username', $name, time()+86400*30, '/');
          setcookie('remember_email', $email, time()+86400*30, '/');
        } else {
          setcookie('remember_user_id', '', time()-3600, '/');
          setcookie('remember_username', '', time()-3600, '/');
          setcookie('remember_email', '', time()-3600, '/');
        }
        header("Location: profile.php");
        exit;
      } else {
        echo "<p class='form-error'>Invalid credentials.</p>";
        $invalid = true;
      }
    } else {
      echo "<p class='form-error'>No account with that email.</p>";
      $invalid = true;
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
  <!-- Legacy style errors already echoed above if any -->
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
